<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DistrictTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DB::table('district_types');

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where('name', 'like', "%{$searchTerm}%");
        }

        $district_types = $query->orderBy('priority')->paginate(15)->withQueryString();
        return view('admin.district_types.index', compact('district_types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:district_types,name',
            'priority' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $id = DB::table('district_types')->insertGetId($data);

        if ($id) {
            return response()->json(['success' => true, 'message' => 'Thêm loại địa điểm thành công.']);
        }

        return response()->json(['success' => false, 'message' => 'Thêm loại địa điểm thất bại.'], 500);
    }

    /**
     * Display the specified resource for editing.
     */
    public function show(string $id)
    {
        $district_type = DB::table('district_types')->find($id);
        if (!$district_type) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy loại địa điểm.'], 404);
        }
        return response()->json(['success' => true, 'data' => $district_type]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:district_types,name,' . $id,
            'priority' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $updated = DB::table('district_types')->where('id', $id)->update($data);

        if ($updated) {
            return response()->json(['success' => true, 'message' => 'Cập nhật loại địa điểm thành công.']);
        }

        return response()->json(['success' => false, 'message' => 'Cập nhật thất bại hoặc không có gì thay đổi.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $deleted = DB::table('district_types')->where('id', $id)->delete();
        if ($deleted) {
            return response()->json(['success' => true, 'message' => 'Đã xóa loại địa điểm thành công.']);
        }
        return response()->json(['success' => false, 'message' => 'Xóa thất bại.'], 500);
    }
}
