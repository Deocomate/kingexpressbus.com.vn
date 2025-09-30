<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class BusRouteController extends Controller
{
    public function index()
    {
        $companyId = DB::table('companies')->where('user_id', Auth::id())->value('id');

        // 1. Lấy danh sách tất cả xe của nhà xe (cột bên trái)
        $buses = DB::table('buses')
            ->where('company_id', $companyId)
            ->select('id', 'name', 'model_name', 'seat_count')
            ->orderBy('priority', 'desc')
            ->get();

        // 2. Lấy danh sách các tỉnh có tuyến đường mà nhà xe này hoạt động
        $startProvinces = DB::table('company_routes as cr')
            ->join('routes as r', 'cr.route_id', '=', 'r.id')
            ->join('provinces as p', 'r.province_start_id', '=', 'p.id')
            ->where('cr.company_id', $companyId)
            ->select('p.id', 'p.name')
            ->distinct()
            ->orderBy('p.name')
            ->get();

        // 3. Lấy tất cả các tuyến đường của nhà xe
        $companyRoutes = DB::table('company_routes as cr')
            ->join('routes as r', 'cr.route_id', '=', 'r.id')
            ->where('cr.company_id', $companyId)
            ->select('cr.id', 'cr.name', 'r.province_start_id')
            ->orderBy('cr.priority')
            ->get();

        // 4. Lấy tất cả các chuyến xe đã được tạo và gom nhóm theo company_route_id
        $busRoutes = DB::table('bus_routes as br')
            ->join('buses as b', 'br.bus_id', '=', 'b.id')
            ->join('company_routes as cr', 'br.company_route_id', '=', 'cr.id')
            ->where('cr.company_id', $companyId)
            ->select('br.*', 'b.name as bus_name', 'b.model_name as bus_model_name')
            ->orderBy('br.priority')
            ->get()
            ->groupBy('company_route_id');


        // 5. Gom nhóm các tuyến đường theo tỉnh xuất phát
        $companyRoutesByProvince = $companyRoutes->groupBy('province_start_id');

        return view('company.bus_routes.index', compact(
            'buses',
            'startProvinces',
            'companyRoutesByProvince',
            'busRoutes'
        ));
    }

    public function store(Request $request)
    {
        // Clean price format before validation
        $request->merge(['price' => str_replace(',', '', $request->price)]);

        $validator = Validator::make($request->all(), [
            'bus_id' => 'required|exists:buses,id',
            'company_route_id' => 'required|exists:company_routes,id',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'price' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        $validatedData['priority'] = 999; // Add to the end by default
        $validatedData['created_at'] = Carbon::now();
        $validatedData['updated_at'] = Carbon::now();

        $id = DB::table('bus_routes')->insertGetId($validatedData);
        $newBusRoute = DB::table('bus_routes as br')
            ->join('buses as b', 'br.bus_id', '=', 'b.id')
            ->where('br.id', $id)
            ->select('br.*', 'b.name as bus_name', 'b.model_name as bus_model_name')
            ->first();

        return response()->json(['success' => true, 'message' => 'Tạo chuyến xe thành công.', 'data' => $newBusRoute]);
    }

    public function show($id)
    {
        $busRoute = DB::table('bus_routes')->find($id);
        if (!$busRoute) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy chuyến xe.'], 404);
        }
        return response()->json(['success' => true, 'data' => $busRoute]);
    }


    public function update(Request $request, $id)
    {
        // Clean price format before validation
        $request->merge(['price' => str_replace(',', '', $request->price)]);

        $validator = Validator::make($request->all(), [
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'price' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        $validatedData = $validator->validated();
        $validatedData['updated_at'] = Carbon::now();

        DB::table('bus_routes')->where('id', $id)->update($validatedData);

        $updatedBusRoute = DB::table('bus_routes as br')
            ->join('buses as b', 'br.bus_id', '=', 'b.id')
            ->where('br.id', $id)
            ->select('br.*', 'b.name as bus_name', 'b.model_name as bus_model_name')
            ->first();

        return response()->json(['success' => true, 'message' => 'Cập nhật chuyến xe thành công.', 'data' => $updatedBusRoute]);
    }


    public function destroy($id)
    {
        $deleted = DB::table('bus_routes')->where('id', $id)->delete();
        if ($deleted) {
            return response()->json(['success' => true, 'message' => 'Xóa chuyến xe thành công.']);
        }
        return response()->json(['success' => false, 'message' => 'Xóa thất bại.']);
    }

    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order' => 'required|array',
            'company_route_id' => 'required|integer|exists:company_routes,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ.'], 400);
        }

        DB::transaction(function () use ($request) {
            foreach ($request->input('order') as $index => $busRouteId) {
                DB::table('bus_routes')
                    ->where('id', $busRouteId)
                    ->update([
                        'priority' => $index,
                        'company_route_id' => $request->input('company_route_id') // Update route id in case of moving
                    ]);
            }
        });

        return response()->json(['success' => true, 'message' => 'Cập nhật thứ tự chuyến xe thành công.']);
    }
}

