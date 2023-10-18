<?php

namespace Tests\Unit;

use ProtoneMedia\SpladeCore\IgnoredComponentFunctions;
use Tests\TestCase;

class IgnoredComponentFunctionsTest extends TestCase
{
    /** @test */
    public function it_returns_an_array_of_ignored_component_functions()
    {
        $this->assertEquals([
            '__construct',
            'render',
            'resolve',
            'resolveView',
            'data',
            'withName',
            'withAttributes',
            'shouldRender',
            'view',
            'flushCache',
            'forgetFactory',
            'forgetComponentsResolver',
            'resolveComponentsUsing',
        ], IgnoredComponentFunctions::get());
    }
}
