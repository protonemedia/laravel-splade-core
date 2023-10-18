<?php

namespace Tests\Unit\Console;

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Mockery;
use ProtoneMedia\SpladeCore\Commands\ClearComponents;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tests\TestCase;

class ClearComponentsTest extends TestCase
{
    /** @test */
    public function it_clears_all_splade_components()
    {
        $filesytem = new Filesystem;

        $filesytem->put(resource_path('js/splade/SpladeTest.js'), '');
        $filesytem->put(resource_path('js/splade/SpladeComponent.vue'), '<script setup></script><template></template>');
        $filesytem->put(resource_path('js/splade/GenericComponent.vue'), '<script setup></script><template></template>');

        Artisan::call(ClearComponents::class);

        $this->assertFileExists(resource_path('js/splade/SpladeTest.js'));
        $this->assertFileDoesNotExist(resource_path('js/splade/SpladeComponent.vue'));
        $this->assertFileExists(resource_path('js/splade/GenericComponent.vue'));
    }

    /** @test */
    public function it_clears_all_splade_components_when_the_compiled_views_are_cleared()
    {
        $filesytem = new Filesystem;

        $filesytem->put(resource_path('js/splade/SpladeTest.js'), '');
        $filesytem->put(resource_path('js/splade/SpladeComponent.vue'), '<script setup></script><template></template>');
        $filesytem->put(resource_path('js/splade/GenericComponent.vue'), '<script setup></script><template></template>');

        event(new CommandFinished('view:clear', Mockery::mock(InputInterface::class), Mockery::mock(OutputInterface::class), 0));

        $this->assertFileExists(resource_path('js/splade/SpladeTest.js'));
        $this->assertFileDoesNotExist(resource_path('js/splade/SpladeComponent.vue'));
        $this->assertFileExists(resource_path('js/splade/GenericComponent.vue'));
    }
}
