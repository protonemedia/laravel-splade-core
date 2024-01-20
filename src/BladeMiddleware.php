<?php

namespace ProtoneMedia\SpladeCore;

interface BladeMiddleware
{
    public function handle(string $value, array $data, string $bladePath): string;
}
