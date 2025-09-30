<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RouteController extends Controller
{
    public function index()
    {
        // Lấy danh sách các tỉnh có tuyến đường bắt đầu từ đó
        $startProvinces = DB::table('routes as r')
            ->join('provinces as p', 'r.province_start_id', '=', 'p.id')
            ->select('p.id', 'p.name')
            ->distinct()
            ->orderBy('p.name')
            ->get();

        // Lấy tất cả các tuyến đường và thông tin tỉnh bắt đầu/kết thúc
        $routes = DB::table('routes as r')
            ->join('provinces as ps', 'r.province_start_id', '=', 'ps.id')
            ->join('provinces as pe', 'r.province_end_id', '=', 'pe.id')
            ->select('r.*', 'ps.name as start_province_name', 'pe.name as end_province_name')
            ->orderBy('r.priority')
            ->get()
            ->groupBy('province_start_id');

        // Lấy tất cả tỉnh cho modal
        $all_provinces_for_modal = DB::table('provinces')->orderBy('name')->get();

        return view('admin.routes.index', compact('startProvinces', 'routes', 'all_provinces_for_modal'));
    }

    public function store(Request $request)
    {
        $request->merge(['slug' => Str::slug($request->name)]);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:routes,slug',
            'province_start_id' => 'required|integer|exists:provinces,id',
            'province_end_id' => 'required|integer|exists:provinces,id|different:province_start_id',
            'duration' => 'nullable|string|max:100',
            'distance_km' => 'nullable|integer',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'thumbnail_url' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'priority' => 'required|integer',
        ], [
            'province_end_id.different' => 'Tỉnh đến phải khác Tỉnh đi.',
            'slug.unique' => 'Tên này đã được sử dụng, vui lòng chọn tên khác.'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['created_at'] = Carbon::now();
        $data['updated_at'] = Carbon::now();

        $id = DB::table('routes')->insertGetId($data);

        if ($id) {
            return response()->json(['success' => true, 'message' => 'Thêm tuyến đường thành công. Vui lòng tải lại trang.']);
        }
        return response()->json(['success' => false, 'message' => 'Thêm tuyến đường thất bại.'], 500);
    }

    public function show($id)
    {
        $route = DB::table('routes')->find($id);
        if (!$route) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy tuyến đường.'], 404);
        }
        return response()->json(['success' => true, 'data' => $route]);
    }

    public function update(Request $request, $id)
    {
        $request->merge(['slug' => Str::slug($request->name)]);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:routes,slug,' . $id,
            'province_start_id' => 'required|integer|exists:provinces,id',
            'province_end_id' => 'required|integer|exists:provinces,id|different:province_start_id',
            'duration' => 'nullable|string|max:100',
            'distance_km' => 'nullable|integer',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'thumbnail_url' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'priority' => 'required|integer',
        ], [
            'province_end_id.different' => 'Tỉnh đến phải khác Tỉnh đi.',
            'slug.unique' => 'Tên này đã được sử dụng, vui lòng chọn tên khác.'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['updated_at'] = Carbon::now();

        DB::table('routes')->where('id', $id)->update($data);
        return response()->json(['success' => true, 'message' => 'Cập nhật tuyến đường thành công. Vui lòng tải lại trang.']);
    }

    public function destroy($id)
    {
        $deleted = DB::table('routes')->where('id', $id)->delete();
        if ($deleted) {
            return response()->json(['success' => true, 'message' => 'Đã xóa tuyến đường thành công.']);
        }
        return response()->json(['success' => false, 'message' => 'Xóa thất bại.'], 500);
    }

    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), ['order' => 'required|array']);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ.'], 400);
        }

        DB::transaction(function () use ($request) {
            foreach ($request->input('order') as $index => $routeId) {
                DB::table('routes')->where('id', $routeId)->update(['priority' => $index]);
            }
        });

        return response()->json(['success' => true, 'message' => 'Cập nhật thứ tự thành công.']);
    }
}
