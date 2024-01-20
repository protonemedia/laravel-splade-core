<?php

namespace ProtoneMedia\SpladeCore\Attributes;

#[Attribute]
class VueProp
{
    public function __construct(
        public ?string $as = null,
    ) {
    }
}
