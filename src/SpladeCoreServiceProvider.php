<?php

namespace ProtoneMedia\SpladeCore;

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Compilers\ComponentTagCompiler;
use Illuminate\View\Component;
use Illuminate\View\DynamicComponent;
use Illuminate\View\Engines\EngineResolver;
use ProtoneMedia\SpladeCore\Commands\BuildComponents;
use ProtoneMedia\SpladeCore\Commands\ClearComponents;
use ProtoneMedia\SpladeCore\Commands\InitializeComponentsDirectory;
use ProtoneMedia\SpladeCore\Http\InvokeComponentController;
use ProtoneMedia\SpladeCore\View\BladeCompiler;
use ProtoneMedia\SpladeCore\View\CompilerEngine;
use ProtoneMedia\SpladeCore\View\Factory;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SpladeCoreServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-splade-core')
            ->hasConfigFile()
            ->hasCommand(BuildComponents::class)
            ->hasCommand(ClearComponents::class)
            ->hasCommand(InitializeComponentsDirectory::class);
    }

    public function packageRegistered()
    {
        $this->registerBladeCompiler();
        $this->registerComponentHelper();
        $this->registerBladeEngine();
        $this->registerFactory();

        $this->app->singleton(SpladeCoreRequest::class, function (Application $app) {
            return new SpladeCoreRequest(fn () => $app['request']);
        });
    }

    protected function registerComponentHelper()
    {
        $this->app->singleton(ComponentHelper::class, function () {
            /** @var BladeCompiler */
            $bladeCompliler = $this->app['blade.compiler'];

            $componentTagCompiler = new ComponentTagCompiler(
                $bladeCompliler->getClassComponentAliases(),
                $bladeCompliler->getClassComponentNamespaces(),
                $bladeCompliler
            );

            return new ComponentHelper($componentTagCompiler, $this->app['files']);
        });
    }

    /**
     * @see \Illuminate\View\ViewServiceProvider
     */
    protected function registerBladeEngine()
    {
        /** @var EngineResolver */
        $resolver = $this->app['view.engine.resolver'];

        $resolver->register('blade', function () {
            $compiler = new CompilerEngine($this->app['blade.compiler'], $this->app['files']);

            $this->app->terminating(static function () use ($compiler) {
                $compiler->forgetCompiledOrNotExpired();
            });

            $compiler->setComponentHelper($this->app->make(ComponentHelper::class));

            return $compiler;
        });
    }

    /**
     * @see \Illuminate\View\ViewServiceProvider
     */
    protected function registerBladeCompiler()
    {
        $this->app->extend('blade.compiler', function () {
            $app = $this->app;

            return tap(new BladeCompiler(
                $app['files'],
                $app['config']['view.compiled'],
                $app['config']->get('view.relative_hash', false) ? $app->basePath() : '',
                $app['config']->get('view.cache', true),
                $app['config']->get('view.compiled_extension', 'php'),
            ), function (BladeCompiler $blade) {
                $blade->component('dynamic-component', DynamicComponent::class);
            });
        });

        BladeCompiler::beforeCompilingString(
            fn ($value, $data, $path) => ExtractVueScriptFromBladeView::from($value, $data, $path)->handle(app('files'))
        );
    }

    protected function registerFactory()
    {
        $this->app->singleton('view', function ($app) {
            // Next we need to grab the engine resolver instance that will be used by the
            // environment. The resolver will be used by an environment to get each of
            // the various engine implementations such as plain PHP or Blade engine.
            $resolver = $app['view.engine.resolver'];

            $finder = $app['view.finder'];

            $factory = new Factory($resolver, $finder, $app['events']);

            // We will also set the container instance on this view environment since the
            // view composers may be classes registered in the container, which allows
            // for great testable, flexible composers for the application developer.
            $factory->setContainer($app);

            $factory->share('app', $app);

            $app->terminating(static function () {
                Component::forgetFactory();
                Factory::clearSpladeComponents();
            });

            return $factory;
        });

        Factory::beforeStartComponent(AddSpladeToComponentData::callback());
    }

    public function packageBooted()
    {
        Route::post(config('splade-core.invoke_component_uri'), InvokeComponentController::class)
            ->middleware(Arr::wrap(config('splade-core.invoke_component_middleware', [])))
            ->name('splade-core.invoke-component');

        Event::listen(CommandFinished::class, function (CommandFinished $event) {
            if ($event->command === 'view:clear') {
                Artisan::call(ClearComponents::class);
            }
        });
    }
}
