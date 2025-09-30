<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BusServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('bus_services');
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where('name', 'like', "%{$searchTerm}%");
        }
        $services = $query->orderBy('priority')->paginate(15)->withQueryString();
        return view('admin.bus_services.index', compact('services'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:bus_services,name',
            'icon' => 'nullable|string|max:100',
            'priority' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::table('bus_services')->insert($validator->validated());
        return response()->json(['success' => true, 'message' => 'Thêm dịch vụ thành công.']);
    }

    public function show(string $id)
    {
        $service = DB::table('bus_services')->find($id);
        if (!$service) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy dịch vụ.'], 404);
        }
        return response()->json(['success' => true, 'data' => $service]);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:bus_services,name,' . $id,
            'icon' => 'nullable|string|max:100',
            'priority' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::table('bus_services')->where('id', $id)->update($validator->validated());
        return response()->json(['success' => true, 'message' => 'Cập nhật dịch vụ thành công.']);
    }

    public function destroy(string $id)
    {
        $deleted = DB::table('bus_services')->where('id', $id)->delete();
        if ($deleted) {
            return response()->json(['success' => true, 'message' => 'Đã xóa dịch vụ thành công.']);
        }
        return response()->json(['success' => false, 'message' => 'Xóa thất bại.']);
    }
}
