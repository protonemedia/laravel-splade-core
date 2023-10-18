<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RegularViewTest extends DuskTestCase
{
    /** @test */
    public function it_extract_the_script_and_slot_from_the_root_layout()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/regular-view')
                ->assertSee('Splade Core demo app')
                ->type('regular-message', 'Hey there!')
                ->assertSee('The message is: Hey there!')
                ->type('message', 'Hello world!')
                ->assertSeeIn('@uppercase', 'HELLO WORLD!');
        });
    }
}
