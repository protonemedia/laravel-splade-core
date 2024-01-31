<?php

namespace ProtoneMedia\SpladeCore;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use InvalidArgumentException;
use ProtoneMedia\SpladeCore\Facades\SpladePlugin;

class BladeViewExtractor
{
    protected readonly string $originalScript;

    protected array $viewRootLayoutTags = [];

    protected string $viewWithoutScriptTag;

    protected ComponentHelper $componentHelper;

    protected ScriptParser $scriptParser;

    protected ?array $importedComponents = null;

    public function __construct(
        protected readonly string $originalView,
        protected readonly array $data,
        protected readonly string $bladePath
    ) {
        if (! Str::endsWith($bladePath, '.blade.php')) {
            throw new InvalidArgumentException("The Blade Path must end with '.blade.php'.");
        }

        $this->componentHelper = app(ComponentHelper::class);
    }

    /**
     * Helper method to create a new instance.
     */
    public static function from(string $originalView, array $data, string $bladePath): self
    {
        return new static($originalView, $data, $bladePath);
    }

    /**
     * Check if the view is a Blade Component.
     */
    protected function isComponent(): bool
    {
        return array_key_exists('spladeBridge', $this->data)
            && Str::contains($this->bladePath, '/components/');
    }

    /**
     * Returns a RenderViewAsVueComponent instance for the view.
     */
    public function getViewAsVueRenderer(): RenderViewAsVueComponent
    {
        $this->splitOriginalView();

        return RenderViewAsVueComponent::from($this->viewWithoutScriptTag, $this->bladePath, $this->viewRootLayoutTags);
    }

    /**
     * Check if the view has a <script setup> tag.
     */
    public function hasScriptSetup(): bool
    {
        return str_contains($this->originalView, '<script setup>');
    }

    /**
     * Check if the view is wrapped in a root layout.
     */
    protected function extractWrappedViewInRootLayout(): void
    {
        $originalView = trim($this->originalView);

        if (! Str::startsWith($originalView, '<x-')) {
            return;
        }

        // find last closing html tag
        $endTag = trim(Arr::last(explode(PHP_EOL, $originalView)));

        if (! (Str::startsWith($endTag, '</') && Str::endsWith($endTag, '>'))) {
            return;
        }

        $tag = Str::between($endTag, '</', '>');

        preg_match_all('/(<'.$tag.'.*?>)(.*?)(<\/'.$tag.'>)/s', $originalView, $matches);

        $this->viewRootLayoutTags = [
            $matches[1][0], // <x-layout {{ $attributes }}>
            $matches[3][0], // </x-layout>
        ];
    }

    /**
     * Handle the extraction of the Vue script. Returns the view without the <script setup> tag.
     */
    public function handle(Filesystem $filesystem): string
    {
        if (! $this->hasScriptSetup()) {
            // The view does not contain a <script setup> tag, so we don't need to do anything.
            return $this->originalView;
        }

        $this->splitOriginalView();
        $this->scriptParser = new ScriptParser($this->originalScript);
        $this->scriptParser->getImports();

        // Some pre-processing of the view.
        $this->viewWithoutScriptTag = $this->replaceComponentMethodLoadingStates($this->viewWithoutScriptTag);
        $this->viewWithoutScriptTag = $this->replaceElementRefs($this->viewWithoutScriptTag);

        if (! SpladePlugin::shouldGenerateVueComponentForPath($this->bladePath)) {
            return $this->viewWithoutScriptTag;
        }

        // Adjust the current defineProps, or generate a new one if it didn't exist yet.
        $defineVueProps = $this->extractDefinePropsFromScript();
        $propsBag = $defineVueProps->toAttributeBag();

        $vueComponent = implode(PHP_EOL, array_filter([
            '<script setup>',
            $this->renderImports(),
            $defineVueProps->generatePropsDeclaration(),
            $this->renderSpladeBridge(),
            $this->renderBladeFunctionsAsJavascriptFunctions(),
            $this->renderBladePropertiesAsComputedVueProperties(),
            $this->renderJavascriptFunctionToRefreshComponent(),
            $this->renderElementRefStoreAndSetter(),
            $defineVueProps->getOriginalScript(),
            $this->renderSpladeRenderFunction($defineVueProps),
            '</script>',
            "<template><spladeRender {$propsBag->toHtml()}>",
            '<template v-for="(_, slot) of $slots" v-slot:[slot]="scope"><slot :name="slot" v-bind="scope"/></template>',
            '</spladeRender></template>',
        ]));

        $directory = config('splade-core.compiled_scripts');
        $filesystem->ensureDirectoryExists($directory);
        $filesystem->put($vuePath = $directory.DIRECTORY_SEPARATOR."{$this->getTag()}.vue", $vueComponent);

        if (config('splade-core.prettify_compiled_scripts')) {
            Process::path(base_path())->run("node_modules/.bin/eslint --fix {$vuePath}");
        }

        return $this->viewWithoutScriptTag;
    }

