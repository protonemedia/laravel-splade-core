<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ChangeBladePropTest extends DuskTestCase
{
    /** @test */
    public function it_can_change_a_blade_prop_from_vue()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/change-blade-prop')
                ->assertSee('Message: Hello World')
                ->press('Change Message with Vue')
                ->waitForText('Message: Hey, Vue!');
        });
    }

    /** @test */
    public function it_can_change_a_blade_prop_from_within_a_blade_method()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/change-blade-prop')
                ->assertSee('Message: Hello World')
                ->press('Change Message with Blade')
                ->waitForText('Message: From the inside: Hey, Blade!');
        });
    }
}
