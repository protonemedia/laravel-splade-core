<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class FormTest extends DuskTestCase
{
    /** @test */
    public function it_handles_regular_two_way_binding_with_a_form_object()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/form')
                ->type('package', 'Dusk')
                ->assertSee('"package":"Dusk"');
        });
    }

    /** @test */
    public function it_passes_the_vmodel_to_the_blade_component()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/form')
                ->select('framework', 'vue')
                ->assertSee('"framework":"vue"');
        });
    }

    /** @test */
    public function it_passes_the_vmodel_to_a_custom_vue_component()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/form')
                ->click('.flatpickr-input')
                ->waitFor('.flatpickr-day')
                ->click('.flatpickr-day[aria-label="January 31, 2021"]')
                ->assertSee('"date":"2021-01-31"');
        });
    }
}
