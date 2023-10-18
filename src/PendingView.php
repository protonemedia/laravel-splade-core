<?php

namespace ProtoneMedia\SpladeCore;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;

class PendingView
{
    public readonly string $originalView;

    public function __construct(
        public readonly string $viewWithoutScript,
        public readonly string $tag,
        public readonly array $rootLayoutTags
    ) {
    }

    public function setOriginalView(string $originalView): self
    {
        $this->originalView = $originalView;

        return $this;
    }

    public function render(string $hash): string
    {
        $tag = Str::kebab($this->tag);

        $component = "<{$tag} splade-template-id=\"{$hash}\"></{$tag}>";

        if (empty($this->rootLayoutTags)) {
            return $component;
        }

        return Blade::render(<<<HTML
{$this->rootLayoutTags[0]}
$component
{$this->rootLayoutTags[1]}
HTML);
    }

    public static function from(string $viewWithoutScript, string $path, array $rootLayoutTags)
    {
        /** @var ComponentHelper */
        $componentHelper = app(ComponentHelper::class);

        return new static($viewWithoutScript, $componentHelper->getTag($path), $rootLayoutTags);
    }
}
