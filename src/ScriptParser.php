<?php

namespace ProtoneMedia\SpladeCore;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Peast\Formatter\PrettyPrint;
use Peast\Peast;
use Peast\Syntax\Node\ArrayExpression;
use Peast\Syntax\Node\ArrayPattern;
use Peast\Syntax\Node\CallExpression;
use Peast\Syntax\Node\FunctionDeclaration;
use Peast\Syntax\Node\Identifier;
use Peast\Syntax\Node\ImportDeclaration;
use Peast\Syntax\Node\ObjectExpression;
use Peast\Syntax\Node\ObjectPattern;
use Peast\Syntax\Node\Program;
use Peast\Syntax\Node\Property;
use Peast\Syntax\Node\StringLiteral;
use Peast\Syntax\Node\VariableDeclaration;

class ScriptParser
{
    protected Program $rootNode;

    protected string $script;

    protected array $vueFunctions = [
        'computed',
        'inject',
        'nextTick',
        'onActivated',
        'onBeforeMount',
        'onBeforeUnmount',
        'onBeforeUpdate',
        'onDeactivated',
        'onErrorCaptured',
        'onMounted',
        'onUnmounted',
        'onUpdated',
        'provide',
        'reactive',
        'readonly',
        'ref',
        'watch',
        'watchEffect',
        'watchPostEffect',
        'watchSyncEffect',
    ];

    public function __construct(string $script)
    {
        $this->script = $script;

        $this->rootNode = Peast::latest($this->script, [
            'sourceType' => \Peast\Peast::SOURCE_TYPE_MODULE,
        ])->parse();
    }

    /**
     * Returns all Vue functions that are used in the script.
     */
    public function getVueFunctions(): Collection
    {
        $functions = Collection::make();

        foreach ($this->rootNode->query('CallExpression[callee.name=/^('.implode('|', $this->vueFunctions).')$/]') as $node) {
            $functions->push($node->getCallee()->getName());
        }

        return $functions->values();
    }

    /**
     * Maps the props array to a Vue props object.
     */
    private function toPropsObjectDefinition(array|Collection $props): string
    {
        return Collection::make($props)
            ->map(function (string $value, string $key) {
                if (Str::camel($key) !== $key) {
                    $key = "'{$key}'";
                }

                if (! $value) {
                    return "{$key}: {}";
                }

                $value = trim(str_replace(["\n", "\t"], '', $value));

                return "{$key}: {$value}";
            })
            ->implode(', ');
    }

    /**
     * Returns the defineProps() call expression and additionally merges
     * the given props with the ones that are defined in the script.
     */
    public function getDefineProps(array $mergeWith = []): DefineVueProps
    {
        $defineVueProps = new DefineVueProps;

        foreach ($this->rootNode->query('CallExpression[callee.name="defineProps"]') as $node) {
            /** @var CallExpression $node */
            $definePropsScript = collect(explode(PHP_EOL, $this->script))
                ->filter(function (string $contents, int $line) use ($node) {
                    return $line >= ($node->getLocation()->getStart()->getLine() - 1)
                        && $line <= ($node->getLocation()->getEnd()->getLine() - 1);
                })
                ->implode(PHP_EOL);

            $defineVueProps->setOriginalScript($definePropsScript);

            $firstArgument = $node->getArguments()[0] ?? null;
            $newPropsObject = '{}';

            if ($firstArgument instanceof ArrayExpression) {
                $props = collect($firstArgument->getElements())
                    ->map(fn (StringLiteral $element) => $element->getValue())
                    ->mapWithKeys(function (string $prop) use ($defineVueProps) {
                        $defineVueProps->addPropKey($prop);

                        return [$prop => ''];
                    })
                    ->merge($mergeWith)
                    ->pipe(fn (Collection $props) => $this->toPropsObjectDefinition($props));

                $newPropsObject = "{{$props}}";
            } elseif ($firstArgument instanceof ObjectExpression) {
                $props = collect($firstArgument->getProperties())
                    ->mapWithKeys(function (Property $property) use ($defineVueProps) {
                        $key = $property->getKey()->getName();

                        $defineVueProps->addPropKey($key);

                        if ($property->getValue() instanceof Identifier) {
                            return [$key => $property->getValue()->getName()];
                        }

                        if ($property->getValue() instanceof ObjectExpression) {
                            $rendered = Str::after($property->render(new PrettyPrint()), "{$key}: ");

                            return [$key => trim($rendered)];
                        }
                    });

                $props = Collection::make($mergeWith)
                    ->merge($props)
                    ->pipe(fn (Collection $props) => $this->toPropsObjectDefinition($props));

                $newPropsObject = "{{$props}}";
            }

            foreach (array_keys($mergeWith) as $key) {
                $defineVueProps->addPropKey($key);
            }

            return $defineVueProps->setNewProjectObject($newPropsObject);
        }

        foreach (array_keys($mergeWith) as $key) {
            $defineVueProps->addPropKey($key);
        }

        return $defineVueProps
            ->setOriginalScript('')
            ->setNewProjectObject(
                '{'.$this->toPropsObjectDefinition($mergeWith).'}'
            );

    }

    /**
     * Returns a Collection of all variables and functions that are defined in the script.
     */
    public function getVariables(Collection|array $additionalItems = []): Collection
    {
        $variables = Collection::make();
        $nodes = Collection::make();

        $add = fn (Identifier $node) => $variables->push($node->getName()) && $nodes->push($node);

        foreach ($this->rootNode->getBody() as $node) {
            if ($node instanceof VariableDeclaration) {
                foreach ($node->getDeclarations() as $declaration) {
                    $id = $declaration->getId();

                    if ($id instanceof Identifier) {
                        $add($id);
                    } elseif ($id instanceof ObjectPattern) {
                        foreach ($id->getProperties() as $property) {
                            $add($property->getKey());
                        }
                    } elseif ($id instanceof ArrayPattern) {
                        foreach ($id->getElements() as $element) {
                            $add($element);
                        }
                    }
                }
            }

            if ($node instanceof FunctionDeclaration) {
                $add($node->getId());
            }
        }

        return $variables->merge($additionalItems)->unique()->sort()->values();
    }

    /**
     * Returns an array of all imports.
     *
     * @return Collection<ImportedVueComponent>
     */
    public function getImports(): Collection
    {
        $imports = [];

        // find ImportDeclaration
        foreach ($this->rootNode->query('ImportDeclaration') as $node) {
            /** @var ImportDeclaration $node */
            $source = $node->getSource();

            if (! $source instanceof StringLiteral) {
                continue;
            }

            // find ImportSpecifier
            foreach ($node->getSpecifiers() as $specifier) {
                /** @var ImportSpecifier $specifier */
                $imports[] = new ImportedVueComponent(
                    $specifier->getLocal()->getName(),
                    $source->getValue(),
                    $source->getFormat() === StringLiteral::DOUBLE_QUOTED
                );
            }
        }

        return new Collection($imports);
    }
}
