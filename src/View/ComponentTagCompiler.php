<?php

namespace ProtoneMedia\SpladeCore\View;

use Illuminate\View\Compilers\ComponentTagCompiler as BaseComponentTagCompiler;

class ComponentTagCompiler extends BaseComponentTagCompiler
{
    protected function compileOpeningTags(string $value)
    {
        return $value = parent::compileOpeningTags($value);

        if (! str_contains($value, '<?php $component->withAttributes')) {
            return $value;
        }

        if (str_contains($value, '<template #default>')) {
            dd($value);
        }

        return collect(explode("\n", $value))->map(function ($line) {
            if (trim($line) === '<?php $component->withAttributes([]); ?>') {
                return $line.'<template #default>';
            }

            if (trim($line) === '<?php $component->withAttributes([\'@incremented\' => \'layoutCounter++\']); ?>') {
                return $line.'<template #default>';
            }

            if (str_contains($line, '<?php $component->withAttributes')) {
                dd($line);
            }

            return $line;
        })->implode("\n");
    }

    protected function compileClosingTags(string $value)
    {
        return $value = parent::compileClosingTags($value);

        return collect(explode("\n", $value))->map(function ($line) {
            if (str_contains($line, '</template>')) {
                return $line;
            }

            if (trim($line) === '@endComponentClass##END-COMPONENT-CLASS##') {
                return $line.'</template>';
            }

            return $line;
        })->implode("\n");
    }
}
