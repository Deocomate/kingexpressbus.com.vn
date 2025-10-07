<?php

namespace App\View\Components\Admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Sidebar extends Component
{
    public $authUser;
    public ?object $webProfile;

    /**
     * Create a new component instance.
     */
    public function __construct($authUser = null, ?object $webProfile = null)
    {
        $this->authUser = $authUser ?? auth()->user();
        $this->webProfile = $webProfile;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.sidebar');
    }
}
