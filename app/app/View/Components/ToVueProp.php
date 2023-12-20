<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use ProtoneMedia\SpladeCore\Attributes\VueProp;

class ToVueProp extends Component
{
    #[VueProp]
    public mixed $mixed = 'foo';

    #[VueProp]
    public string $string = 'foo';

    #[VueProp]
    public string $defaultString = 'foo';

    #[VueProp]
    public ?string $nullableString = null;

    /**
     * Create a new component instance.
     */
    public function __construct(
        #[VueProp] public int $int,
        #[VueProp] public bool $bool,
        #[VueProp] public array $array,
        #[VueProp] public object $object,
        #[VueProp] public ?int $nullableInt = null,
        #[VueProp] public ?bool $nullableBool = null,
        #[VueProp] public ?array $nullableArray = null,
        #[VueProp] public ?object $nullableObject = null,
        #[VueProp] public int $defaultInt = 1,
        #[VueProp] public bool $defaultBool = true,
        #[VueProp] public array $defaultArray = ['foo'],
        #[VueProp] public array|bool|string $multipleTypes = ['foo'],
    ) {
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.to-vue-prop');
    }
}
