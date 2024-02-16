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

    public function replaceHtmlWithVueSlot(string $value): string
    {
        if (str_contains($value, $this->getTemplateContents())) {
            return $value;
        }

        return str_replace($this->contents, $this->toVueSlot(), $value);
    }

    public function toVueSlot(): string
    {
        return '<slot name="'.$this->hash.'" '.((string) $this->attributes).'></slot>';
    }

    private function getTemplateContents(): string
    {
        return '<!--splade-template-'.$this->hash.'-->'.$this->contents.'</template>';
    }

    public function toVueTemplate(): string
    {
        return '<template v-slot:'.$this->hash.'>'.$this->getTemplateContents().'</template>';
    }
}
