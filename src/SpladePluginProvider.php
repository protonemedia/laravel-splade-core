<?php

namespace ProtoneMedia\SpladeCore;

interface SpladePluginProvider
{
    /**
     * @return array<array-key, class-string>
     */
    public function getComponents(): array;

    public function getComposerPackageName(): string;

    public function getLibraryBuildFilename(): string;
}
