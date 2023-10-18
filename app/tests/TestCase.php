<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Snapshots\MatchesSnapshots;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use MatchesSnapshots;

    public function assertMatchesVueSnapshot($actual): void
    {
        $this->assertMatchesSnapshot($actual, new VueDriver());
    }
}
