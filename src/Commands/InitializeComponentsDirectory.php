<?php

namespace ProtoneMedia\SpladeCore\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class InitializeComponentsDirectory extends Command
{
    public $signature = 'splade:core:initialize-directory';

    public $description = 'Initializes the directory where the compiled Vue Components by Splade Core will be stored';

    public function handle(Filesystem $filesystem): int
    {
        $directory = config('splade-core.compiled_scripts');

        $filesystem->ensureDirectoryExists($directory);

        $gitIgnore = $directory.'/.gitignore';

        if (! $filesystem->exists($gitIgnore)) {
            $filesystem->put($gitIgnore, "*\n!.gitignore\n");
        }

        $this->comment('Done!');

        return self::SUCCESS;
    }
}
