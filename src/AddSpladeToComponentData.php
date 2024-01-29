<?php

namespace ProtoneMedia\SpladeCore;

use Illuminate\Support\Facades\Log;
use Illuminate\View\AnonymousComponent;
use Illuminate\View\Component;
use Illuminate\View\View;
use ProtoneMedia\SpladeCore\Facades\SpladePlugin;

class AddSpladeToComponentData
{
    public function __invoke(Component $component, array &$data, string $hash, mixed $view = null): void
    {
        if (! ($view instanceof View || $component instanceof AnonymousComponent)) {
            return;
        }

        /** @var ComponentHelper */
        $componentHelper = app(ComponentHelper::class);

        $viewContents = $componentHelper->filesystem->get(
            $path = $componentHelper->getPath($view)
        );

        if (SpladePlugin::bladeComponentIsProvidedByPlugin($component) && ! SpladePlugin::componentIsOnWorkbench($component)) {
            SpladePlugin::dontGenerateVueComponentForPath($path);
        }

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

        Log::debug('Splade bridge added to component', [
            'component' => get_class($component),
            'path' => $path,
        ]);
    }
}
