<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ComponentImportTest extends DuskTestCase
{
    /** @test */
    public function it_handles_js_imports()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/component-import')
                ->assertMissing('h2')
                ->assertMissing('#headlessui-portal-root')
                ->press('Open Dialog')
                ->assertSeeIn('h2', 'Dialog')
                ->assertPresent('#headlessui-portal-root');
        });
    }
}
