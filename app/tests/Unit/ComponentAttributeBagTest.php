<?php

namespace Tests\Unit;

use Illuminate\View\ComponentAttributeBag;
use Tests\TestCase;

class ComponentAttributeBagTest extends TestCase
{
    private function attr($attribute, $value = null, bool $omitBlankValue = true, bool $escape = true): string
    {
        return (new ComponentAttributeBag)
            ->vue($attribute, $value, $omitBlankValue, $escape)
            ->toHtml();
    }

    /** @test */
    public function it_omits_blank_values_by_default()
    {
        $this->assertEmpty($this->attr('animation', ''));
        $this->assertEmpty($this->attr('animation', null));
        $this->assertEmpty($this->attr('animation', []));
    }

    /** @test */
    public function it_doesnt_affect_non_vue_attributes()
    {
        $this->assertEquals('animation="default"', $this->attr('animation', 'default'));
    }

    /** @test */
    public function it_rewrites_a_vue_event_shortcut_to_the_full_notation()
    {
        $this->assertEquals('v-on:click="doSomething"', $this->attr('@click', 'doSomething'));
        $this->assertEquals('v-on:click="doSomething"', $this->attr('v-on:click', 'doSomething'));
    }

    /** @test */
    public function it_rewrites_a_vue_binding_shortcut_to_the_full_notation()
    {
        $this->assertEquals('v-bind:animation="default"', $this->attr(':animation', 'default'));
        $this->assertEquals('v-bind:animation="default"', $this->attr('v-bind:animation', 'default'));
    }

    /** @test */
    public function it_rewrites_a_boolean_value_to_a_string()
    {
        $this->assertEquals('v-bind:animation="true"', $this->attr(':animation', true));
        $this->assertEquals('v-bind:animation="false"', $this->attr(':animation', false));
    }

    /** @test */
    public function it_can_bind_arrays()
    {
        $this->assertEquals('v-bind:animation="JSON.parse(&#039;[1,2,3]&#039;)"', $this->attr(':animation', [1, 2, 3]));
        $this->assertEquals('v-bind:animation="JSON.parse(&#039;[true,false]&#039;)"', $this->attr(':animation', [true, false]));
        $this->assertEquals('v-bind:animation="JSON.parse(&#039;[\u0022a\u0022,\u0022b\u0022]&#039;)"', $this->attr(':animation', ['a', 'b']));
    }
}
