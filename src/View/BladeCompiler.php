<?php

namespace ProtoneMedia\SpladeCore\View;

use Illuminate\View\Compilers\BladeCompiler as BaseBladeCompiler;

class BladeCompiler extends BaseBladeCompiler
{
    /**
     * The array of data that should be available to the template.
     */
    protected array $data = [];

    /**
     * Callbacks that are called before compiling the string.
     */
    protected static array $beforeCompilingStringCallbacks = [];

    /**
     * Registers a callback that is called before compiling the string.
     */
    public static function beforeCompilingString(callable $callback): void
    {
        static::$beforeCompilingStringCallbacks[] = $callback;
    }

    /**
     * Extract the Vue script from the given template.
     */
    public function compileString($value): string
    {
        foreach (static::$beforeCompilingStringCallbacks as $callback) {
            $callback = $callback->bindTo($this, static::class);
            $value = $callback($value, $this->data, $this->getPath());
        }

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
