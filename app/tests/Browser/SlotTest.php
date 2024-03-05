<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SlotTest extends DuskTestCase
{
    /** @test */
    public function it_can_reference_a_parent_variable_from_within_child_slots()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/slot')
                ->assertSee('This is the base view, rendering Root component (Base View Counter: 0)')
                ->assertSee('This is Root Component, rendering Child Component (Root Counter: 0)')
                ->assertSeeIn('h3', "What's that, Hawaiian Noises?")
                ->within('@slot', function (Browser $browser) {
                    $browser
                        ->assertSee('Root Counter from default slot: 0')
                        ->assertSee("What's that, Hawaiian Noises?");
                })
                ->within('@subslot', function (Browser $browser) {
                    $browser
                        ->assertSee('Root Counter from sub-slot: 0')
                        ->assertSee("What's that, Hawaiian Noises?");
                })
                ->press('Increment')
                ->assertSee('This is the base view, rendering Root component (Base View Counter: 1)')
                ->assertSee('This is Root Component, rendering Child Component (Root Counter: 1)')
                ->assertSeeIn('h3', "What's that, Hawaiian Noises?")
                ->within('@slot', function (Browser $browser) {
                    $browser
                        ->assertSee('Root Counter from default slot: 1');
                })
                ->within('@subslot', function (Browser $browser) {
                    $browser
                        ->assertSee('Root Counter from sub-slot: 1');
                });
        });
    }
}
