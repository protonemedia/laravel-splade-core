<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use ProtoneMedia\SpladeCore\Facades\SpladeCore;

class TimeState extends Component
{
    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        if (SpladeCore::isRefreshingComponent()) {
            sleep(2);
        }

        return view('components.time-state');
    }
}
