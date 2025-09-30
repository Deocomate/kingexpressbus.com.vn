<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function index()
    {
        $menus = DB::table('menus')->orderBy('parent_id')->orderBy('priority')->get();
        $menuTree = $this->buildMenuTree($menus);

        // Sử dụng url() helper để tạo đường dẫn tuyệt đối, an toàn hơn
        $systemPages = [
            ['name' => 'Trang chủ', 'url' => url('/')],
            ['name' => 'Giới thiệu', 'url' => url('/gioi-thieu')],
            ['name' => 'Liên hệ', 'url' => url('/lien-he')],
        ];

        $routes = DB::table('routes as r')
            ->join('provinces as ps', 'r.province_start_id', '=', 'ps.id')
            ->join('provinces as pe', 'r.province_end_id', '=', 'pe.id')
            ->select('r.id', 'r.name as title', 'ps.name as start_province', 'pe.name as end_province', 'r.slug')
            ->orderBy('r.name')
            ->get();

        return view('admin.menus.index', compact('menuTree', 'systemPages', 'routes'));
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

    public function addItem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|in:custom_link,system_page,route',
            'name' => 'required_if:type,custom_link,system_page|string|max:255',
            'url' => 'required_if:type,custom_link,system_page|string|max:255',
            'related_id' => 'required_if:type,route|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $data = $validator->validated();
        $menuData = [
            'type' => $data['type'],
            'priority' => 999, // Tạm thời set priority cao để nằm cuối, sau đó sẽ được sắp xếp lại
        ];

        switch ($data['type']) {
            case 'route':
                $route = DB::table('routes')->find($data['related_id']);
                if (!$route) {
                    return response()->json(['success' => false, 'message' => 'Tuyến đường không tồn tại.']);
                }
                $menuData['name'] = $route->name;
                // FIX: Tạo URL thủ công thay vì dùng route() helper với tên không tồn tại
                $menuData['url'] = url('/tuyen-duong/' . $route->slug);
                $menuData['related_id'] = $route->id;
                break;
            default:
                $menuData['name'] = $data['name'];
                $menuData['url'] = $data['url'];
                break;
        }

        $id = DB::table('menus')->insertGetId($menuData);
        $item = DB::table('menus')->find($id);
        $item->children = [];

        $html = view('admin.menus.menu_item', compact('item'))->render();

        return response()->json(['success' => true, 'html' => $html]);
    }

    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'menuData' => 'required|array',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ.'], 400);
        }
        DB::beginTransaction();
        try {
            $this->updateMenuItemOrder($request->input('menuData'));
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Cập nhật cấu trúc menu thành công!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()], 500);
        }
    }

    private function updateMenuItemOrder(array $menuItems, $parentId = null)
    {
        foreach ($menuItems as $priority => $item) {
            DB::table('menus')
                ->where('id', $item['id'])
                ->update([
                    'priority' => $priority,
                    'parent_id' => $parentId,
                    'updated_at' => now(),
                ]);

            if (!empty($item['children'])) {
                $this->updateMenuItemOrder($item['children'], $item['id']);
            }
        }
    }

    public function edit($id)
    {
        $menu = DB::table('menus')->find($id);
        if (!$menu) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy mục menu.'], 404);
        }
        return response()->json(['success' => true, 'data' => $menu]);
    }

    public function update(Request $request, $id)
    {
        $menu = DB::table('menus')->find($id);
        if (!$menu) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy mục menu.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'url' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $dataToUpdate = [
            'name' => $request->input('name'),
            'updated_at' => now(),
        ];

        if ($menu->type === 'custom_link') {
            $dataToUpdate['url'] = $request->input('url');
        }

        DB::table('menus')->where('id', $id)->update($dataToUpdate);
        $updatedMenu = DB::table('menus')->find($id);

        return response()->json(['success' => true, 'message' => 'Cập nhật thành công!', 'data' => $updatedMenu]);
    }

    public function destroy($id)
    {
        $deleted = DB::table('menus')->where('id', $id)->delete();
        if ($deleted) {
            return response()->json(['success' => true, 'message' => 'Đã xóa mục menu thành công.']);
        }
        return response()->json(['success' => false, 'message' => 'Không tìm thấy hoặc không thể xóa mục menu.'], 404);
    }
}

