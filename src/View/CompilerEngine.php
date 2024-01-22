<?php

namespace ProtoneMedia\SpladeCore\View;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
use Illuminate\View\Engines\CompilerEngine as BaseCompilerEngine;
use ProtoneMedia\SpladeCore\BladeViewExtractor;
use ProtoneMedia\SpladeCore\ComponentHelper;

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
        $compiler = $this->getCompiler();
        $compiler->setData($data);

        if (! str_starts_with($path, config('view.compiled'))) {
            $vueComponent = $this->componentHelper->getTag($path).'.vue';

            // Delete the compiled script if the Vue component is not found,
            // for example, when the compiled component is deleted but not
            // the compiled template.
            if (! $this->files->exists(config('splade-core.compiled_scripts').'/'.$vueComponent)) {
                $this->files->delete($compiler->getCompiledPath($path));
            }
        }

        $result = tap(
            parent::get($path, $data),
            fn () => $compiler->setData([])
        );

        if (str_contains($path, '/components/')) {
            return $result;
        }

        $service = BladeViewExtractor::from($this->files->get($path), $data, $path);

        if (! $service->hasScriptSetup()) {
            return $result;
        }

        $hash = md5(Str::random());

        app('view')->pushSpladeTemplate($hash, $result);

        return $service->getViewAsVueRenderer()->render($hash, $result);
    }
}