    /**
     * Check if the view uses custom bound attributes.
     */
    protected function attributesAreCustomBound(): bool
    {
        return str_contains($this->originalView, 'v-bind="$attrs"');
    }

    /**
     * Get the functions that are passed from the Blade Component.
     */
    protected function getBladeFunctions(): array
    {
        $functions = $this->data['spladeBridge']['functions'] ?? [];

        if ($functions instanceof ResolveOnce) {
            $functions = $functions();
        }

        return $functions;
    }

    /**
     * Get the properties that are passed from the Blade Component.
     */
    protected function getBladeProperties(): array
    {
        $data = $this->data['spladeBridge']['data'] ?? [];

        if ($data instanceof ResolveOnce) {
            $data = $data();
        }

        return array_keys($data);
    }

    /**
     * Get the properties that are passed from the Blade Component.
     */
    protected function getBladePropsThatArePassedAsVueProps(): array
    {
        $props = $this->data['spladeBridge']['props'] ?? [];

        if ($props instanceof ResolveOnce) {
            $props = $props();
        }

        return $props;
    }

    /**
     * Get the 'Splade' tag of the Blade Component.
     */
    protected function getTag(): string
    {
        if ($this->isComponent()) {
            return $this->data['spladeBridge']['tag'];
        }

        return $this->componentHelper->getTag($this->bladePath);
    }

    /**
     * Check if the view uses the refreshComponent method.
     */
    protected function isRefreshable(): bool
    {
        return str_contains($this->originalView, 'refreshComponent');
    }

    /**
     * Check if the view needs the SpladeBridge.
     */
    protected function needsSpladeBridge(): bool
    {
        if (! $this->isComponent()) {
            return false;
        }

        if (! empty($this->getBladeFunctions())) {
            return true;
        }

        if (! empty($this->getBladeProperties())) {
            return true;
        }

        if ($this->isRefreshable()) {
            return true;
        }

        return false;
    }

    /**
     * Check if the view uses element refs.
     */
    protected function viewUsesElementRefs(): bool
    {
        return preg_match('/ref="(\w+)"/', $this->originalView) > 0;
    }

    /**
     * Check if the view uses v-model.
     */
    protected function viewUsesVModel(): bool
    {
        return str_contains($this->originalView, 'modelValue');
    }

    /**
     * Extract the script from the view.
     */
    protected function splitOriginalView(): void
    {
        $this->originalScript = Str::betweenFirst($this->originalView, '<script setup>', '</script>');

        $this->viewWithoutScriptTag = Str::of($this->originalView)
            ->replaceFirst("<script setup>{$this->originalScript}</script>", '')
            ->trim()
            ->toString();

        $this->extractWrappedViewInRootLayout();

        if (empty($this->viewRootLayoutTags)) {
            return;
        }

        $this->viewWithoutScriptTag = Str::of($this->viewWithoutScriptTag)
            ->after($this->viewRootLayoutTags[0])
            ->beforeLast($this->viewRootLayoutTags[1])
            ->trim()
            ->toString();
    }

