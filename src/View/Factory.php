<?php

namespace ProtoneMedia\SpladeCore\View;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Js;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;
use Illuminate\View\Factory as BaseFactory;
use ProtoneMedia\SpladeCore\AddSpladeToComponentData;
use ProtoneMedia\SpladeCore\ResolveOnce;
use ProtoneMedia\SpladeCore\View\ComponentSlot as CustomComponentSlot;

class Factory extends BaseFactory
{
    /**
     * Whether to track Splade components.
     */
    protected static bool $trackSpladeComponents = false;

    /**
     * The tracked Splade components.
     */
    protected static array $spladeComponents = [];

    /**
     * The original slots before they were replaced with a Vue slot.
     */
    protected array $originalSlots = [];

    /**
     * Enable tracking of Splade components.
     */
    public static function trackSpladeComponents(): void
    {
        static::$trackSpladeComponents = true;
        static::clearSpladeComponents();
    }

    /**
     * Clear the tracked Splade components.
     */
    public static function clearSpladeComponents(): void
    {
        static::$spladeComponents = [];
    }

    /**
     * Get a single tracked Splade component.
     */
    public static function getSpladeComponent(string $key): ?string
    {
        return static::$spladeComponents[$key] ?? null;
    }

    /**
     * Get all tracked Splade components.
     */
    public static function getSpladeComponents(): array
    {
        return static::$spladeComponents;
    }

    /**
     * Execute the callback before the component is started.
     */
    public function startComponent($view, array $data = [], $component = null)
    {
        if ($component instanceof Component) {
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);

            // prevent leaking the full path
            $path = Str::after($trace[0]['file'], base_path());

            $hash = md5($path.'.'.$trace[0]['line'].json_encode($data));

            (new AddSpladeToComponentData)($component, $data, $hash, $view);
        }

        return parent::startComponent($view, $data);
    }

    /**
     * Initialize the stack for the Splade templates once.
     */
    protected function prepareSpladeTemplatesStack(): void
    {
        if ($this->hasRenderedOnce('splade-templates')) {
            return;
        }

        $this->markAsRenderedOnce('splade-templates');
        $this->extendPush('splade-templates', 'const spladeTemplates = {};');
    }

    /**
     * Pushes a Splade template to the stack.
     */
    public function pushSpladeTemplate($id, $value): void
    {
        $this->prepareSpladeTemplatesStack();

        $value = Js::from($value)->toHtml();

        $this->extendPush(
            'splade-templates',
            "spladeTemplates['{$id}'] = {$value};"
        );
    }

    /**
     * Replaces the Blade component slots with Vue slots and preserves the original slots
     * so they can be injected in the generic Splade component (in renderComponent()).
     */
    protected function componentData()
    {
        $this->originalSlots = [];

        $data = parent::componentData();

        $templateId = $data['spladeBridge']['template_hash'] ?? null;

        /** @var ComponentSlot|null */
        $defaultSlot = $data['__laravel_slots']['__default'] ?? null;

        if (! isset($data['__laravel_slots']['slot'])
            && $defaultSlot?->toHtml() !== '###SPLADE-INJECT-HERE###'
            && ! Str::startsWith($defaultSlot?->toHtml(), ['<generic-splade-component', '<!--splade-template-id='])
        ) {
            $data['__laravel_slots']['slot'] = $data['__laravel_slots']['__default'];
            $data['__laravel_slots']['__default'] = new ComponentSlot;
        }

        $data['__laravel_slots'] = collect($data['__laravel_slots'] ?? [])
            ->mapWithKeys(function (ComponentSlot $slot, $name) use ($templateId) {
                $slot = new CustomComponentSlot(
                    $slot->toHtml(),
                    $slot->attributes->getAttributes(),
                    $hash = md5(Str::random(32))
                );

                $this->originalSlots[$name] = clone $slot;

                if ($slot->isEmpty()) {
                    return [$name => $slot];
                }

                $vueSlot = '<slot name="'.$slot->getHash().'" '.((string) $slot->attributes).'></slot>';

                $componentSlot = new CustomComponentSlot(
                    static::$trackSpladeComponents
                        ? "<!--splade-template-id=\"{$templateId}\"-->{$vueSlot}"
                        : $vueSlot,
                    [], $hash
                );

                return [$name => $componentSlot];
            })
            ->all();

        foreach ($data['__laravel_slots'] as $name => $slot) {
            $data[$name] = $slot;
        }

        return $data;
    }

    /**
     * Temporarily store the passed attributes, render the component and
     * push it to the Splade templates stack. Then return a generic Vue
     * component that will grab the template from the stack.
     *
     * @return string
     */
    public function renderComponent()
    {
        /** @var array */
        $componentData = $this->componentData[$this->currentComponent()];

        /** @var array|null */
        $spladeBridge = $componentData['spladeBridge'] ?? null;

        if ($spladeBridge) {
            /** @var ComponentAttributeBag */
            $attributes = $componentData['attributes'];

            $this->componentData[$this->currentComponent()]['attributes'] = new ComponentAttributeBag;
        }

        $output = parent::renderComponent();

        if (! $spladeBridge) {
            return $output;
        }

        $templateId = $spladeBridge['template_hash'];

        if (static::$trackSpladeComponents) {
            static::$spladeComponents[$templateId] = $output;
        }

        foreach (['data', 'props', 'functions'] as $key) {
            if ($spladeBridge[$key] instanceof ResolveOnce) {
                $spladeBridge[$key] = $spladeBridge[$key]();
            }
        }

        $spladeBridgeHtml = Js::from(Arr::only($spladeBridge, [
            'instance',
            'invoke_url',
            'original_url',
            'original_verb',
            'signature',
            'tag',
            'template_hash',
            'data',
            // 'props',
            // 'functions',
            // 'response',
        ]))->toHtml();

        collect($spladeBridge['props'])->each(function ($specs, $key) use ($attributes) {
            if (! str_starts_with($key, 'v-bind:')) {
                $key = 'v-bind:'.Str::kebab($key);
            }

            $attributes[$key] = $specs->raw
                ? $specs->value
                : Js::from($specs->value)->toHtml();
        });

        $attrs = $attributes->toHtml();

        $this->pushSpladeTemplate($templateId, $output);

        $slotsHtml = collect($this->originalSlots)->map(function ($slot, $name) {
            return "<template #{$slot->getHash()}>{$slot->toHtml()}</template>";
        })->implode("\n");

        $genericComponent = "<generic-splade-component {$attrs} :bridge=\"{$spladeBridgeHtml}\">
            {$slotsHtml}
        </generic-splade-component>";

        return static::$trackSpladeComponents
            ? "<!--splade-template-id=\"{$templateId}\"-->{$genericComponent}"
            : $genericComponent;
    }
}
