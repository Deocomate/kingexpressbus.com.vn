<?php

namespace App\View\Components\Admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Footer extends Component
{
    public ?object $webProfile;

    /**
     * Create a new component instance.
     */
    public function __construct(?object $webProfile = null)
    {
        $this->webProfile = $webProfile;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.footer');
    }
}
