<?php

namespace ProtoneMedia\SpladeCore;

use ProtoneMedia\SpladeCore\Facades\SpladePlugin;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

abstract class SpladePluginServiceProvider extends PackageServiceProvider implements SpladePluginProvider
{
    protected string $pluginName = '';

    protected string $composerPackageName = '';

    public function getPluginName(): string
    {
        return $this->pluginName;
    }

    public function getComposerPackageName(): string
    {
        return $this->composerPackageName;
    }

    public function getLibraryBuildFilename(): string
    {
        return 'splade-core-plugin';
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name($this->pluginName)
            ->hasViews($this->pluginName);

        foreach ($this->getComponents() as $component) {
            $package->hasViewComponent('', $component);
        }

        SpladePlugin::registerPluginProvider($this);

        $this->configureSpladePackage($package);
    }

    public function configureSpladePackage(Package $package): void
    {
    }
}
