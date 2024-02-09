<?php

namespace ProtoneMedia\SpladeCore\Commands;

use Illuminate\Console\Command;
use ProtoneMedia\SpladeCore\BladeViewExtractor;
use ProtoneMedia\SpladeCore\ComponentHelper;
use ProtoneMedia\SpladeCore\ComponentSerializer;
use Symfony\Component\Finder\SplFileInfo;

class BuildComponents extends Command
{
    public $signature = 'splade:core:build-components {--unprocessed}';

    public $description = 'Builds all Vue Components with Splade Core';

    public function handle(ComponentHelper $componentHelper): int
    {
        $filesystem = $componentHelper->filesystem;

        $this->call(ClearComponents::class);

        $this->newLine();

        if ($this->option('unprocessed')) {
            config(['splade-core.prettify_compiled_scripts' => false]);
        }

        foreach (['', '/components/splade'] as $sub) {
            foreach (config('view.paths') as $path) {
                $path = $path.$sub;

                if (! $filesystem->exists($path)) {
                    continue;
                }

                $this->info("Searching in {$path}");

                $files = $filesystem->allFiles($path);

                // sort by length so that the longest path is first
                usort($files, fn ($a, $b) => strlen($a) <=> strlen($b));

                foreach ($files as $view) {
                    /** @var SplFileInfo $view */
                    $viewPath = (string) $view;

                    if (! str_ends_with($viewPath, '.blade.php')) {
                        continue;
                    }

                    $contents = $filesystem->get($viewPath);

                    if (! str_contains($contents, '<script setup>')) {
                        continue;
                    }

                    $this->info("Compiling {$viewPath}");

                    $componentClass = $componentHelper->getClass($viewPath);

                    BladeViewExtractor::from($contents, ['spladeBridge' => [
                        'data' => $componentClass ? ComponentSerializer::getDataFromComponentClass($componentClass) : [],
                        'props' => $componentClass ? ComponentSerializer::getPropsFromComponentClass($componentClass) : [],
                        'tag' => $componentHelper->getTag($viewPath),
                        'functions' => $componentClass ? ComponentSerializer::getFunctionsFromComponentClass($componentClass) : [],
                    ]], $viewPath)->handle($filesystem);
                }
            }
        }

        $this->comment('Done!');

        return self::SUCCESS;
    }
}
