<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ProvinceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DB::table('provinces');

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where('name', 'like', "%{$searchTerm}%");
        }

        $provinces = $query->orderByDesc('priority')->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('admin.provinces.index', compact('provinces'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:provinces,name',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'thumbnail_url' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'priority' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['slug'] = Str::slug($data['name']);
        $data['created_at'] = Carbon::now();
        $data['updated_at'] = Carbon::now();

        $provinceId = DB::table('provinces')->insertGetId($data);

        if ($provinceId) {
            $newProvince = DB::table('provinces')->where('id', $provinceId)->first();
            return response()->json(['success' => true, 'message' => 'Thêm Tỉnh/Thành phố thành công.', 'data' => $newProvince]);
        }

        return response()->json(['success' => false, 'message' => 'Thêm Tỉnh/Thành phố thất bại.'], 500);
    }

    /**
     * Display the specified resource for editing.
     */
    public function show(string $id)
    {
        $province = DB::table('provinces')->find($id);
        if (!$province) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy Tỉnh/Thành phố.'], 404);
        }
        return response()->json(['success' => true, 'data' => $province]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $province = DB::table('provinces')->find($id);
        if (!$province) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy Tỉnh/Thành phố.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:provinces,name,' . $id,
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'thumbnail_url' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'priority' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['slug'] = Str::slug($data['name']);
        $data['updated_at'] = Carbon::now();

        $updated = DB::table('provinces')->where('id', $id)->update($data);

        if ($updated) {
            $updatedProvince = DB::table('provinces')->where('id', $id)->first();
            return response()->json(['success' => true, 'message' => 'Cập nhật Tỉnh/Thành phố thành công.', 'data' => $updatedProvince]);
        }

        return response()->json(['success' => false, 'message' => 'Cập nhật thất bại hoặc không có gì thay đổi.'], 500);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $province = DB::table('provinces')->find($id);
        if (!$province) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy Tỉnh/Thành phố.'], 404);
        }

        $deleted = DB::table('provinces')->where('id', $id)->delete();

        if ($deleted) {
            return response()->json(['success' => true, 'message' => 'Đã xóa Tỉnh/Thành phố thành công.']);
        }

        return response()->json(['success' => false, 'message' => 'Xóa thất bại, vui lòng thử lại.'], 500);
    }

    // Unused methods as per single-page application design
    public function create() {}
    public function edit($province) {}
}
