<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PropsTest extends DuskTestCase
{
    /** @test */
    public function it_exposes_the_props_to_the_template_data()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/props-in-template')
                ->assertSeeIn('h2', 'Default title')
                ->assertSeeIn('h3', 'Default subtitle');
        });
    }
}
