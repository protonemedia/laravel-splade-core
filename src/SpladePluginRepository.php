<?php

namespace ProtoneMedia\SpladeCore;

use Illuminate\Support\Collection;
use Illuminate\View\Component;

class SpladePluginRepository
{
    /**
     * @var SpladePluginProvider[]
     */
    private array $plugins = [];

    /**
     * @var Component[]
     */
    private array $bladeComponents = [];

    /**
     * @var string[]
     */
    private array $componentsOnWorkbench = [];

    /**
     * @var string[]
     */
    private array $dontGenerateVueComponentForPaths = [];

    /**
     * Register a plugin provider.
     */
    public function registerPluginProvider(SpladePluginProvider $provider): void
    {
        $this->plugins[get_class($provider)] = $provider;

        foreach ($provider->getComponents() as $component) {
            $this->bladeComponents[$component] = true;
        }
    }

    /**
     * Returns a boolean indicating if the given component is provided by a plugin.
     */
    public function bladeComponentIsProvidedByPlugin(string|Component $component): bool
    {
        $componentClass = is_string($component) ? $component : get_class($component);

        return array_key_exists($componentClass, $this->bladeComponents);
    }

    /**
     * Put the given components on the workbench.
     *
     * @param  array<array-key, class-string>  $components
     */
    public function putComponentsOnWorkbench(array $components): void
    {
        foreach ($components as $component) {
            $this->componentsOnWorkbench[$component] = true;
        }
    }

    /**
     * Returns a boolean indicating if the given component is on the workbench.
     */
    public function componentIsOnWorkbench(string|Component $component): bool
    {
        $componentClass = is_string($component) ? $component : get_class($component);

        return array_key_exists($componentClass, $this->componentsOnWorkbench);
    }

    /**
     * Mark the given path as a path that we should not generate a Vue component for.
     */
    public function dontGenerateVueComponentForPath(string $path): void
    {
        $this->dontGenerateVueComponentForPaths[$path] = true;
    }

    /**
     * Returns a boolean indicating if we should generate a Vue component for the given path.
     */
    public function shouldGenerateVueComponentForPath(string $path): bool
    {
        return ! array_key_exists($path, $this->dontGenerateVueComponentForPaths);
    }

    private function getJavascriptImports(): Collection
    {
        return collect($this->plugins)->mapWithKeys(function (SpladePluginProvider $provider) {
            $hash = 'S'.md5($provider->getLibraryBuildFilename());

            $path = base_path('vendor/'.$provider->getComposerPackageName().'/dist/'.$provider->getLibraryBuildFilename());

            return [$hash => "import {$hash} from '{$path}'"];
        });
    }

    /**
     * Generates a JavaScript file that imports all the plugins.
     */
    public function generateManifest(): void
    {
        $imports = $this->getJavascriptImports();

        $installPluginsIntoApp = $imports->keys()
            ->map(fn (string $hash) => "app.use({$hash})")
            ->implode("\n");

        $contents = [
            '// This file is automatically generated by Splade Core.',
            $imports->implode("\n"),
            <<<JS
export default function installPlugins(app) {
    {$installPluginsIntoApp}
}

JS
        ];

        file_put_contents(
            config('splade-core.compiled_scripts').'/'.'plugins.js',
            implode("\n\n", array_filter($contents))
        );
    }
}