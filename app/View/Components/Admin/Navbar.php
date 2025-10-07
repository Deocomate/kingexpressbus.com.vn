<?php

namespace App\View\Components\Admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Navbar extends Component
{
    public $authUser;

    /**
     * Create a new component instance.
     */
    public function __construct($authUser = null)
    {
        $this->authUser = $authUser ?? auth()->user();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.navbar');
    }
}
