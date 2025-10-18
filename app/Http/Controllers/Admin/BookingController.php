<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Str;

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
        // Get base booking info first
        $booking = DB::table('bookings as b')
            ->join('bus_routes as br', 'b.bus_route_id', '=', 'br.id')
            ->join('company_routes as cr', 'br.company_route_id', '=', 'cr.id')
            ->join('routes as r', 'cr.route_id', '=', 'r.id')
            ->join('companies as c', 'cr.company_id', '=', 'c.id')
            ->join('buses', 'br.bus_id', '=', 'buses.id')
            ->select(
                'b.*', // Select all booking fields
                'r.name as route_name',
                'c.name as company_name',
                'buses.name as bus_name',
                'br.start_time',
                'br.end_time'
            )
            ->where('b.id', $id)
            ->first();

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy đặt vé.'], 404);
        }

        // Fetch stop info separately to handle null pickup_stop_id
        $pickupStop = null;
        if ($booking->pickup_stop_id) {
            $pickupStop = DB::table('stops')
                ->select('name as pickup_stop_name', 'address as pickup_stop_address')
                ->where('id', $booking->pickup_stop_id)
                ->first();
        }

        $dropoffStop = DB::table('stops')
            ->select('name as dropoff_stop_name', 'address as dropoff_stop_address')
            ->where('id', $booking->dropoff_stop_id)
            ->first();

        // Merge stop info into booking object
        $booking = (object)array_merge((array)$booking, (array)$pickupStop, (array)$dropoffStop);

        // Prepare display fields for modal
        $booking->pickup_display = 'N/A';
        if ($booking->pickup_stop_id && isset($booking->pickup_stop_name)) {
            $booking->pickup_display = $booking->pickup_stop_name;
            if ($booking->pickup_stop_address) {
                $booking->pickup_display .= ' - ' . $booking->pickup_stop_address;
            }
        } elseif (is_null($booking->pickup_stop_id) && Str::contains($booking->notes, '[Đón tại khách sạn]')) {
            // Extract hotel address from notes
            $hotelAddress = Str::after($booking->notes, '[Đón tại khách sạn]: ');
            $hotelAddress = Str::before($hotelAddress, "\n"); // Get only the first line if there are other notes
            $booking->pickup_display = 'Đón tại khách sạn: ' . trim($hotelAddress);
        }

        // Clean up notes for display (remove hotel part if present)
        $booking->notes_display = $booking->notes;
        if (Str::contains($booking->notes, '[Đón tại khách sạn]')) {
            $parts = explode("\n", $booking->notes, 2);
            $booking->notes_display = count($parts) > 1 ? trim(Str::after($parts[1], '[Ghi chú của khách]: ')) : '';
        }
        if (empty(trim((string)$booking->notes_display))) {
            $booking->notes_display = 'Không có';
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
     * Note: This only updates the status and potentially appends notes for cancellation.
     * Payment status should be updated via a separate mechanism if needed.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,confirmed,cancelled,completed',
            // 'payment_status' => 'required|in:unpaid,paid', // Removed as per refactor plan
            'notes' => 'nullable|string|max:1000', // Cancellation reason
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $booking = DB::table('bookings')->where('id', $id)->first();
        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy đặt vé.'], 404);
        }

        $updateData = [
            'status' => $request->input('status'),
            'updated_at' => Carbon::now(),
        ];

        // Handle cancellation notes
        if ($request->input('status') === 'cancelled' && $request->filled('notes')) {
            $cancellationReason = trim($request->input('notes'));
            $existingNotes = $booking->notes ?? '';
            // Append reason, ensuring not to duplicate if already cancelled with reason
            if (!Str::contains($existingNotes, '[Lý do hủy Admin]')) {
                $updateData['notes'] = $existingNotes . "\n[Lý do hủy Admin]: " . $cancellationReason;
            }
        }
        // If changing status away from cancelled, consider if you want to clear/modify notes


        $updated = DB::table('bookings')
            ->where('id', $id)
            ->update($updateData);

        if ($updated) {
            // Return the updated booking status for UI update
            $updatedBooking = DB::table('bookings')->select('id', 'status', 'notes')->find($id);
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật đặt vé thành công.',
                'updated_data' => [ // Send back minimal data needed for UI update
                    'status' => $updatedBooking->status,
                    'notes' => $updatedBooking->notes // Send updated notes if needed
                ]
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Cập nhật thất bại hoặc không có gì thay đổi.'], 500);
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

        // Add checks here if needed (e.g., prevent deleting completed bookings)

        $deleted = DB::table('bookings')->where('id', $id)->delete();

        if ($deleted) {
            return response()->json(['success' => true, 'message' => 'Đã xóa đặt vé thành công.']);
        }

        return response()->json(['success' => false, 'message' => 'Xóa thất bại, vui lòng thử lại.'], 500);
    }
}
