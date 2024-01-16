<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class EmitTest extends DuskTestCase
{
    /** @test */
    public function it_can_bind_a_listener_to_an_emit_event()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/emit')
                ->assertMissing('h2')
                ->press('Trigger')
                ->assertSeeIn('h2', 'Triggered');
        });
    }
}
