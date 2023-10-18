<?php

namespace ProtoneMedia\SpladeCore\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Console\ComponentMakeCommand;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class InstallNewApp extends Command
{
    public $signature = 'splade:core:install';

    public $description = 'Installs Splade Core into a fresh Laravel application';

    public function handle(Filesystem $filesystem): int
    {
        $this->info('Installing Splade Core...');

        Artisan::call(InitializeComponentsDirectory::class);
        Artisan::call(ComponentMakeCommand::class, ['name' => 'Demo']);
        Artisan::call(ComponentMakeCommand::class, ['name' => 'Layout']);

        $this->updateNodePackages(function (array $packages) {
            return [
                '@protonemedia/laravel-splade-core' => '^1.0.0',
                '@protonemedia/laravel-splade-vite' => '^1.0.3',
                '@vitejs/plugin-vue' => '^4.4.0',
                'axios' => '^1.1.2',
                'laravel-vite-plugin' => '^0.8.0',
                'vite' => '^4.0.0',
                'vue' => '^3.3.4',
            ] + $packages;
        }, $filesystem);

        $stubs = [
            'app.js' => resource_path('js/app.js'),
            'vite.config.js' => base_path('vite.config.js'),
            'demo.blade.php' => resource_path('views/components/demo.blade.php'),
            'layout.blade.php' => resource_path('views/components/layout.blade.php'),
            'welcome.blade.php' => resource_path('views/welcome.blade.php'),
        ];

        foreach ($stubs as $stub => $destination) {
            $filesystem->copy(__DIR__.'/../../stubs/'.$stub, $destination);
        }

        $this->comment('All done');
        $this->comment('Execute "npm install" and "npm run dev" to start the Vite dev server.');

        return static::SUCCESS;
    }

    protected function updateNodePackages(callable $callback, Filesystem $filesystem): void
    {
        $packageJsonFile = base_path('package.json');

        if (! $filesystem->exists($packageJsonFile)) {
            $this->error('package.json file not found');

            return;
        }

        $packages = File::json($packageJsonFile);

        $configurationKey = 'devDependencies';

        $packages[$configurationKey] = $callback(
            array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : [],
            $configurationKey
        );

        ksort($packages[$configurationKey]);

        $filesystem->put(
            $packageJsonFile,
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).static::eol()
        );
    }

    /**
     * End of line symbol.
     */
    public static function eol(): string
    {
        return windows_os() ? "\n" : PHP_EOL;
    }
}
