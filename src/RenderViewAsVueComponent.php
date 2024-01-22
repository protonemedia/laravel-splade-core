<?php

namespace ProtoneMedia\SpladeCore;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;

class RenderViewAsVueComponent
{
    public function __construct(
        public readonly string $viewWithoutScript,
        public readonly string $tag,
        public readonly array $rootLayoutTags
    ) {
    }

    public static function from(string $viewWithoutScript, string $path, array $rootLayoutTags)
    {
        /** @var ComponentHelper */
        $componentHelper = app(ComponentHelper::class);

        return new static($viewWithoutScript, $componentHelper->getTag($path), $rootLayoutTags);
    }

    public function render(string $templateId): string
    {
        $tag = Str::kebab($this->tag);

        $component = "<{$tag}></{$tag}>";

        if (empty($this->rootLayoutTags)) {
            return $component;
        }

        $rootLayout = Blade::render(<<<HTML
{$this->rootLayoutTags[0]}
###SPLADE-INJECT-SLOT-HERE###
{$this->rootLayoutTags[1]}
HTML);

        return str_replace('###SPLADE-INJECT-SLOT-HERE###', $component, $rootLayout);
    }
}
