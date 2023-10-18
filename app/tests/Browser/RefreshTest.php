<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RefreshTest extends DuskTestCase
{
    /** @test */
    public function it_can_refresh_the_parent_and_all_of_its_children()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/refresh')->pause(1000);

            $parentTime = $browser->text('@parent');
            $childTime = $browser->text('@child');

            $browser->press('Refresh Parent')
                ->waitUntilMissingText($parentTime)
                ->waitUntilMissingText($childTime)
                ->assertDontSeeIn('@parent', $parentTime)
                ->assertDontSeeIn('@child', $childTime);
        });
    }

    /** @test */
    public function it_can_refresh_a_child_without_refreshing_the_parent()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/refresh')->pause(1000);

            $parentTime = $browser->text('@parent');
            $childTime = $browser->text('@child');

            $browser->press('Refresh Child')
                ->within('@child', fn (Browser $browser) => $browser->waitUntilMissingText($childTime))
                ->assertDontSeeIn('@child', $childTime)
                ->assertSeeIn('@parent', $parentTime);
        });
    }

    /** @test */
    public function it_can_show_a_loading_state_when_a_component_is_refreshing()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/refresh-state');

            $time = $browser->text('@time');

            $browser
                ->assertSee('Is refreshing: No')
                ->assertSee('Status: idle')
                ->press('Refresh')
                ->assertSee('Is refreshing: Yes')
                ->assertSee('Status: loading')
                ->waitUntilMissingText($time)
                ->assertSee('Is refreshing: No')
                ->assertSee('Status: idle');
        });
    }
}
