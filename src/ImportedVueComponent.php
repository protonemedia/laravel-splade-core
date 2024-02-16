<?php

namespace ProtoneMedia\SpladeCore;

class ImportedVueComponent
{
    public function __construct(
        public string $name,
        public string $module,
        public bool $double = false,
        public bool $dynamic = false,
    ) {
    }

    public function setDynamic(bool $dynamic = true): self
    {
        $this->dynamic = $dynamic;

        return $this;
    }
}
