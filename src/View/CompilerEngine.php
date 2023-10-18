<?php

namespace ProtoneMedia\SpladeCore\View;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
use Illuminate\View\Engines\CompilerEngine as BaseCompilerEngine;
use ProtoneMedia\SpladeCore\ComponentHelper;
use ProtoneMedia\SpladeCore\PendingView;

class CompilerEngine extends BaseCompilerEngine
{
    private ComponentHelper $componentHelper;

    /**
     * Setter for the ComponentHelper instance.
     */
    public function setComponentHelper(ComponentHelper $componentHelper): self
    {
        $this->componentHelper = $componentHelper;

        return $this;
    }

    /**
     * Set the data on the Blade Compiler and get the evaluated contents of the view.
     */
    public function get($path, array $data = [])
    {
        /** @var BladeCompiler */
        $compiler = $this->compiler;
        $compiler->setData($data);

        if (str_contains($path, DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR)) {
            $vueComponent = $this->componentHelper->getTag($path).'.vue';

            // Delete the compiled script if the Vue component is not found,
            // for example, when the compiled component is deleted but not
            // the compiled template.
            if (! $this->files->exists(config('splade-core.compiled_scripts').DIRECTORY_SEPARATOR.$vueComponent)) {
                $this->files->delete($this->getCompiler()->getCompiledPath($path));
            }
        }

        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 6);

        // prevent leaking the full path
        $tracePath = Str::after($trace[5]['file'], base_path());

        $hash = md5($tracePath.'.'.$trace[5]['line']);

        $result = parent::get($path, $data);
        $compiler->setData([]);

        // TODO: evaluate if $path contains <script setup> tag,
        // move stuff over from BladeCompiler::compileString()
        if (! array_key_exists($hash, $compiler->pendingViews)) {
            return $result;
        }

        /** @var PendingView */
        $pendingView = $compiler->pendingViews[$hash];

        app('view')->pushSpladeTemplate($hash, $result);

        return $pendingView->render($hash);

    }
}
