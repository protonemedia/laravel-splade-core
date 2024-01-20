<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ComponentImportTest extends DuskTestCase
{
    public static function urls()
    {
        return [
            ['/component-import'],
            ['/dynamic-component-import'],
        ];
    }

    /**
     * @dataProvider urls
     *
     * @test */
    public function it_handles_js_imports($url)
    {
        $this->browse(function (Browser $browser) use ($url) {
            $browser->visit($url)
                ->assertMissing('h2')
                ->assertMissing('#headlessui-portal-root')
                ->press('Open Dialog')
                ->assertSeeIn('h2', 'Dialog')
                ->assertPresent('#headlessui-portal-root');
        });
    }
}
