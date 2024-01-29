<?php

namespace ProtoneMedia\SpladeCore\View;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Js;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;
use Illuminate\View\Factory as BaseFactory;
use Illuminate\View\View;
use ProtoneMedia\SpladeCore\AddSpladeToComponentData;
use ProtoneMedia\SpladeCore\ResolveOnce;

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

            $name = Str::camel($component->componentName);

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

    private $originalSlots = [];

    protected function componentData()
    {
        Log::debug('Fetching slots for component', [
            'view' => $view = $this->componentData[count($this->componentStack)]['componentName'] ?? '',
        ]);

        $templateId = $this->componentData[count($this->componentStack) + 1]['spladeBridge']['template_hash'] ?? null;

        $data = parent::componentData();

        if (! array_key_exists('spladeBridge', $data)) {
            return $data;
        }

        $this->originalSlots[count($this->componentStack)] = [];

        $data['__laravel_slots'] = collect($data['__laravel_slots'] ?? [])
            ->map(function (ComponentSlot $slot, $name) use ($templateId, $view) {
                if ($slot->isEmpty()) {
                    return $slot;
                }

                $name = $name === '__default' ? 'default' : Str::kebab($name);

                $this->originalSlots[count($this->componentStack)][$name] = [
                    'slot' => $slot,
                    'component' => $view,
                ];

                $slot = '<slot name="'.$name.'"></slot>';

                return new ComponentSlot(
                    static::$trackSpladeComponents
                        ? "<!--splade-template-id=\"{$templateId}\"-->{$slot}"
                        : $slot);
            })
            ->all();

        $data['slot'] = $data['__laravel_slots']['__default'] ?? null;

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
        $component = Arr::last($this->componentStack);

        // if ($component instanceof View) {
        //     Log::debug('Rendering component', [
        //         'view' => $component->name(),
        //     ]);
        // }

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

        // if ($component instanceof View) {
        //     Log::debug('Rendered component', [
        //         'view' => $view = $component->name(),
        //     ]);

        //     if ($view === 'components.layout') {
        //         // dd($output, $this);
        //     }
        // }
        // $output = str_replace('###INSERT-SLOT###', $this->originalSlots[count($this->componentStack)]['default']->toHtml() ?? '', $output);

        if (! $spladeBridge) {
            // if ($view === 'components.layout') {
            //     // dd($output, $this);
            // }

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

        $slots = $this->originalSlots[count($this->componentStack)] ?? [];

        $slotsHtml = collect($slots)->map(function ($slot, $name) {
            return "<template #{$name}>{$slot['slot']->toHtml()}</template>";
        })->implode("\n");

        $slotKeys = collect($slots)
            ->keys()
            ->map(function ($name) {
                return "'{$name}'";
            })
            ->implode(', ');

        $genericComponent = "<generic-splade-component {$attrs} :bridge=\"{$spladeBridgeHtml}\" :slots=\"[{$slotKeys}]\">
            {$slotsHtml}
        </generic-splade-component>";

        return static::$trackSpladeComponents
            ? "<!--splade-template-id=\"{$templateId}\"-->{$genericComponent}"
            : $genericComponent;
    }
}
