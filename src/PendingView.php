<?php

namespace ProtoneMedia\SpladeCore;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Js;

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
        $bridge = Js::from(['tag' => $this->tag, 'template_hash' => $hash])->toHtml();

        if (empty($this->rootLayoutTags)) {
            return Blade::render(<<<HTML
<generic-splade-component :bridge="{$bridge}"></generic-splade-component>
HTML);
        }

        return Blade::render(<<<HTML
{$this->rootLayoutTags[0]}
<generic-splade-component :bridge="{$bridge}"></generic-splade-component>
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
