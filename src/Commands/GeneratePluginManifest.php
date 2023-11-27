<?php

namespace ProtoneMedia\SpladeCore\Commands;

use Illuminate\Console\Command;
use ProtoneMedia\SpladeCore\SpladePluginRepository;

class GeneratePluginManifest extends Command
{
    public $signature = 'splade:core:generate-plugin-manifest';

    public $description = 'Generates a plugin manifest file.';

    public function handle(SpladePluginRepository $pluginRepository): int
    {
        $this->comment('Generating plugin manifest...');

        $pluginRepository->generateManifest();

        $this->comment('Done!');

        return self::SUCCESS;
    }
}
