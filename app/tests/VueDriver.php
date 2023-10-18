<?php

namespace Tests;

use Spatie\Snapshots\Drivers\TextDriver;

class VueDriver extends TextDriver
{
    public function extension(): string
    {
        return 'vue';
    }
}
