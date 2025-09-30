<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class BusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = DB::table('bus_services')->orderBy('priority')->get();
        return view('company.buses.index', compact('services'));
    }

    /**
     * Provide data for the DataTables.
     */
    public function list(Request $request)
    {
        $companyId = DB::table('companies')->where('user_id', Auth::id())->value('id');

        $query = DB::table('buses')->where('company_id', $companyId);

        if ($request->filled('search.value')) {
            $searchValue = $request->search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                    ->orWhere('model_name', 'like', "%{$searchValue}%");
            });
        }

        $totalRecords = $query->count();

        $buses = $query->orderBy('priority', 'desc')
            ->skip($request->input('start', 0))
            ->take($request->input('length', 10))
            ->get();

        $data = $buses->map(function ($bus) {
            return [
                'thumbnail_url' => $bus->thumbnail_url,
                'name' => $bus->name,
                'model_name' => $bus->model_name,
                'seat_count' => $bus->seat_count,
                'priority' => $bus->priority,
                'action' => view('company.buses.partials.actions', ['bus' => $bus])->render(),
            ];
        });

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $companyId = DB::table('companies')->where('user_id', Auth::id())->value('id');

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:1000',
            'model_name' => 'nullable|string|max:1000',
            'seat_map' => 'required|json',
            'services' => 'nullable|array',
            'thumbnail_url' => 'nullable|string|max:1000',
            'image_list_url' => 'nullable|json',
            'content' => 'nullable|string',
            'priority' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        $validatedData['company_id'] = $companyId;
        $validatedData['services'] = isset($validatedData['services']) ? json_encode($validatedData['services']) : json_encode([]);

        // Auto-calculate seat_count from seat_map
        $seatMap = json_decode($validatedData['seat_map'], true);
        $validatedData['seat_count'] = is_array($seatMap) ? count($seatMap) : 0;

        $validatedData['created_at'] = Carbon::now();
        $validatedData['updated_at'] = Carbon::now();

        DB::table('buses')->insert($validatedData);

        return response()->json(['success' => true, 'message' => 'Thêm xe thành công.']);
    }

    /**
     * Display the specified resource for editing.
     */
    public function show(string $id)
    {
        $companyId = DB::table('companies')->where('user_id', Auth::id())->value('id');
        $bus = DB::table('buses')->where('id', $id)->where('company_id', $companyId)->first();

        if (!$bus) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy xe.'], 404);
        }

        // Decode services for easier handling on the frontend
        $bus->services = json_decode($bus->services, true) ?? [];

        return response()->json(['success' => true, 'data' => $bus]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $companyId = DB::table('companies')->where('user_id', Auth::id())->value('id');

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:1000',
            'model_name' => 'nullable|string|max:1000',
            'seat_map' => 'required|json',
            'services' => 'nullable|array',
            'thumbnail_url' => 'nullable|string|max:1000',
            'image_list_url' => 'nullable|json',
            'content' => 'nullable|string',
            'priority' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        $validatedData['services'] = isset($validatedData['services']) ? json_encode($validatedData['services']) : json_encode([]);

        // Auto-calculate seat_count from seat_map
        $seatMap = json_decode($validatedData['seat_map'], true);
        $validatedData['seat_count'] = is_array($seatMap) ? count($seatMap) : 0;

        $validatedData['updated_at'] = Carbon::now();

        $updated = DB::table('buses')
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->update($validatedData);

        if ($updated) {
            return response()->json(['success' => true, 'message' => 'Cập nhật thông tin xe thành công.']);
        }

        return response()->json(['success' => false, 'message' => 'Cập nhật thất bại hoặc không có gì thay đổi.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $companyId = DB::table('companies')->where('user_id', Auth::id())->value('id');

        $deleted = DB::table('buses')
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->delete();

        if ($deleted) {
            return response()->json(['success' => true, 'message' => 'Đã xóa xe thành công.']);
        }

        return response()->json(['success' => false, 'message' => 'Xóa thất bại hoặc xe không tồn tại.']);
    }

    /**
     * Get all buses for select options.
     */
    public function all()
    {
        $companyId = DB::table('companies')->where('user_id', Auth::id())->value('id');
        $buses = DB::table('buses')
            ->where('company_id', $companyId)
            ->select('id', DB::raw("CONCAT(name, ' - ', model_name) as text"))
            ->get();
        return response()->json($buses);
    }
}
