<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ChangeBladeProp extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $message = 'Hello World'
    ) {
    }

    public function setMessage(string $message)
    {
        $this->message = 'From the inside: '.$message;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.change-blade-prop');
    }
}
