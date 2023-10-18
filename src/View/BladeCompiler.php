<?php

namespace ProtoneMedia\SpladeCore\View;

use Illuminate\View\Compilers\BladeCompiler as BaseBladeCompiler;
use ProtoneMedia\SpladeCore\BladeViewExtractor;

class BladeCompiler extends BaseBladeCompiler
{
    /**
     * The array of data that should be available to the template.
     */
    protected array $data = [];

    /**
     * Extract the Vue script from the given template.
     */
    public function compileString($value): string
    {
        $value = BladeViewExtractor::from(
            $value, $this->data, $this->getPath()
        )->handle($this->files);

        return parent::compileString($value);
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
