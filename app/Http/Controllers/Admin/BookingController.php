<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DB::table('bookings as b')
            ->join('bus_routes as br', 'b.bus_route_id', '=', 'br.id')
            ->join('company_routes as cr', 'br.company_route_id', '=', 'cr.id')
            ->join('routes as r', 'cr.route_id', '=', 'r.id')
            ->select(
                'b.id',
                'b.booking_code',
                'b.customer_name',
                'b.customer_phone',
                'b.booking_date',
                'b.total_price',
                'b.status',
                'r.name as route_name'
            );

        // Filtering logic
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('b.booking_code', 'like', "%{$searchTerm}%")
                  ->orWhere('b.customer_name', 'like', "%{$searchTerm}%")
                  ->orWhere('b.customer_phone', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->filled('status') && $request->status != 'all') {
            $query->where('b.status', $request->input('status'));
        }

        if ($request->filled('start_date')) {
            $query->whereDate('b.booking_date', '>=', Carbon::parse($request->input('start_date')));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('b.booking_date', '<=', Carbon::parse($request->input('end_date')));
        }

        $bookings = $query->orderByDesc('b.created_at')->paginate(15)->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $booking = DB::table('bookings as b')
            ->join('bus_routes as br', 'b.bus_route_id', '=', 'br.id')
            ->join('company_routes as cr', 'br.company_route_id', '=', 'cr.id')
            ->join('routes as r', 'cr.route_id', '=', 'r.id')
            ->join('companies as c', 'cr.company_id', '=', 'c.id')
            ->join('buses', 'br.bus_id', '=', 'buses.id')
            ->join('stops as pickup_stop', 'b.pickup_stop_id', '=', 'pickup_stop.id')
            ->join('stops as dropoff_stop', 'b.dropoff_stop_id', '=', 'dropoff_stop.id')
            ->select(
                'b.*',
                'r.name as route_name',
                'c.name as company_name',
                'buses.name as bus_name',
                'br.start_time',
                'br.end_time',
                'pickup_stop.name as pickup_stop_name',
                'pickup_stop.address as pickup_stop_address',
                'dropoff_stop.name as dropoff_stop_name',
                'dropoff_stop.address as dropoff_stop_address'
            )
            ->where('b.id', $id)
            ->first();

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy đặt vé.'], 404);
        }

        return response()->json(['success' => true, 'data' => $booking]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // This is handled by the show() method and a modal in the frontend.
        // Kept for RESTful compliance.
        return $this->show($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,confirmed,cancelled,completed',
            'payment_status' => 'required|in:unpaid,paid',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $booking = DB::table('bookings')->where('id', $id)->first();
        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy đặt vé.'], 404);
        }

        $updated = DB::table('bookings')
            ->where('id', $id)
            ->update([
                'status' => $request->input('status'),
                'payment_status' => $request->input('payment_status'),
                'notes' => $request->input('notes'),
                'updated_at' => Carbon::now(),
            ]);

        if ($updated) {
            return response()->json(['success' => true, 'message' => 'Cập nhật đặt vé thành công.']);
        }

        return response()->json(['success' => false, 'message' => 'Cập nhật thất bại, vui lòng thử lại.'], 500);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $booking = DB::table('bookings')->where('id', $id)->first();
        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy đặt vé.'], 404);
        }

        $deleted = DB::table('bookings')->where('id', $id)->delete();

        if ($deleted) {
            return response()->json(['success' => true, 'message' => 'Đã xóa đặt vé thành công.']);
        }

        return response()->json(['success' => false, 'message' => 'Xóa thất bại, vui lòng thử lại.'], 500);
    }
}
