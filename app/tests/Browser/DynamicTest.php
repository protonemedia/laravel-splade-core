<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DynamicTest extends DuskTestCase
{
    /** @test */
    public function it_handles_dynamic_components()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/dynamic')
                ->assertSeeIn('@uppercase', 'HELLO VUE!')
                ->assertSeeIn('@reversed', '!euV olleH');
        });
    }
}
