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
        $this->mainMenu = $this->buildMenuTree(collect($mainMenu));
        $this->authUser = $authUser;
        $this->customerLinks = $customerLinks;
    }

    protected function buildMenuTree(Collection $menuItems, $parentId = null): array
    {
        $branch = [];

        $items = $menuItems->where('parent_id', $parentId)->sortBy('priority')->values();

        foreach ($items as $item) {
            // Dịch tên của mục menu
            $item->name = __($item->name);

            $children = $this->buildMenuTree($menuItems, $item->id);
            if (!empty($children)) {
                $item->children = $children;
            }
            $branch[] = $item;
        }

        return $branch;
    }

    public function render(): View|Closure|string
    {
        return view('components.client.nav-bar');
    }
}
