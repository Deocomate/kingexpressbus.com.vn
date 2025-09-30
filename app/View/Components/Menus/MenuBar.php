<?php

namespace App\View\Components\Menus;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\View\Component;

class MenuBar extends Component
{
    public bool $isActive = false;

    /**
     * Create a new component instance.
     *
     * @param string $route
     * @param string $icon
     * @param string $name
     * @param array $routeGroup Mảng các mẫu route để kiểm tra active
     */
    public function __construct(
        public string $route = '#',
        public string $icon = '',
        public string $name = '',
        public array $routeGroup = []
    ) {
        // Logic tính toán trạng thái active được chuyển vào đây
        foreach ($this->routeGroup as $pattern) {
            if (request()->routeIs($pattern)) {
                $this->isActive = true;
                break; // Thoát vòng lặp ngay khi tìm thấy một mẫu khớp
            }
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.menus.menu-bar');
    }
}
