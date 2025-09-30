<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index()
    {
        return view('company.bookings.index');
    }

    public function list(Request $request)
    {
        $companyId = DB::table('companies')->where('user_id', Auth::id())->value('id');

        $query = DB::table('bookings as b')
            ->join('bus_routes as br', 'b.bus_route_id', '=', 'br.id')
            ->join('company_routes as cr', 'br.company_route_id', '=', 'cr.id')
            ->where('cr.company_id', $companyId)
            ->select(
                'b.id',
                'b.booking_code',
                'b.customer_name',
                'b.customer_phone',
                'b.booking_date',
                'b.total_price',
                'b.status',
                'cr.name as company_route_name'
            );

        if ($request->filled('search.value')) {
            $searchValue = $request->search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('b.booking_code', 'like', "%{$searchValue}%")
                  ->orWhere('b.customer_name', 'like', "%{$searchValue}%")
                  ->orWhere('b.customer_phone', 'like', "%{$searchValue}%");
            });
        }

        $totalRecords = $query->count();

        $bookings = $query->orderBy('b.created_at', 'desc')
            ->skip($request->input('start', 0))
            ->take($request->input('length', 10))
            ->get();

        $data = $bookings->map(function($booking) {
            $actions = '<div class="btn-group">
                            <button class="btn btn-primary btn-xs view-btn" data-id="'. $booking->id .'">
                                <i class="fas fa-folder"></i> Xem
                            </button>
                            <button class="btn btn-info btn-xs update-btn" data-id="'. $booking->id .'">
                                <i class="fas fa-pencil-alt"></i> Cập nhật
                            </button>
                        </div>';

            return [
                'booking_code' => $booking->booking_code,
                'customer_info' => $booking->customer_name . '<br><small>' . $booking->customer_phone . '</small>',
                'company_route_name' => $booking->company_route_name,
                'booking_date' => Carbon::parse($booking->booking_date)->format('d/m/Y'),
                'total_price' => number_format($booking->total_price) . 'đ',
                'status' => $this->getStatusBadge($booking->status),
                'action' => $actions
            ];
        });

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data,
        ]);
    }

    private function getStatusBadge($status)
    {
        $statusClasses = [
            'pending' => 'badge-warning',
            'confirmed' => 'badge-success',
            'cancelled' => 'badge-danger',
            'completed' => 'badge-primary',
        ];
        $statusTexts = [
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'cancelled' => 'Đã hủy',
            'completed' => 'Hoàn thành',
        ];

        return '<span class="badge ' . ($statusClasses[$status] ?? 'badge-secondary') . '">' . ($statusTexts[$status] ?? ucfirst($status)) . '</span>';
    }


    public function show($id)
    {
        $companyId = DB::table('companies')->where('user_id', Auth::id())->value('id');

        $booking = DB::table('bookings as b')
            ->join('bus_routes as br', 'b.bus_route_id', '=', 'br.id')
            ->join('company_routes as cr', 'br.company_route_id', '=', 'cr.id')
            ->join('buses', 'br.bus_id', '=', 'buses.id')
            ->join('stops as pickup_stop', 'b.pickup_stop_id', '=', 'pickup_stop.id')
            ->join('stops as dropoff_stop', 'b.dropoff_stop_id', '=', 'dropoff_stop.id')
            ->where('cr.company_id', $companyId)
            ->where('b.id', $id)
            ->select(
                'b.*',
                'cr.name as company_route_name',
                'buses.name as bus_name',
                'br.start_time',
                'br.end_time',
                'pickup_stop.name as pickup_stop_name',
                'pickup_stop.address as pickup_stop_address',
                'dropoff_stop.name as dropoff_stop_name',
                'dropoff_stop.address as dropoff_stop_address'
            )
            ->first();

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy đặt vé.'], 404);
        }
        return response()->json(['success' => true, 'data' => $booking]);
    }

    public function updateStatus(Request $request, $id)
    {
         $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,confirmed,cancelled,completed',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $companyId = DB::table('companies')->where('user_id', Auth::id())->value('id');

        $booking = DB::table('bookings as b')
            ->join('bus_routes as br', 'b.bus_route_id', '=', 'br.id')
            ->join('company_routes as cr', 'br.company_route_id', '=', 'cr.id')
            ->where('cr.company_id', $companyId)
            ->where('b.id', $id)
            ->select('b.id')
            ->first();

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy đặt vé hoặc bạn không có quyền.'], 404);
        }

        DB::table('bookings')->where('id', $id)->update([
            'status' => $request->input('status'),
            'updated_at' => Carbon::now()
        ]);

        return response()->json(['success' => true, 'message' => 'Cập nhật trạng thái đặt vé thành công.']);
    }
}

