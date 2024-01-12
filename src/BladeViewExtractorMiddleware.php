<?php

namespace ProtoneMedia\SpladeCore;

use Illuminate\Filesystem\Filesystem;

class BladeViewExtractorMiddleware implements BladeMiddleware
{
    public function __construct(
        private Filesystem $filesystem
    ) {
        //
    }

    public function handle(string $value, array $data, string $bladePath): string
    {
        return BladeViewExtractor::from($value, $data, $bladePath)->handle($this->filesystem);
    }
}
