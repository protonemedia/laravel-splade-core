<?php

namespace Tests\Browser;

use Illuminate\Filesystem\Filesystem;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class BladeMethodTest extends DuskTestCase
{
    /** @test */
    public function it_can_call_a_blade_method()
    {
        $filesystem = new Filesystem;
        $filesystem->delete(storage_path('blade-method.txt'));

        $this->browse(function (Browser $browser) {
            $browser->visit('/blade-method')
                ->press('Write time');
        });

        retry(10, fn () => $this->assertFileExists(storage_path('blade-method.txt')), 200);
    }

    /** @test */
    public function it_can_call_a_blade_method_and_show_the_response()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/blade-method')
                ->press('Write time with callback')
                ->waitForText('Response: '.now()->format('Y-m-d'));
        });
    }

    /** @test */
    public function it_can_show_the_loading_state()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/blade-method')
                ->assertSee('Sleeping: No')
                ->press('Sleep')
                ->assertSee('Sleeping: Yes')
                ->waitForText('Sleeping: No');
        });
    }
}
