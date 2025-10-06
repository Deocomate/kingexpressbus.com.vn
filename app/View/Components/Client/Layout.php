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
    public $authUser;
    public array $customerLinks;

    public function __construct(
        ?string $title = null,
        ?string $description = null,
        ?string $favicon = null,
        ?string $bodyClass = null
    )
    {
        $this->title = $title;
        $this->description = $description;
        $this->favicon = $favicon;
        $this->bodyClass = $bodyClass ?? 'bg-gray-50';

        $this->webProfile = $this->resolveWebProfile();
        $this->mainMenu = $this->resolveMainMenu();
        $this->authUser = auth()->user();
        $this->customerLinks = $this->resolveCustomerLinks();
    }

    protected function resolveWebProfile(): ?object
    {
        if (!Schema::hasTable('web_profiles')) {
            return null;
        }
        return DB::table('web_profiles')->where('is_default', true)->first();
    }

    protected function resolveCustomerLinks(): array
    {
        if ($this->authUser && ($this->authUser->role ?? null) === 'customer') {
            return [
                [
                    'label' => __('client.layout.profile'),
                    'url' => route('client.profile.index'),
                    'icon' => 'fa-solid fa-user',
                ],
                [
                    'label' => __('client.layout.my_bookings'),
                    'url' => route('client.profile.index') . '#history',
                    'icon' => 'fa-solid fa-ticket',
                ],
            ];
        }
        return [];
    }

    protected function resolveMainMenu(): array
    {
        if (!Schema::hasTable('menus')) {
            return [];
        }

        $staticMenuItems = [
            'home' => (object)[
                'id' => 'static_home', 'name' => __('client.menu.home'), 'url' => url('/'),
                'parent_id' => null, 'children' => []
            ],
            'about' => (object)[
                'id' => 'static_about', 'name' => __('client.menu.about'), 'url' => url('/gioi-thieu'),
                'parent_id' => null, 'children' => []
            ],
            'contact' => (object)[
                'id' => 'static_contact', 'name' => __('client.menu.contact'), 'url' => url('/lien-he'),
                'parent_id' => null, 'children' => []
            ],
        ];

        $staticUrls = [url('/'), url('/gioi-thieu'), url('/lien-he')];

        $dbMenuItems = DB::table('menus')->orderBy('parent_id')->orderBy('priority')->get()
            ->filter(function ($item) use ($staticUrls) {
                return !in_array(url($item->url), $staticUrls, true);
            });

        $dynamicMenuTree = $this->buildMenuTree($dbMenuItems);

        return [
            $staticMenuItems['home'],
            $staticMenuItems['about'],
            ...$dynamicMenuTree,
            $staticMenuItems['contact']
        ];
    }

    protected function buildMenuTree(Collection $menus, $parentId = null): array
    {
        $branch = [];
        $items = $menus->where('parent_id', $parentId)->sortBy('priority');

        foreach ($items as $item) {
            $item->name = __($item->name);
            $children = $this->buildMenuTree($menus, $item->id);
            if (!empty($children)) {
                $item->children = $children;
            }
            $branch[] = $item;
        }
        return $branch;
    }

    public function render(): View|Closure|string
    {
        return view('components.client.layout');
    }
}
