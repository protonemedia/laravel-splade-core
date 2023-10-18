<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TwoWayBindingTest extends DuskTestCase
{
    /** @test */
    public function it_handles_two_way_binding()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/two-way-binding')
                ->type('message', 'Hello World')
                ->assertSeeIn('@uppercase', 'HELLO WORLD');
        });
    }
}
