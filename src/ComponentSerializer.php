<?php

namespace ProtoneMedia\SpladeCore;

use Illuminate\Container\Container;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use JsonSerializable;
use ProtoneMedia\SpladeCore\Attributes\Vue;
use ProtoneMedia\SpladeCore\Attributes\VueProp;
use ProtoneMedia\SpladeCore\Attributes\VuePropRaw;
use ProtoneMedia\SpladeCore\Attributes\VueRef;
use ProtoneMedia\SpladeCore\Facades\Transformer;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use ReflectionType;

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
    public function toArray(array $with = [], bool $resolveImmediately = false): array
    {
        return static::getDataWithSignature(
            array_merge([
                'data' => ResolveOnce::make(fn () => $this->getDataFromProperties())->resolveWhen($resolveImmediately),
                'props' => ResolveOnce::make(fn () => $this->getPropsFromComponent())->resolveWhen($resolveImmediately),
                'functions' => ResolveOnce::make(fn () => $this->getFunctionsFromComponentClass(get_class($this->component)))->resolveWhen($resolveImmediately),
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
     * Transforms a Blade Component prop.
     */
    private function transformValue(mixed $value): mixed
    {
        $value = Transformer::handle($value);

        if ($value instanceof Model) {
            return (object) $value->jsonSerialize();
        }

        if ($value instanceof Collection) {
            return $value->map(function ($item) {
                if ($item instanceof Model) {
                    return (object) $item->jsonSerialize();
                }

                return $item;
            })->jsonSerialize();
        }

        if ($value instanceof JsonSerializable) {
            return $value->jsonSerialize();
        }

        return $value;
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

            if (empty($property->getAttributes(VueRef::class))) {
                continue;
            }

            $values[$name] = $this->getSerializedPropertyValue(
                $property->getValue($this->component)
            );
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

            if (empty($property->getAttributes(VueRef::class))) {
                continue;
            }

            $values[$name] = '';
        }

        return $values;
    }

    private static function getVuePropAttribute(ReflectionProperty|ReflectionMethod $propertyOrMethod): ?ReflectionAttribute
    {
        $prop = $propertyOrMethod->getAttributes(VueProp::class);

        if (! empty($prop)) {
            return $prop[0];
        }

        $propRaw = $propertyOrMethod->getAttributes(VuePropRaw::class);

        if (! empty($propRaw)) {
            return $propRaw[0];
        }

        return null;
    }

    /**
     * Same as getPropsFromComponent() but for a component class, so
     * without any values.
     */
    public static function getPropsFromComponentClass(string $componentClass): array
    {
        $values = [];

        // From Properties...
        $properties = (new ReflectionClass($componentClass))->getProperties();

        foreach ($properties as $property) {
            if ($property->isStatic() || ! $property->isPublic()) {
                continue;
            }

            $name = $property->getName();

            if ($name === 'componentName' || $name === 'attributes') {
                continue;
            }

            $vuePropAttribute = static::getVuePropAttribute($property);

            if (! $vuePropAttribute) {
                continue;
            }

            $as = $vuePropAttribute->getArguments()['as'] ?? $name;

            $values[$as] = (object) [
                'raw' => $vuePropAttribute->getName() === VuePropRaw::class,
                'type' => static::mapTypeToVueType($property->getType()),
                'value' => null,
            ];
        }

        // From Methods...
        $methods = (new ReflectionClass($componentClass))->getMethods(ReflectionMethod::IS_PUBLIC);
        $ignoredFunctions = IgnoredComponentFunctions::get();

        foreach ($methods as $method) {
            if ($method->isStatic()) {
                continue;
            }

            $name = $method->getName();

            if (in_array($name, $ignoredFunctions)) {
                continue;
            }

            $vuePropAttribute = static::getVuePropAttribute($method);

            if (! $vuePropAttribute) {
                continue;
            }

            $as = $vuePropAttribute->getArguments()['as'] ?? $name;

            $values[$as] = (object) [
                'raw' => $vuePropAttribute->getName() === VuePropRaw::class,
                'type' => static::mapTypeToVueType($method->getReturnType()),
                'value' => null,
            ];
        }

        return $values;
    }

    /**
     * Returns all public props from the component class (one-way bound).
     */
    public function getPropsFromComponent(): array
    {
        $values = [];

        // From Properties...
        $properties = (new ReflectionClass($this->component))->getProperties();

        foreach ($properties as $property) {
            if ($property->isStatic() || ! $property->isPublic()) {
                continue;
            }

            $name = $property->getName();

            if ($name === 'componentName' || $name === 'attributes') {
                continue;
            }

            $vuePropAttribute = static::getVuePropAttribute($property);

            if (! $vuePropAttribute) {
                continue;
            }

            $as = $vuePropAttribute->getArguments()['as'] ?? $name;

            $value = $property->isInitialized($this->component)
                ? $property->getValue($this->component)
                : null;

            $values[$as] = (object) [
                'raw' => $vuePropAttribute->getName() === VuePropRaw::class,
                'type' => static::mapTypeToVueType($property->getType()),
                'value' => $this->getSerializedPropertyValue($this->transformValue($value)),
            ];
        }

        // From Methods...
        $methods = (new ReflectionClass($this->component))->getMethods(ReflectionMethod::IS_PUBLIC);
        $ignoredFunctions = IgnoredComponentFunctions::get();

        foreach ($methods as $method) {
            if ($method->isStatic()) {
                continue;
            }

            $name = $method->getName();

            if (in_array($name, $ignoredFunctions)) {
                continue;
            }

            $vuePropAttribute = static::getVuePropAttribute($method);

            if (! $vuePropAttribute) {
                continue;
            }

            $as = $vuePropAttribute->getArguments()['as'] ?? $name;

            $values[$as] = (object) [
                'raw' => $vuePropAttribute->getName() === VuePropRaw::class,
                'type' => static::mapTypeToVueType($method->getReturnType()),
                'value' => Container::getInstance()->call([$this->component, $name]),
            ];
        }

        return $values;
    }

    /**
     * Maps a PHP type to a Vue type.
     */
    public static function mapTypeToVueType(ReflectionType $type = null): array|string|null
    {
        if ($type instanceof \ReflectionUnionType) {
            $types = collect($type->getTypes())
                ->map(fn ($type = null) => static::mapTypeToVueType($type))
                ->filter();

            return $types->isEmpty() ? null : $types->all();
        }

        return match ($type?->getName()) {
            'bool' => 'Boolean',
            'int' => 'Number',
            'float' => 'Number',
            'string' => 'String',
            'array' => 'Array',
            'object' => 'Object',
            'null' => 'null',
            default => null,
        };
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
