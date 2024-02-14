<?php

namespace ProtoneMedia\SpladeCore\View;

use Illuminate\View\ComponentSlot as BaseComponentSlot;

class ComponentSlot extends BaseComponentSlot
{
    public function __construct(
        string $contents,
        array $attributes,
        private string $hash
    ) {
        parent::__construct($contents, $attributes);
    }

    public function getHash(): string
    {
        return $this->hash;
    }
}
