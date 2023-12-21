<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ToVuePropTest extends DuskTestCase
{
    /** @test */
    public function it_can_pass_blade_props_as_vue_rops()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/to-vue-prop')
                ->waitForText('Splade Core demo app')
                ->assertSee('Mixed: foo')
                ->assertSee('String: foo')
                ->assertSee('Default String: foo')
                ->assertSee('Nullable String:')
                ->assertSee('Int: 1')
                ->assertSee('Bool: false')
                ->assertSee('Array: [ "foo", "bar" ]')
                ->assertSee('Object: { "foo": "bar" }')
                ->assertSee('Nullable Int:')
                ->assertSee('Nullable Bool:')
                ->assertSee('Nullable Array:')
                ->assertSee('Nullable Object:')
                ->assertSee('Default Int: 1')
                ->assertSee('Default Bool: true')
                ->assertSee('Default Array: [ "foo" ]')
                ->assertSee('Multiple Types: [ "foo" ]')
                ->assertSee('Data From Method: [ "foo", "bar", "baz" ]')
                ->assertSee('Renamed: renamed-foo')
                ->assertSee('JS Object: { "foo": "bar" } (object)');
        });
    }
}
