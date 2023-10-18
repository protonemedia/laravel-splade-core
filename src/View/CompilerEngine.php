<?php

namespace ProtoneMedia\SpladeCore\View;

use Illuminate\View\Engines\CompilerEngine as BaseCompilerEngine;
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
        $compiler = $this->compiler;
        $compiler->setData($data);

        if (str_contains($path, DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR)) {
            $vueComponent = $this->componentHelper->getTag($path).'.vue';

            if (! $this->files->exists(config('splade-core.compiled_scripts').DIRECTORY_SEPARATOR.$vueComponent)) {
                $this->files->delete($this->getCompiler()->getCompiledPath($path));
            }
        }

        return tap(
            parent::get($path, $data),
            fn () => $compiler->setData([])
        );
    }
}
