<?php

namespace Tests\Feature;

use Tests\TestCase;

class DemoRoutesTest extends TestCase
{
    public static function demoRoutes(): array
    {
        return [
            ['/anonymous'],
            ['/base-view'],
            ['/blade-method-callbacks'],
            ['/blade-method'],
            ['/change-blade-prop'],
            ['/component-import'],
            ['/dynamic'],
            ['/dynamic-component-import'],
            ['/emit'],
            ['/form'],
            ['/props-in-template'],
            ['/refresh-state'],
            ['/refresh'],
            ['/regular-view'],
            ['/slot'],
            ['/to-vue-prop'],
            ['/two-way-binding'],
        ];
    }

    /**
     * @test
     *
     * @dataProvider demoRoutes
     */
    public function it_renders_all_demo_routes($route)
    {
        $this->get($route)->assertOk();
    }
}
