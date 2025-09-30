<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StopController extends Controller
{
    public function index()
    {
        // Lấy dữ liệu theo cấu trúc phân cấp
        $provinces = DB::table('provinces')->orderBy('name')->get();
        $districts = DB::table('districts')->orderBy('priority')->get()->groupBy('province_id');
        $stops = DB::table('stops')->orderBy('priority')->get()->groupBy('district_id');

        // Lấy toàn bộ quận huyện cho modal
        $all_districts_for_modal = DB::table('districts')->orderBy('name')->get();

        return view('admin.stops.index', compact('provinces', 'districts', 'stops', 'all_districts_for_modal'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'district_id' => 'required|integer|exists:districts,id',
            'priority' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['created_at'] = now();
        $data['updated_at'] = now();

        $id = DB::table('stops')->insertGetId($data);

        if ($id) {
            return response()->json(['success' => true, 'message' => 'Thêm điểm dừng thành công. Vui lòng tải lại trang.']);
        }

        return response()->json(['success' => false, 'message' => 'Thêm điểm dừng thất bại.'], 500);
    }

    public function show(string $id)
    {
        $stop = DB::table('stops')->find($id);
        if (!$stop) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy điểm dừng.'], 404);
        }
        return response()->json(['success' => true, 'data' => $stop]);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'district_id' => 'required|integer|exists:districts,id',
            'priority' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['updated_at'] = now();

        $updated = DB::table('stops')->where('id', $id)->update($data);

        if ($updated) {
            return response()->json(['success' => true, 'message' => 'Cập nhật điểm dừng thành công. Vui lòng tải lại trang.']);
        }

        return response()->json(['success' => false, 'message' => 'Cập nhật thất bại hoặc không có gì thay đổi.']);
    }

    public function destroy(string $id)
    {
        $deleted = DB::table('stops')->where('id', $id)->delete();
        if ($deleted) {
            return response()->json(['success' => true, 'message' => 'Đã xóa điểm dừng thành công.']);
        }
        return response()->json(['success' => false, 'message' => 'Xóa thất bại.'], 500);
    }

    /**
     * Update district and priority from drag-and-drop.
     */
    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'districtId' => 'required|integer|exists:districts,id',
            'order' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ.'], 400);
        }

        $districtId = $request->input('districtId');
        $order = $request->input('order');

        DB::transaction(function () use ($districtId, $order) {
            foreach ($order as $index => $stopId) {
                DB::table('stops')
                    ->where('id', $stopId)
                    ->update([
                        'district_id' => $districtId,
                        'priority' => $index,
                        'updated_at' => now()
                    ]);
            }
        });

        return response()->json(['success' => true, 'message' => 'Cập nhật vị trí điểm dừng thành công.']);
    }
}
