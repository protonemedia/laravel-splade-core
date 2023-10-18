<?php

namespace ProtoneMedia\SpladeCore;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Illuminate\View\AnonymousComponent;
use Illuminate\View\Compilers\ComponentTagCompiler;
use Illuminate\View\Component;
use Illuminate\View\View;

class ComponentHelper
{
    /**
     * The view paths sorted by length.
     */
    protected ?array $viewPaths = null;

    public function __construct(
        public readonly ComponentTagCompiler $componentTagCompiler,
        public readonly Filesystem $filesystem
    ) {
    }

    /**
     * Returns the view paths sorted by length.
     */
    protected function getViewPaths(): array
    {
        if ($this->viewPaths) {
            return $this->viewPaths;
        }

        $paths = config('view.paths');

        // sort by length so that the longest path is first
        usort($paths, fn ($a, $b) => strlen($b) <=> strlen($a));

        return $this->viewPaths = $paths;
    }

    /**
     * Returns the path of a view or an anonymous component.
     */
    public function getPath(View|string $component): string
    {
        if ($component instanceof View) {
            return $component->getPath();
        }

        if (Str::contains($component, 'components.')) {
            // Anonymous component
            $filename = Str::of($component)
                ->replace('.', '/')
                ->append('.blade.php');

            foreach ($this->getViewPaths() as $path) {
                if ($this->filesystem->exists($path.'/'.$filename)) {
                    return $path.'/'.$filename;
                }
            }
        }

        return $component;
    }

    /**
     * Returns the SpladeComponent tag for a component.
     */
    public function getTag(Component|View|string $component): ?string
    {
        if ($component instanceof AnonymousComponent) {
            $component = $component->render();
        }

        $class = $this->getClass($component);

        if (! $class) {
            $isComponent = $component instanceof Component
                || Str::contains($component, 'components.')
                || Str::contains($component, '/components/');

            return Str::of($this->getAlias($component))
                ->replace('.', ' ')
                ->studly()
                ->prepend($isComponent ? 'SpladeComponent' : 'Splade')
                ->toString();
        }

        return Str::of($class)
            ->after('\\View\\Components\\')
            ->prepend('SpladeComponent')
            ->replace('\\', '')
            ->toString();
    }

    /**
     * Returns the relative path of a view.
     */
    protected function getRelativePath(string $fullPath): ?string
    {
        foreach ($this->getViewPaths() as $path) {
            if (! str_starts_with($fullPath, $path)) {
                continue;
            }

            return Str::after($fullPath, $path);
        }

        return null;
    }

    /**
     * Returns the alias of a component.
     */
    protected function getAlias(Component|string $component): ?string
    {
        $bladePath = $this->getPath($component);

        $relativePath = $this->getRelativePath($bladePath);

        return Str::of($relativePath)
            ->beforeLast('.blade.php')
            ->after('/components/')
            ->replace('/', '.')
            ->toString();
    }

    /**
     * Returns the class of a component.
     */
    public function getClass(Component|string $component): ?string
    {
        if ($component instanceof Component) {
            return $component::class;
        }

        $class = rescue(
            callback: fn () => $this->componentTagCompiler->componentClass($this->getAlias($component)),
            report: false,
        );

        return class_exists($class) ? $class : null;
    }
}
