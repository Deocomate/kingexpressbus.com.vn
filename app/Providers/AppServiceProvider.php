<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Collection;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (Schema::hasTable('web_profiles') && Schema::hasTable('menus')) {
            View::composer(['layouts.client.*', 'client.*'], function ($view) {
                $web_profile = DB::table('web_profiles')->where('is_default', true)->first();
                $menuItems = DB::table('menus')->orderBy('parent_id')->orderBy('priority')->get();
                $mainMenu = $this->buildMenuTree($menuItems);
                $view->with(compact('web_profile', 'mainMenu'));
            });
        }
    }

    private function buildMenuTree($menus, $parentId = null): array
    {
        $branch = [];
        foreach ($menus as $menu) {
            if ($menu->parent_id == $parentId) {
                $children = $this->buildMenuTree($menus, $menu->id);
                if ($children) {
                    $menu->children = $children;
                }
                $branch[] = $menu;
            }
        }
        return $branch;
    }
}
