<?php

namespace App\View\Components\Admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MainMenuBar extends Component
{
    public $authUser;
    public string $userRole;

    /**
     * Create a new component instance.
     */
    public function __construct($authUser = null)
    {
        $this->authUser = $authUser ?? auth()->user();
        $this->userRole = $this->authUser->role ?? 'guest';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.main-menu-bar');
    }
}
