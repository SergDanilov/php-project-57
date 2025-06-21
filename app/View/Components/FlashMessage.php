<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FlashMessage extends Component
{
    public function __construct()
    {
        // you may add some logic here when it need's
    }

    public function render(): View|Closure|string
    {
        return view('components.flash-message');
    }
}
