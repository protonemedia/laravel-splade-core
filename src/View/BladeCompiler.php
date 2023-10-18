<?php

namespace ProtoneMedia\SpladeCore\View;

use Illuminate\Support\Str;
use Illuminate\View\Compilers\BladeCompiler as BaseBladeCompiler;
use ProtoneMedia\SpladeCore\ExtractVueScriptFromBladeView;

class BladeCompiler extends BaseBladeCompiler
{
    /**
     * The array of data that should be available to the template.
     */
    protected array $data = [];

    public array $hashes = [];

    public array $pendingViews = [];

    /**
     * Extract the Vue script from the given template.
     */
    public function compileString($value): string
    {
        $service = ExtractVueScriptFromBladeView::from($value, $this->data, $this->getPath());

        $result = $service->handle($this->files);

        if (is_string($result)) {
            return parent::compileString($result);
        }

        // TODO: move to CompilerEngine::get()
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 9);

        // prevent leaking the full path
        $path = Str::after($trace[8]['file'], base_path());

        $hash = md5($path.'.'.$trace[8]['line']);

        $this->pendingViews[$hash] = $result->setOriginalView($value);

        return parent::compileString($result->viewWithoutScript);
    }

    /**
     * Sets the data.
     */
    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Adds the component itself as a third parameter to the startComponent() method call.
     */
    public static function compileClassComponentOpening(string $component, string $alias, string $data, string $hash): string
    {
        return str_replace(
            'startComponent($component->resolveView(), $component->data())',
            'startComponent($component->resolveView(), $component->data(), $component)',
            parent::compileClassComponentOpening($component, $alias, $data, $hash)
        );
    }
}
