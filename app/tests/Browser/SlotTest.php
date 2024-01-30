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
                ->assertSee('Layout Counter: 0')
                ->assertSee('Root Counter: 0')
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
                ->assertSee('Layout Counter: 1')
                ->assertSee('Root Counter: 1')
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
