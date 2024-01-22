<?php

namespace ProtoneMedia\SpladeCore;

class DefineVueProps
{
    private string $originalScript = '';

    private string $newPropsObject = '';

    private array $propKeys = [];

    public function generatePropsDeclaration(): string
    {
        $object = $this->getNewPropsObject();

        return "const props = defineProps({$object});";
    }

    public function getPropKeys(): array
    {
        return $this->propKeys;
    }

    public function addPropKey(string $key): self
    {
        $this->propKeys[] = $key;

        return $this;
    }

    public function getNewPropsObject(): string
    {
        return $this->newPropsObject;
    }

    public function setNewProjectObject(string $newPropsObject): self
    {
        $this->newPropsObject = $newPropsObject;

        return $this;
    }

    public function getOriginalScript(): string
    {
        return $this->originalScript;
    }

    public function setOriginalScript(string $script): self
    {
        $this->originalScript = trim($script);

        return $this;
    }

    public function toArray()
    {
        return [
            'original' => $this->getOriginalScript(),
            'new' => $this->getNewPropsObject(),
            'keys' => $this->getPropKeys(),
        ];
    }
}
