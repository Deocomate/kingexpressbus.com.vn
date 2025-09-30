<?php

namespace App\View\Components\Client;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\Component;

class Layout extends Component
{
    public ?object $webProfile;

    public array $mainMenu;

    public ?string $title;

    public ?string $description;

    public ?string $favicon;

    public string $bodyClass;

    public function __construct(
        $webProfile = null,
        $mainMenu = [],
        ?string $title = null,
        ?string $description = null,
        ?string $favicon = null,
        ?string $bodyClass = null
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->favicon = $favicon;
        $this->bodyClass = $bodyClass ?? 'bg-gray-50';

        $this->webProfile = $webProfile ?: $this->resolveWebProfile();
        $this->mainMenu = $this->normalizeMenu($mainMenu);

        if (empty($this->mainMenu)) {
            $this->mainMenu = $this->resolveMainMenu();
        }
    }

    protected function resolveWebProfile(): ?object
    {
        if (!Schema::hasTable('web_profiles')) {
            return null;
        }

        return DB::table('web_profiles')
            ->where('is_default', true)
            ->first();
    }

    protected function resolveMainMenu(): array
    {
        if (!Schema::hasTable('menus')) {
            return [];
        }

        $menus = DB::table('menus')
            ->orderBy('parent_id')
            ->orderBy('priority')
            ->get();

        return $this->buildMenuTree($menus);
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

    protected function buildMenuTree($menus, $parentId = null): array
    {
        $branch = [];

        foreach ($menus as $menu) {
            $currentParentId = $menu->parent_id ?? null;
            if ($currentParentId == $parentId) {
                $children = $this->buildMenuTree($menus, $menu->id);
                if (!empty($children)) {
                    $menu->children = $children;
                }
                $branch[] = $menu;
            }
        }

        return $branch;
    }

    public function render(): View|Closure|string
    {
        return view('components.client.layout');
    }
}
