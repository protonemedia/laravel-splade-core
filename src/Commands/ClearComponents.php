<?php

namespace ProtoneMedia\SpladeCore\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;

class ClearComponents extends Command
{
    public $signature = 'splade:core:clear-components';

    public $description = 'Clears all compiled Vue Components by Splade Core';

    public function handle(Filesystem $filesystem): int
    {
        $this->cleanCompiledScripts($filesystem);

        $this->comment('Done!');

        return self::SUCCESS;
    }

    private function cleanCompiledScripts(Filesystem $filesystem): void
    {
        $directory = config('splade-core.compiled_scripts');

        $filesystem->ensureDirectoryExists($directory);

        foreach ($filesystem->allFiles($directory) as $script) {
            $filename = $script->getFilename();

            /** @var SplFileInfo $script */
            if (! str_starts_with($filename, 'Splade') || ! str_ends_with($filename, '.vue')) {
                continue;
            }

            $this->info("Removing {$script}");
            $filesystem->delete($script);
        }
    }
}
