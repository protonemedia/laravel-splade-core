<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class VueComponentInSlotTest extends DuskTestCase
{
    /** @test */
    public function it_can_pass_a_blade_slot_to_a_vue_component()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/vue-component-in-slot')
                ->assertSee("This is the base view, rendering Child A with a 'Hi from parent' message:")
                ->within('@child-a', function (Browser $browser) {
                    $browser->assertSee('This is Child A, rendering Child B:');
                })
                ->within('@child-b', function (Browser $browser) {
                    $browser->assertSee('This is Child B, rendering a Custom Vue Component');
                })
                ->within('@custom-vue-component', function (Browser $browser) {
                    $browser
                        ->assertSee('This Custom Vue Component')
                        ->assertSee('Hi from parent');
                });
        });
    }
}
