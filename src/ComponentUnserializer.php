<?php

namespace ProtoneMedia\SpladeCore;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use JsonSerializable;
use ReflectionClass;
use Throwable;

class ComponentUnserializer
{
    use SerializesNewModels;

    public function __construct(
        protected array $data
    ) {
    }

    /**
     * Creates a new instance from the given data.
     */
    public static function fromData(array $data): self
    {
        return new static($data);
    }

    /**
     * Validates the signature and throws an exception if it's invalid.
     */
    protected function validateSignature(): void
    {
        $dataWithSignature = ComponentSerializer::getDataWithSignature($this->data);

        if ($dataWithSignature['signature'] !== $this->data['signature']) {
            throw new InvalidSignatureException;
        }
    }

    /**
     * Returns the unserialized component.
     */
    protected function getUnserializedComponent(): Component
    {
        try {
            return unserialize(gzdecode(decrypt($this->data['instance'])));
        } catch (Throwable $e) {
            throw new CouldNotResolveComponentClassException;
        }
    }

    /**
     * Validates the signature, unserializes the component, and applies
     * the new property data to the component.
     */
    public function unserialize(): Component
    {
        $this->validateSignature();

        return tap(
            $this->getUnserializedComponent(),
            fn (Component $component) => $this->applyNewPropertyData($component)
        );
    }

    /**
     * Applies the new property data to the component.
     */
    protected function applyNewPropertyData(Component $component): void
    {
        $properties = (new ReflectionClass($component))->getProperties();

        $data = $this->data['data'];

        foreach ($properties as $property) {
            if ($property->isStatic() || ! $property->isPublic() || ! $property->isInitialized($component)) {
                continue;
            }

            $name = $property->getName();

            if ($name === 'componentName' || $name === 'attributes') {
                continue;
            }

            if (! array_key_exists($name, $data)) {
                continue;
            }

            $value = $property->getValue($component);

            if (! $value instanceof JsonSerializable) {
                // Just set the property with the value from the data.
                static::fill($component, $name, $data[$name]);

                continue;
            }

            // Transform the data to a dotted array and then set each key individually.
            Collection::make(Arr::dot($data[$name]))->each(function ($dottedValue, $dottedKey) use ($value) {
                if (! Str::contains($dottedKey, '.')) {
                    return static::fill($value, $dottedKey, $dottedValue);
                }

                $lastKey = Str::afterLast($dottedKey, '.');
                $parentKey = Str::beforeLast($dottedKey, '.');

                $target = data_get($value, $parentKey);

                static::fill($target, $lastKey, $dottedValue);
            });
        }
    }

    /**
     * A little helper method that force the use of the fill
     * method on an Eloquent Model so the filled/guarded attributes are respected.
     */
    protected static function fill(&$target, $key, $value)
    {
        $originalValue = data_get($target, $key);

        if ($originalValue === $value) {
            // Only set dirty attributes.
            return;
        }

        if ($target instanceof Collection && $originalValue instanceof Model) {
            // Don't override Models without visible values with an empty array.
            if (blank($value) || (is_object($value) && $value == new \stdClass)) {
                return;
            }

            $targetModel = $target->get($key);

            foreach ($value as $k => $v) {
                static::fill($targetModel, $k, $v);
            }

            return;
        }

        if ($target instanceof Model) {
            return $target->fill([$key => $value]);
        }

        data_set($target, $key, $value);
    }
}
