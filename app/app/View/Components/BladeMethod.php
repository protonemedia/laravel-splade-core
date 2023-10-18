<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class BladeMethod extends Component
{
    /**
     * Create a new component instance.
     */
    public function execute(string $input)
    {
        file_put_contents(
            storage_path('blade-method.txt'),
            $input
        );

        return $input;
    }

    public function sleep()
    {
        sleep(2);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.blade-method');
    }
}
