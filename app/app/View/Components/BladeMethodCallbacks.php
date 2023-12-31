<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use ProtoneMedia\SpladeCore\Attributes\Vue;

class BladeMethodCallbacks extends Component
{
    #[Vue]
    public function execute()
    {
        sleep(2);
    }

    #[Vue]
    public function fail()
    {
        abort(500);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.blade-method-callbacks');
    }
}
