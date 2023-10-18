<?php

namespace Tests\Unit\Console;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use ProtoneMedia\SpladeCore\Commands\BuildComponents;
use Tests\TestCase;

class BuildComponentsTest extends TestCase
{
    /** @test */
    public function it_builds_the_components()
    {
        $filesytem = new Filesystem;
        $filesytem->cleanDirectory(resource_path('js/splade'));
        $filesytem->ensureDirectoryExists(resource_path('js/splade'));
        $filesytem->put(resource_path('js/splade/.gitignore'), '');

        $this->assertEquals(0, Artisan::call(BuildComponents::class));

        foreach ([
            'ComponentAnonymous',
            'ComponentBladeMethod',
            'ComponentBladeMethodCallbacks',
            'ComponentChangeBladeProp',
            'ComponentDatePicker',
            'ComponentForm',
            'ComponentTime',
            'ComponentTimeState',
            'ComponentTwoWayBinding',
            'IncludedView',
            'RegularView',
        ] as $component) {
            $this->assertFileExists(resource_path('js/splade/Splade'.$component.'.vue'));
            $this->assertMatchesVueSnapshot($filesytem->get(resource_path('js/splade/Splade'.$component.'.vue')));
        }
    }
}
