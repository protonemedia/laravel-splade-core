<?php

namespace ProtoneMedia\SpladeCore;

use Illuminate\View\AnonymousComponent;
use Illuminate\View\Component;
use Illuminate\View\View;

class AddSpladeToComponentData
{
    public static function callback(): callable
    {
        return function (Component $component, array &$data, string $hash, mixed $view = null) {

            if (! ($view instanceof View || $component instanceof AnonymousComponent)) {
                return;
            }

            /** @var ComponentHelper */
            $componentHelper = app(ComponentHelper::class);

            $viewContents = $componentHelper->filesystem->get(
                $componentHelper->getPath($view)
            );

            if (! str_starts_with(trim($viewContents), '<script setup')) {
                // No Vue 3 script setup, so no need to add the Splade bridge.
                return;
            }

            $key = 'spladeBridge';

            $data[$key] = ComponentSerializer::make($component)->toArray([
                'template_hash' => $hash,
                'original_url' => url()->current(),
                'original_verb' => request()->method(),
            ]);

            if ($view instanceof View) {
                $view->with($key, $data[$key]);
            }
        };
    }
}
