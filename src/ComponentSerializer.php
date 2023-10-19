<?php

namespace ProtoneMedia\SpladeCore;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use JsonSerializable;
use ProtoneMedia\SpladeCore\Attributes\Vue;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

class ComponentSerializer implements Arrayable
{
    use SerializesNewModels;

    public function __construct(
        protected Component $component,
        protected ComponentHelper $componentHelper,
    ) {
    }

    /**
     * Helper to create a new instance.
     */
    public static function make(Component $component): static
    {
        return new static($component, app(ComponentHelper::class));
    }

    /**
     * A serialized, zipped, encrypted represenation of the component.
     */
    public function getSerializedComponent(): string
    {
        return encrypt(gzencode(serialize($this->component), 9));
    }

    /**
     * Returns an array with the required data for the signature
     * and the signature itself.
     */
    public static function getDataWithSignature(array $data): array
    {
        $dataForSignature = Arr::only($data, [
            'instance', 'original_url', 'original_verb', 'template_hash',
        ]);

        ksort($dataForSignature);

        $data['signature'] = hash_hmac('sha256', serialize($dataForSignature), config('app.key'));

        return $data;
    }

    /**
     * Serializes the component to an array with a signature.
     */
    public function toArray(array $with = []): array
    {
        return static::getDataWithSignature(
            array_merge([
                'data' => $this->getDataFromProperties(),
                'functions' => $this->getFunctionsFromComponentClass(get_class($this->component)),
                'instance' => $this->getSerializedComponent(),
                'invoke_url' => route('splade-core.invoke-component'),
                'original_url' => null,
                'original_verb' => null,
                'response' => null,
                'tag' => $this->componentHelper->getTag($this->component),
                'template_hash' => null,
            ], $with)
        );
    }

    /**
     * Gathers all data that will be two-way bound to the Vue component.
     */
    public function getDataFromProperties(): array
    {
        $properties = (new ReflectionClass($this->component))->getProperties();

        $values = [];

        foreach ($properties as $property) {
            if ($property->isStatic() || ! $property->isPublic() || ! $property->isInitialized($this->component)) {
                continue;
            }

            $name = $property->getName();

            if ($name === 'componentName' || $name === 'attributes') {
                continue;
            }

            if (empty($property->getAttributes(Vue::class))) {
                continue;
            }

            $value = $property->getValue($this->component);

            if ($value instanceof Model) {
                $value = (object) $value->jsonSerialize();
            }

            if ($value instanceof Collection) {
                $value = $value->map(function ($item) {
                    if ($item instanceof Model) {
                        return (object) $item->jsonSerialize();
                    }

                    return $item;
                })->jsonSerialize();
            }

            if ($value instanceof JsonSerializable) {
                $value = $value->jsonSerialize();
            }

            $values[$name] = $this->getSerializedPropertyValue($value);
        }

        return $values;
    }

    /**
     * Same as getDataFromProperties() but for a component class, so
     * without any values.
     */
    public static function getDataFromComponentClass(string $componentClass): array
    {
        $properties = (new ReflectionClass($componentClass))->getProperties();

        $values = [];

        foreach ($properties as $property) {
            /** @var ReflectionProperty $property */
            if ($property->isStatic() || ! $property->isPublic()) {
                continue;
            }

            $name = $property->getName();

            if ($name === 'componentName' || $name === 'attributes') {
                continue;
            }

            if (empty($property->getAttributes(Vue::class))) {
                continue;
            }

            $values[$name] = '';
        }

        return $values;
    }

    /**
     * Returns all public functions from the component class.
     */
    public static function getFunctionsFromComponentClass(string $componentClass): array
    {
        $ignoredFunctions = IgnoredComponentFunctions::get();

        $functions = (new ReflectionClass($componentClass))->getMethods(ReflectionMethod::IS_PUBLIC);

        return Collection::make($functions)
            ->reject(fn ($function) => in_array($function->getName(), $ignoredFunctions))
            ->reject(fn (ReflectionMethod $function) => $function->isStatic())
            ->reject(fn (ReflectionMethod $function) => empty($function->getAttributes(Vue::class)))
            ->map(fn ($function) => $function->getName())
            ->values()
            ->all();
    }
}
