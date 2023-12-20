<?php

namespace ProtoneMedia\SpladeCore\View;

use Illuminate\Support\Js;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\Factory as BaseFactory;
use ProtoneMedia\SpladeCore\AddSpladeToComponentData;

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
     * Get the tracked Splade components.
     */
    public static function getSpladeComponent(string $key): ?string
    {
        return static::$spladeComponents[$key] ?? null;
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

        if (! array_key_exists('spladeBridge', $componentData)) {
            return parent::renderComponent();
        }

        /** @var ComponentAttributeBag */
        $attributes = $componentData['attributes'];

        $this->componentData[$this->currentComponent()]['attributes'] = new ComponentAttributeBag;

        $output = parent::renderComponent();

        $templateId = $componentData['spladeBridge']['template_hash'];

        if (static::$trackSpladeComponents) {
            static::$spladeComponents[$templateId] = $output;
        }

        $this->pushSpladeTemplate($templateId, $output);

        $spladeBridge = Js::from($componentData['spladeBridge'])->toHtml();

        collect($componentData['spladeBridge']['props'])->each(function ($specs, $key) use ($attributes) {
            if (! str_starts_with($key, 'v-bind:')) {
                $key = 'v-bind:'.Str::kebab($key);
            }

            $attributes[$key] = Js::from($specs->value)->toHtml();
        });

        $attrs = $attributes->toHtml();

        return static::$trackSpladeComponents
            ? "<!--splade-template-id=\"{$templateId}\"--><generic-splade-component {$attrs} :bridge=\"{$spladeBridge}\"></generic-splade-component>"
            : "<generic-splade-component {$attrs} :bridge=\"{$spladeBridge}\"></generic-splade-component>";
    }
}
