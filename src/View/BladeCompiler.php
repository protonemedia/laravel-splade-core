<?php

namespace ProtoneMedia\SpladeCore\View;

use Illuminate\Support\Collection;
use Illuminate\View\Compilers\BladeCompiler as BaseBladeCompiler;
use ProtoneMedia\SpladeCore\BladeMiddleware;
use ProtoneMedia\SpladeCore\BladeViewExtractorMiddleware;

class BladeCompiler extends BaseBladeCompiler implements BladeMiddleware
{
    /**
     * The array of data that should be available to the template.
     */
    protected array $data = [];

    /**
     * The array of middleware that should be applied to the compileString() method.
     *
     * @var array
     */
    protected $compileStringMiddleware = [
        BladeViewExtractorMiddleware::class,
        self::class,
    ];

    /**
     * Adds a Blade Middleware to the middleware stack.
     */
    public function pushMiddleware(mixed $middleware): self
    {
        $this->compileStringMiddleware[] = $middleware;

        return $this;
    }

    /**
     * Adds a Blade Middleware before another Blade Middleware.
     */
    public function addMiddlewareBefore(string $middleware, string $before): self
    {
        $index = array_search($before, $this->compileStringMiddleware);

        if ($index === false) {
            throw new \Exception("Middleware {$before} not found.");
        }

        array_splice($this->compileStringMiddleware, $index, 0, $middleware);

        return $this;
    }

    /**
     * Adds a Blade Middleware after another Blade Middleware.
     */
    public function addMiddlewareAfter(string $middleware, string $after): self
    {
        $index = array_search($after, $this->compileStringMiddleware);

        if ($index === false) {
            throw new \Exception("Middleware {$after} not found.");
        }

        array_splice($this->compileStringMiddleware, $index + 1, 0, $middleware);

        return $this;
    }

    /**
     * Pipes the value through the compileStringMiddleware.
     */
    public function compileString($value): string
    {
        return Collection::make($this->compileStringMiddleware)
            ->map(function (string $middleware) {
                if (is_string($middleware)) {
                    return $middleware === self::class ? $this : app($middleware);
                }

                return $middleware;
            })
            ->reduce(function (string $value, BladeMiddleware $middleware) {
                return $middleware->handle($value, $this->data, $this->getPath());
            }, $value);
    }

    /**
     * Wrapper for the compileString() method.
     */
    public function handle(string $value, array $data, string $bladePath): string
    {
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
