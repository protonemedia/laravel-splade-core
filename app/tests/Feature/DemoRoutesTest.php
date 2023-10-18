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
            ['/dynamic'],
            ['/form'],
            ['/refresh-state'],
            ['/refresh'],
            ['/regular-view'],
            ['/two-way-binding'],
        ];
    }

    /**
     * @test
     *
     * @runInSeparateProcess
     *
     * @dataProvider demoRoutes
     */
    public function it_renders_all_demo_routes($route)
    {
        $this->get($route)->assertOk();
    }
}
