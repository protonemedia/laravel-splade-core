<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Snapshots\MatchesSnapshots;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use MatchesSnapshots;

    public function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function assertMatchesVueSnapshot($actual): void
    {
        $this->assertMatchesSnapshot($actual, new VueDriver());
    }
}