    /**
     * Replace someMethod.loading with someMethod.loading.value
     */
    protected function replaceComponentMethodLoadingStates(string $script): string
    {
        if (! $this->isComponent()) {
            return $script;
        }

        $methods = ['refreshComponent', ...$this->getBladeFunctions()];

        return preg_replace_callback('/(\w+)\.loading/', function ($matches) use ($methods) {
            if (! in_array($matches[1], $methods)) {
                return $matches[0];
            }

            return $matches[1].'.loading.value';
        }, $script);
    }

    /**
     * Replace ref="textarea" with :ref="(value) => setRef('textarea', value)"
     */
    protected function replaceElementRefs(string $script): string
    {
        return preg_replace('/ref="(\w+)"/', ':ref="(value) => setSpladeRef(\'$1\', value)"', $script);
    }

    /**
     * Extract the defineProps from the script.
     */
    protected function extractDefinePropsFromScript(): DefineVueProps
    {
        $bladePropsAsVueProps = Collection::make($this->getBladePropsThatArePassedAsVueProps())
            ->map(function (object $specs) {
                $type = null;

                if (! $specs->raw) {
                    $type = is_array($specs->type) ? '['.implode(',', $specs->type).']' : "{$specs->type}";
                }

                return $type ? "{type: {$type}}" : '{}';
            });

        $defaultProps = Collection::make(['spladeTemplateId' => 'String'])
            ->merge($bladePropsAsVueProps)
            ->when($this->isComponent(), fn (Collection $collection) => $collection->prepend('Object', 'spladeBridge'))
            ->when($this->viewUsesVModel(), fn (Collection $collection) => $collection->put('modelValue', '{}'));

        $defineVueProps = $this->scriptParser->getDefineProps($defaultProps->all());

        if (! $defineVueProps->getOriginalScript()) {
            $defineVueProps->setOriginalScript($this->originalScript);

        } else {
            $defineVueProps->setOriginalScript(
                str_replace($defineVueProps->getOriginalScript(), '', $this->originalScript)
            );
        }

        return $defineVueProps;
    }

    /**
     * Renders the imports for the Vue script.
     */
    protected function renderImports(): string
    {
        $vueFunctionsImports = $this->scriptParser->getVueFunctions()
            ->when($this->getImportedComponents()['dynamic']->isNotEmpty(), fn ($collection) => $collection->push('markRaw'))
            ->when($this->needsSpladeBridge(), fn ($collection) => $collection->push('inject')->push('ref'))
            ->when($this->isRefreshable(), fn ($collection) => $collection->push('inject'))
            ->when($this->isComponent() && ! empty($this->getBladeProperties()), fn ($collection) => $collection->push('computed'))
            ->unique()
            ->sort()
            ->implode(',');

        return $vueFunctionsImports ? <<<JS
import { {$vueFunctionsImports} } from 'vue';
JS : '';
    }

    /**
     * Renders the state for the SpladeBridge.
     */
    protected function renderSpladeBridge(): string
    {
        if (! $this->needsSpladeBridge()) {
            return '';
        }

        return <<<'JS'
const _spladeBladeHelpers = inject("$spladeBladeHelpers");
const _spladeBridgeState = ref(props.spladeBridge);
JS;
    }

    /**
     * Renders a Javascript function that calls the Blade function.
     */
    protected function renderBladeFunctionsAsJavascriptFunctions(): string
    {
        $lines = [];

        foreach ($this->getBladeFunctions() as $function) {
            $lines[] = <<<JS
const {$function} = _spladeBladeHelpers.asyncComponentMethod('{$function}', _spladeBridgeState);
JS;
        }

        return implode(PHP_EOL, $lines);
    }

