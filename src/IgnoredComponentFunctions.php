<?php

namespace ProtoneMedia\SpladeCore;

use Illuminate\View\Component;
use ReflectionClass;
use ReflectionMethod;

class IgnoredComponentFunctions
{
    protected static $ignoredMethods;

    /**
     * Returns all public methods of the abstract Component class.
     */
    public static function get(): array
    {
        if (is_array(static::$ignoredMethods)) {
            return static::$ignoredMethods;
        }

        self::$ignoredMethods = ['__construct'];

        $reflection = new ReflectionClass(Component::class);

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $componentMethod) {
            if ($componentMethod->isPublic()) {
                self::$ignoredMethods[] = $componentMethod->getName();
            }
        }

        return self::$ignoredMethods;
    }
}
