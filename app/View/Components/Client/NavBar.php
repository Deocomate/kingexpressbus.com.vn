<?php

namespace App\View\Components\Client;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class NavBar extends Component
{
    public ?object $webProfile;

    public array $mainMenu;

    public $authUser;

    public array $customerLinks;

    public function __construct($webProfile = null, $mainMenu = [], $authUser = null, array $customerLinks = [])
    {
        $this->webProfile = $webProfile;
        $this->mainMenu = $this->normalizeMenu($mainMenu);
        $this->authUser = $authUser;
        $this->customerLinks = $customerLinks;
    }

    protected function normalizeMenu($menu): array
    {
        if (!$menu) {
            return [];
        }

        if ($menu instanceof Collection) {
            return $menu->values()->all();
        }

        if (is_array($menu)) {
            return array_values($menu);
        }

        if (is_iterable($menu)) {
            return collect($menu)->values()->all();
        }

        return [];
    }

    public function render(): View|Closure|string
    {
        return view('components.client.nav-bar');
    }
}
