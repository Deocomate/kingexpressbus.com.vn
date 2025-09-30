<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class RouteController extends Controller
{
    public function index()
    {
        $companyId = DB::table('companies')->where('user_id', Auth::id())->value('id');

        $startProvinces = DB::table('company_routes as cr')
            ->join('routes as r', 'cr.route_id', '=', 'r.id')
            ->join('provinces as p', 'r.province_start_id', '=', 'p.id')
            ->where('cr.company_id', $companyId)
            ->select('p.id', 'p.name')
            ->distinct()
            ->orderBy('p.name')
            ->get();

        $companyRoutesByProvince = DB::table('company_routes as cr')
            ->join('routes as r', 'cr.route_id', '=', 'r.id')
            ->join('provinces as ps', 'r.province_start_id', '=', 'ps.id')
            ->join('provinces as pe', 'r.province_end_id', '=', 'pe.id')
            ->where('cr.company_id', $companyId)
            ->select('cr.id', 'cr.name as company_route_name', 'r.province_start_id', 'pe.name as end_province_name', 'cr.priority')
            ->orderBy('cr.priority')
            ->get()
            ->groupBy('province_start_id');

        $all_global_routes = DB::table('routes as r')
            ->join('provinces as ps', 'r.province_start_id', '=', 'ps.id')
            ->join('provinces as pe', 'r.province_end_id', '=', 'pe.id')
            ->select('r.id', DB::raw("CONCAT(r.name, ' (', ps.name, ' -> ', pe.name, ')') as text"))
            ->orderBy('r.name')
            ->get();

        $all_stops = DB::table('stops as s')
            ->join('districts as d', 's.district_id', '=', 'd.id')
            ->join('provinces as p', 'd.province_id', '=', 'p.id')
            ->select('s.id', 's.name', 's.address', DB::raw("CONCAT(d.name, ', ', p.name) as location"))
            ->orderBy('p.name')->orderBy('d.name')->orderBy('s.name')
            ->get();

        return view('company.routes.index', compact('startProvinces', 'companyRoutesByProvince', 'all_global_routes', 'all_stops'));
    }

    private function processStopsData(Request $request, $companyRouteId)
    {
        DB::table('company_route_stops')->where('company_route_id', $companyRouteId)->delete();

        $stopsData = json_decode($request->input('stops_json'), true);

        if (!empty($stopsData) && is_array($stopsData)) {
            $stopsToInsert = [];
            foreach ($stopsData as $index => $stop) {
                $stopsToInsert[] = [
                    'company_route_id' => $companyRouteId,
                    'stop_id' => $stop['stop_id'],
                    'stop_type' => $stop['stop_type'],
                    'priority' => $index,
                ];
            }
            if (!empty($stopsToInsert)) {
                DB::table('company_route_stops')->insert($stopsToInsert);
            }
        }
    }

    public function store(Request $request)
    {
        $companyId = DB::table('companies')->where('user_id', Auth::id())->value('id');

        $validator = Validator::make($request->all(), [
            'route_id' => 'required|exists:routes,id',
            'name' => 'required|string|max:1000',
            'slug' => 'required|string|max:255|unique:company_routes,slug',
            'priority' => 'required|integer',
            'stops_json' => 'nullable|json',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        DB::transaction(function () use ($validatedData, $companyId, $request) {
            $companyRouteId = DB::table('company_routes')->insertGetId([
                'company_id' => $companyId,
                'route_id' => $validatedData['route_id'],
                'name' => $validatedData['name'],
                'slug' => $validatedData['slug'],
                'priority' => $validatedData['priority'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $this->processStopsData($request, $companyRouteId);
        });

        return response()->json(['success' => true, 'message' => 'Thêm tuyến đường thành công.']);
    }

    public function show($id)
    {
        $companyId = DB::table('companies')->where('user_id', Auth::id())->value('id');
        $route = DB::table('company_routes')
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->first();

        if (!$route) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy tuyến đường.'], 404);
        }

        $route->stops = DB::table('company_route_stops as crs')
            ->join('stops as s', 'crs.stop_id', '=', 's.id')
            ->join('districts as d', 's.district_id', '=', 'd.id')
            ->join('provinces as p', 'd.province_id', '=', 'p.id')
            ->where('crs.company_route_id', $id)
            ->select('s.id as stop_id', 's.name', 's.address', 'crs.stop_type', DB::raw("CONCAT(d.name, ', ', p.name) as location"))
            ->orderBy('crs.priority')
            ->get();

        return response()->json(['success' => true, 'data' => $route]);
    }

    public function update(Request $request, $id)
    {
        $companyId = DB::table('companies')->where('user_id', Auth::id())->value('id');

        $validator = Validator::make($request->all(), [
            'route_id' => 'required|exists:routes,id',
            'name' => 'required|string|max:1000',
            'slug' => 'required|string|max:255|unique:company_routes,slug,' . $id,
            'priority' => 'required|integer',
            'stops_json' => 'nullable|json',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        $validatedData = $validator->validated();

        DB::transaction(function () use ($validatedData, $id, $companyId, $request) {
            DB::table('company_routes')
                ->where('id', $id)
                ->where('company_id', $companyId)
                ->update([
                    'route_id' => $validatedData['route_id'],
                    'name' => $validatedData['name'],
                    'slug' => $validatedData['slug'],
                    'priority' => $validatedData['priority'],
                    'updated_at' => Carbon::now(),
                ]);

            $this->processStopsData($request, $id);
        });

        return response()->json(['success' => true, 'message' => 'Cập nhật tuyến đường thành công.']);
    }

    public function destroy($id)
    {
        $companyId = DB::table('companies')->where('user_id', Auth::id())->value('id');

        DB::transaction(function () use ($id, $companyId) {
            DB::table('company_route_stops')->where('company_route_id', $id)->delete();
            DB::table('company_routes')->where('id', $id)->where('company_id', $companyId)->delete();
        });

        return response()->json(['success' => true, 'message' => 'Xóa tuyến đường thành công.']);
    }

    public function all()
    {
        $companyId = DB::table('companies')->where('user_id', Auth::id())->value('id');
        $routes = DB::table('company_routes')
            ->where('company_id', $companyId)
            ->select('id', 'name as text')
            ->get();
        return response()->json($routes);
    }

    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), ['order' => 'required|array']);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ.'], 400);
        }
        DB::transaction(function () use ($request) {
            foreach ($request->input('order') as $index => $routeId) {
                DB::table('company_routes')->where('id', $routeId)->update(['priority' => $index]);
            }
        });
        return response()->json(['success' => true, 'message' => 'Cập nhật thứ tự thành công.']);
    }
}
