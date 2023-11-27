<?php

namespace ProtoneMedia\SpladeCore\Facades;

use Illuminate\Support\Facades\Facade;
use Illuminate\View\Component;
use ProtoneMedia\SpladeCore\SpladePluginProvider;
use ProtoneMedia\SpladeCore\SpladePluginRepository;

/**
 * @method static void registerPluginProvider(SpladePluginProvider $provider)
 * @method static bool bladeComponentIsProvidedByPlugin(string|Component $component)
 * @method static void dontGenerateVueComponentForPath(string $path)
 * @method static bool shouldGenerateVueComponentForPath(string $path)
 *
 * @see \ProtoneMedia\SpladeCore\SpladePluginRepository
 */
class SpladePlugin extends Facade
{
    protected static function getFacadeAccessor()
    {
        return SpladePluginRepository::class;
    }
}
