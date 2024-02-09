<?php

namespace ProtoneMedia\SpladeCore;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

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

    private function findViewPath($bladePath): string
    {
        foreach (app(ComponentHelper::class)->getViewPaths() as $viewPath) {
            if (str_starts_with($bladePath, $viewPath)) {
                return $viewPath;
            }
        }
    }

    public function ensureBladeViewExists(string $usedInBladePath): void
    {
        $directory = $this->findViewPath($usedInBladePath).'/components/splade';

        $path = $directory.'/'.$this->getBladeFilename();

        if (file_exists($path)) {
            return;
        }

        $importName = $this->double ? "{{$this->name}}" : $this->name;

        $contents = <<<BLADE
<script setup>
    import {$importName} from '{$this->module}';
</script>

<{$this->name}>
    {{ \$slot }}
</{$this->name}>
BLADE;

        $filesystem = new Filesystem;
        $filesystem->ensureDirectoryExists($directory);

        $gitIgnore = $directory.'/.gitignore';

        if (! $filesystem->exists($gitIgnore)) {
            $filesystem->put($gitIgnore, "*\n!.gitignore\n");
        }

        file_put_contents($path, $contents);
    }

    public function getBladeFilename(): string
    {
        return 'auto-generated-'.Str::kebab($this->name).'.blade.php';
    }

    public function getBladeTag(): string
    {
        return 'x-splade.auto-generated-'.Str::kebab($this->name);
    }
}
