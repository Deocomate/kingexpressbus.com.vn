<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DistrictController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $provinces = DB::table('provinces')->orderBy('name')->get();
        $district_types = DB::table('district_types')->orderBy('priority')->get();
        $districtsQuery = DB::table('districts as d')
            ->leftJoin('provinces as p', 'd.province_id', '=', 'p.id')
            ->leftJoin('district_types as dt', 'd.district_type_id', '=', 'dt.id')
            ->select('d.*', 'p.name as province_name', 'dt.name as district_type_name');
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $districtsQuery->where('d.name', 'like', "%{$searchTerm}%");
        }
        $allDistricts = $districtsQuery->orderBy('d.priority')->get();
        $districtsByProvince = $allDistricts->groupBy('province_id');
        return view('admin.districts.index', compact('provinces', 'district_types', 'districtsByProvince'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Generate slug before validation
        $request->merge(['slug' => Str::slug($request->name)]);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:districts,slug', // Validate slug uniqueness
            'province_id' => 'required|integer|exists:provinces,id',
            'district_type_id' => 'required|integer|exists:district_types,id',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'thumbnail_url' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'priority' => 'required|integer',
        ], [
            'slug.unique' => 'Tên này đã được sử dụng, vui lòng chọn tên khác.'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['created_at'] = Carbon::now();
        $data['updated_at'] = Carbon::now();

        $districtId = DB::table('districts')->insertGetId($data);

        if ($districtId) {
            return response()->json(['success' => true, 'message' => 'Thêm Quận/Huyện thành công. Vui lòng tải lại trang để xem thay đổi.']);
        }

        return response()->json(['success' => false, 'message' => 'Thêm Quận/Huyện thất bại.'], 500);
    }

    /**
     * Display the specified resource for editing.
     */
    public function show(string $id)
    {
        $district = DB::table('districts')->find($id);
        if (!$district) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy Quận/Huyện.'], 404);
        }
        return response()->json(['success' => true, 'data' => $district]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $district = DB::table('districts')->find($id);
        if (!$district) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy Quận/Huyện.'], 404);
        }

        // Generate slug before validation
        $request->merge(['slug' => Str::slug($request->name)]);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:districts,slug,' . $id, // Ignore current ID
            'province_id' => 'required|integer|exists:provinces,id',
            'district_type_id' => 'required|integer|exists:district_types,id',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'thumbnail_url' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'priority' => 'required|integer',
        ], [
            'slug.unique' => 'Tên này đã được sử dụng, vui lòng chọn tên khác.'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['updated_at'] = Carbon::now();

        DB::table('districts')->where('id', $id)->update($data);

        return response()->json(['success' => true, 'message' => 'Cập nhật Quận/Huyện thành công. Vui lòng tải lại trang để xem thay đổi.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $deleted = DB::table('districts')->where('id', $id)->delete();
        if ($deleted) {
            return response()->json(['success' => true, 'message' => 'Đã xóa Quận/Huyện thành công.']);
        }
        return response()->json(['success' => false, 'message' => 'Xóa thất bại.'], 500);
    }

    /**
     * Update province and priority from drag-and-drop.
     */
    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provinceId' => 'required|integer|exists:provinces,id',
            'order' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ.'], 400);
        }

        $provinceId = $request->input('provinceId');
        $order = $request->input('order');

        DB::transaction(function () use ($provinceId, $order) {
            foreach ($order as $index => $districtId) {
                DB::table('districts')
                    ->where('id', $districtId)
                    ->update([
                        'province_id' => $provinceId,
                        'priority' => $index,
                        'updated_at' => Carbon::now()
                    ]);
            }
        });

        return response()->json(['success' => true, 'message' => 'Cập nhật vị trí thành công.']);
    }
}
