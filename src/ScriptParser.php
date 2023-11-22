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
use Peast\Syntax\Node\ObjectExpression;
use Peast\Syntax\Node\ObjectPattern;
use Peast\Syntax\Node\Program;
use Peast\Syntax\Node\Property;
use Peast\Syntax\Node\StringLiteral;
use Peast\Syntax\Node\VariableDeclaration;

class ScriptParser
{
    protected Program $rootNode;

    protected string $scriptWithoutImports;

    protected array $vueFunctions = [
        'computed',
        'inject',
        'nextTick',
        'onMounted',
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
        $this->scriptWithoutImports = trim($this->removeImports($script));

        $this->rootNode = Peast::latest($this->scriptWithoutImports)->parse();
    }

    /**
     * Removes the import statements from the script.
     */
    protected function removeImports(string $script): string
    {
        return Collection::make(explode(PHP_EOL, $script))
            ->filter(fn ($line) => ! str_starts_with(trim($line), 'import '))
            ->implode(PHP_EOL);
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
     * Returns the defineProps() call expression and additionally merges
     * the given props with the ones that are defined in the script.
     */
    public function getDefineProps(array $mergeWith = []): array
    {
        foreach ($this->rootNode->query('CallExpression[callee.name="defineProps"]') as $node) {
            /** @var CallExpression $node */
            $definePropsScript = collect(explode(PHP_EOL, $this->scriptWithoutImports))
                ->filter(function (string $contents, int $line) use ($node) {
                    return $line >= ($node->getLocation()->getStart()->getLine() - 1)
                        && $line <= ($node->getLocation()->getEnd()->getLine() - 1);
                })
                ->implode(PHP_EOL);

            $firstArgument = $node->getArguments()[0] ?? null;
            $newFirstArgument = '';

            if ($firstArgument instanceof ArrayExpression) {
                $props = collect($firstArgument->getElements())
                    ->map(fn (StringLiteral $element) => $element->getValue())
                    ->values()
                    ->merge(array_keys($mergeWith))
                    ->unique()
                    ->map(fn (string $prop) => "'{$prop}'")
                    ->implode(', ');

                $newFirstArgument = "[{$props}]";
            } elseif ($firstArgument instanceof ObjectExpression) {
                $props = collect($firstArgument->getProperties())
                    ->mapWithKeys(function (Property $property) {
                        $key = $property->getKey()->getName();

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
                    ->map(function (string $value, string $key) {
                        $value = trim(str_replace(["\n", "\t"], '', $value));

                        return "{$key}: {$value}";
                    })
                    ->values()
                    ->implode(', ');

                $newFirstArgument = "{{$props}}";
            }

            return [
                'original' => trim($definePropsScript),
                'new' => "const props = defineProps({$newFirstArgument});",
            ];
        }

        $keys = Collection::make($mergeWith)->keys()->map(
            fn (string $prop) => "'{$prop}'"
        )->implode(',');

        return [
            'original' => '',
            'new' => 'const props = defineProps(['.$keys.']);',
        ];
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
}
