<?php

namespace App\View\Components\Client;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;
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
        // Dữ liệu $mainMenu truyền vào đã là một cây hoàn chỉnh
        // Chúng ta sẽ xử lý nó để thêm trạng thái active
        $this->mainMenu = $this->processMenuItems($mainMenu);
        $this->authUser = $authUser;
        $this->customerLinks = $customerLinks;
    }

    /**
     * Duyệt qua cây menu đã có và thêm trạng thái active.
     * Đây là hàm đệ quy để xử lý các menu con.
     */
    protected function processMenuItems(array $menuItems): array
    {
        foreach ($menuItems as $item) {
            // Chuyển đổi stdClass thành object có thể thêm thuộc tính
            $item = (object)$item;

            // Xử lý các mục con trước (post-order traversal)
            $children = !empty($item->children) ? $this->processMenuItems((array)$item->children) : [];

            // Khởi tạo trạng thái
            $item->isActive = false;
            $item->isParentOfActive = false;

            $path = ltrim(parse_url($item->url ?? '', PHP_URL_PATH), '/');

            if (!empty($children)) {
                // --- Xử lý cho mục menu CÓ con ---
                $item->children = $children;

                // Kiểm tra xem có mục con nào đang active không
                if (collect($children)->some(fn($child) => $child->isActive || $child->isParentOfActive)) {
                    $item->isParentOfActive = true;
                }

                // Mục cha chỉ active nếu URL của chính nó khớp và URL đó không phải là trang chủ
                if ($path !== '' && Request::is($path)) {
                    $item->isActive = true;
                }
            } else {
                // --- Xử lý cho mục menu KHÔNG có con ---
                // Chỉ active khi URL khớp chính xác (bao gồm cả trường hợp trang chủ "/")
                if (($path === '' && Request::is('/')) || ($path !== '' && Request::is($path))) {
                    $item->isActive = true;
                }
            }
        }
        return $menuItems;
    }

    public function render(): View|Closure|string
    {
        return view('components.client.nav-bar');
    }
}
