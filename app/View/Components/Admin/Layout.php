<?php

namespace App\View\Components\Admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;

class Layout extends Component
{
    public ?object $webProfile;
    public ?string $title;
    public ?string $bodyClass;
    public $authUser;
    public array $menuItems;

    /**
     * Create a new component instance.
     */
    public function __construct(
        ?string $title = null,
        ?string $bodyClass = null
    )
    {
        $this->title = $title;
        $this->bodyClass = $bodyClass ?? 'hold-transition sidebar-mini';
        $this->webProfile = $this->resolveWebProfile();
        $this->authUser = auth()->user();
        $this->menuItems = $this->resolveMenuItems();
    }

    /**
     * Resolve web profile data
     */
    protected function resolveWebProfile(): ?object
    {
        if (!Schema::hasTable('web_profiles')) {
            return null;
        }

        return DB::table('web_profiles')
            ->where('is_default', true)
            ->first();
    }

    /**
     * Resolve menu items based on user role
     */
    protected function resolveMenuItems(): array
    {
        // Menu sẽ được render trong blade template
        // Vì logic menu phụ thuộc vào role và routes
        return [];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.layout');
    }
}