    /**
     * Renders a computed Vue property for a Blade property.
     */
    protected static function renderBladePropertyAsComputedVueProperty(string $property): string
    {
        return <<<JS
const {$property} = computed({
    get() { return _spladeBridgeState.value.data.{$property} },
    set(newValue) { _spladeBridgeState.value.data.{$property} = newValue }
});
JS;
    }

    /**
     * Renders computed Vue properties for all Blade properties.
     */
    protected function renderBladePropertiesAsComputedVueProperties(): string
    {
        $lines = [];

        foreach ($this->getBladeProperties() as $property) {
            $lines[] = static::renderBladePropertyAsComputedVueProperty($property);
        }

        return implode(PHP_EOL, $lines);
    }

    /**
     * Injects the $spladeTemplateBus and adds a 'refreshComponent' method.
     */
    protected function renderJavascriptFunctionToRefreshComponent(): string
    {
        if (! $this->isRefreshable()) {
            return '';
        }

        return <<<'JS'
const _spladeTemplateBus = inject("$spladeTemplateBus");
const refreshComponent = _spladeBladeHelpers.asyncRefreshComponent(_spladeBridgeState, _spladeTemplateBus);
JS;
    }

    /**
     * Renders the element ref store and setter.
     */
    protected function renderElementRefStoreAndSetter(): string
    {
        if (! $this->viewUsesElementRefs()) {
            return '';
        }

        return <<<'JS'
const $refs = {};
const setSpladeRef = (key, value) => $refs[key] = value;
JS;
    }

    /**
     * Returns the imported components.
     */
    protected function getImportedComponents(): array
    {
        if ($this->importedComponents) {
            return $this->importedComponents;
        }

        $this->importedComponents = [
            'static' => Collection::make([]),
            'dynamic' => Collection::make([]),
        ];

        if (! $this->isComponent()) {
            return $this->importedComponents;
        }

        Collection::make($this->scriptParser->getImports())
            ->keys()
            ->each(function (string $import) {
                if (Str::contains($this->viewWithoutScriptTag, "<{$import}")) {
                    return $this->importedComponents['static'][] = $import;
                }

                // match anything in :is="" (e.g.: :is="true ? A : B") attribute
                preg_match_all('/:is="(.+?)"/', $this->viewWithoutScriptTag, $matches);

                $isDynamic = Collection::make($matches[1] ?? [])
                    ->flatMap(fn (string $match) => explode(' ', $match))
                    ->contains($import);

                if ($isDynamic) {
                    return $this->importedComponents['dynamic'][] = $import;
                }
            });

        return $this->importedComponents;
    }

    /**
     * Renders the SpladeRender 'h'-function.
     */
    protected function renderSpladeRenderFunction(DefineVueProps $defineVueProps): string
    {
        $inheritAttrs = $this->attributesAreCustomBound() ? <<<'JS'
inheritAttrs: false,
JS : '';

        $importedComponents = $this->getImportedComponents();

        $dataObject = Collection::make()
            ->merge($this->getBladeProperties())
            ->merge($this->scriptParser->getVariables()->reject(fn ($variable) => $variable === 'props'))
            ->merge($this->getBladeFunctions())
            ->when($this->isRefreshable(), fn (Collection $collection) => $collection->push('refreshComponent'))
            ->when($this->viewUsesElementRefs(), fn (Collection $collection) => $collection->push('setSpladeRef'))
            ->merge($importedComponents['dynamic']->map(function (string $component) {
                return "{$component}: markRaw({$component})";
            }))
            ->implode(',');

        $components = Collection::make($importedComponents['static'])->implode(',');

        $componentsObject = $components ? <<<JS
components: { {$components} },
JS : '';

        $definePropsObject = $defineVueProps->getNewPropsObject();

        return <<<JS
const spladeRender = {
    {$inheritAttrs}
    {$componentsObject}
    name: "{$this->getTag()}Render",
    template: spladeTemplates[props.spladeTemplateId],
    props: {$definePropsObject},
    data: () => ({{$dataObject}}),
};
JS;
    }
}
