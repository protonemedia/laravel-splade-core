<?php

namespace Tests\Unit\Console;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use ProtoneMedia\SpladeCore\Commands\InitializeComponentsDirectory;
use Tests\TestCase;

class InitializeComponentsDirectoryTest extends TestCase
{
    /** @test */
    public function it_initializes_the_components_directory()
    {
        $filesytem = new Filesystem;
        $filesytem->deleteDirectory(resource_path('js/splade'));

        Artisan::call(InitializeComponentsDirectory::class);

        $this->assertDirectoryExists(resource_path('js/splade'));
        $this->assertFileExists(resource_path('js/splade/.gitignore'));
    }
}
