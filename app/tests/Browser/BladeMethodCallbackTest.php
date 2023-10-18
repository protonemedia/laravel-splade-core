<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class BladeMethodCallbackTest extends DuskTestCase
{
    /** @test */
    public function it_can_bind_a_callback_to_a_blade_method()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/blade-method-callbacks')
                ->assertSee('Response: -')
                ->press('Execute')
                ->assertSee('Response: waiting...')
                ->waitForText('Response: yes!');
        });
    }

    /** @test */
    public function it_can_bind_a_catch_callback_to_a_blade_method()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/blade-method-callbacks')
                ->assertSee('Response: -')
                ->press('Fail')
                ->waitForText('Response: no!');
        });
    }
}
