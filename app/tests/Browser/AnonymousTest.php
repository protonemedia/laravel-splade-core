<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AnonymousTest extends DuskTestCase
{
    /** @test */
    public function it_handles_anonymous_components()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/anonymous')
                ->type('message', 'Hello World')
                ->assertSeeIn('@reversed', 'dlroW olleH');
        });
    }
}
