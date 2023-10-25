<?php

namespace ProtoneMedia\SpladeCore\Data;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use League\Fractal\TransformerAbstract;
use Spatie\Fractalistic\ArraySerializer;
use Spatie\Fractalistic\Fractal;
use Traversable;

class TransformerRepository
{
    private $enforce = false;

    private array $transformers = [];

    /**
     * Setter for the enforce property.
     */
    public function enforce(bool $value = true): self
    {
        $this->enforce = $value;

        return $this;
    }

    /**
     * Adds a transformer for the given class.
     */
    public function register($class, $transformer = null): self
    {
        if (is_array($class)) {
            foreach ($class as $key => $value) {
                $this->register($key, $value);
            }

            return $this;
        }

        $this->transformers[$class] = $transformer;

        return $this;
    }

    /**
     * Finds the transformer for the given class.
     */
    private function findTransformerFor(array|object $instance): mixed
    {
        if (is_object($instance)) {
            $class = get_class($instance);

            if (array_key_exists($class, $this->transformers)) {
                return $this->transformers[$class];
            }
        }

        if (is_array($instance) || $instance instanceof Traversable) {
            $firstElement = Arr::first($instance);

            return is_object($firstElement) ? $this->findTransformerFor($firstElement) : null;
        }

        return null;
    }

    /**
     * Checks if the given value can be transformed.
     */
    private function canBeTransformed(mixed $value): bool
    {
        if (is_array($value) || $value instanceof Traversable) {
            return $this->canBeTransformed(Arr::first($value));
        }

        return is_object($value);
    }

    /**
     * Transforms the given instance.
     */
    public function handle(mixed $instance): mixed
    {
        if (! $this->canBeTransformed($instance)) {
            return $instance;
        }

        $transformer = $this->findTransformerFor($instance);

        $instanceName = is_object($instance) ? get_class($instance) : 'array';

        if ($transformer === null) {
            throw_if($this->enforce, new InvalidTransformerException("No transformer found for {$instanceName}"));

            return $instance;
        }

        if (is_subclass_of($transformer, TransformerAbstract::class)) {
            if (! class_exists(Fractal::class)) {
                throw new InvalidTransformerException(
                    "To use Fractal Transformers, please install the 'spatie/fractalistic' package."
                );
            }

            return Fractal::create($instance, new $transformer, new ArraySerializer)->toArray();
        }

        if (is_array($instance) || $instance instanceof Traversable) {
            $instance = $instance instanceof Collection ? $instance : Collection::make($instance);
        }

        if (is_subclass_of($transformer, JsonResource::class)) {
            /** @var JsonResource */
            $resource = $instance instanceof Collection
                ? $transformer::collection($instance)
                : new $transformer($instance);

            return $resource->resolve();
        }

        if (is_callable($transformer)) {
            return $instance instanceof Collection
                ? $instance->map($transformer)->all()
                : $transformer($instance);
        }

        throw new InvalidTransformerException("The transformer for {$instanceName} is not a valid transformer.");
    }
}
