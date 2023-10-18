<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class IncludeViewTest extends DuskTestCase
{
    /** @test */
    public function it_extract_the_included_script_and_slot_from_the_root_layout()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/base-view')
                ->assertSee('Splade Core demo app')
                ->type('included-message', 'Hey there!')
                ->assertSee('The message is: Hey there!')
                ->type('message', 'Hello world!')
                ->assertSeeIn('@uppercase', 'HELLO WORLD!');
        });
    }
}
