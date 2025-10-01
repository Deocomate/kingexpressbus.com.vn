<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Mail\BookingConfirmMail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'bus_route_id' => 'required|integer|exists:bus_routes,id',
            'date' => 'required|string',
        ]);

        $busRouteId = (int)$request->input('bus_route_id');
        $bookingDate = $this->parseBookingDate($request->input('date', now()->format('Y-m-d')));

        $trip = DB::table('bus_routes as br')
            ->join('company_routes as cr', 'br.company_route_id', '=', 'cr.id')
            ->join('companies as c', 'cr.company_id', '=', 'c.id')
            ->join('routes as r', 'cr.route_id', '=', 'r.id')
            ->join('buses as b', 'br.bus_id', '=', 'b.id')
            ->select([
                'br.id as bus_route_id',
                'br.start_time',
                'br.end_time',
                'br.price',
                'br.is_active',
                'cr.id as company_route_id',
                'cr.name as company_route_name',
                'cr.slug as company_route_slug',
                'c.name as company_name',
                'c.slug as company_slug',
                'c.phone as company_phone',
                'c.email as company_email',
                'c.hotline as company_hotline',
                'r.id as route_id',
                'r.name as route_name',
                'r.slug as route_slug',
                'b.id as bus_id',
                'b.name as bus_name',
                'b.model_name as bus_model',
                'b.seat_count',
                'b.services',
                'b.thumbnail_url as bus_thumbnail',
                'b.image_list_url',
                'b.content as bus_content',
            ])
            ->where('br.id', $busRouteId)
            ->first();

        abort_if(!$trip || !$trip->is_active, 404, 'Chuyến xe không tồn tại hoặc đã tạm dừng.');

        $bookedTicketsCount = DB::table('bookings')
            ->where('bus_route_id', $busRouteId)
            ->whereDate('booking_date', $bookingDate)
            ->whereIn('status', ['pending', 'confirmed', 'completed'])
            ->sum('quantity');

        $availableSeats = ($trip->seat_count ?? 0) - $bookedTicketsCount;

        $stops = DB::table('company_route_stops as crs')
            ->join('stops as s', 'crs.stop_id', '=', 's.id')
            ->join('districts as d', 's.district_id', '=', 'd.id')
            ->join('provinces as p', 'd.province_id', '=', 'p.id')
            ->select([
                's.id',
                's.name',
                's.address',
                'crs.stop_type',
                'p.name as province_name',
                'd.name as district_name',
            ])
            ->where('crs.company_route_id', $trip->company_route_id)
            ->orderBy('crs.priority')
            ->get();

        $services = $trip->services ? json_decode($trip->services, true) : [];
        $busImages = $trip->image_list_url ? json_decode($trip->image_list_url, true) : [];

        $paymentMethods = [
            [
                'key' => 'online_banking',
                'label' => 'Thanh toán chuyển khoản',
                'description' => 'Thông tin tài khoản sẽ được gửi qua email sau khi xác nhận.',
            ],
            [
                'key' => 'cash_on_pickup',
                'label' => 'Thanh toán khi lên xe',
                'description' => 'Thanh toán trực tiếp cho tài xế hoặc nhân viên phụ xe khi đón.',
            ],
        ];

        $user = Auth::user();

        return view('client.booking.create', compact(
            'trip',
            'bookingDate',
            'stops',
            'services',
            'busImages',
            'paymentMethods',
            'availableSeats',
            'user'
        ));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bus_route_id' => 'required|integer|exists:bus_routes,id',
            'booking_date' => 'required|date_format:d/m/Y',
            'quantity' => 'required|integer|min:1',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'required|email|max:255',
            'pickup_stop_id' => 'required|integer|exists:stops,id',
            'dropoff_stop_id' => 'required|integer|exists:stops,id',
            'total_price' => 'required|integer|min:0',
            'payment_method' => 'required|string|in:cash_on_pickup,online_banking',
            'notes' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();
        $validated['notes'] = isset($validated['notes']) ? strip_tags($validated['notes']) : null;
        $bookingDateForDb = Carbon::createFromFormat('d/m/Y', $validated['booking_date'])->format('Y-m-d');

        DB::beginTransaction();
        try {
            $trip = DB::table('bus_routes as br')
                ->join('buses as b', 'br.bus_id', '=', 'b.id')
                ->select('b.seat_count')
                ->where('br.id', $validated['bus_route_id'])
                ->first();

            $totalSeats = $trip->seat_count ?? 0;

            $bookedTicketsCount = DB::table('bookings')
                ->where('bus_route_id', $validated['bus_route_id'])
                ->whereDate('booking_date', $bookingDateForDb)
                ->whereIn('status', ['pending', 'confirmed', 'completed'])
                ->lockForUpdate()
                ->sum('quantity');

            $availableSeats = $totalSeats - $bookedTicketsCount;
            $requestedQuantity = (int)$validated['quantity'];

            if ($requestedQuantity > $availableSeats) {
                return back()->with('error', 'Chuyến xe không còn đủ ' . $requestedQuantity . ' vé. Chỉ còn lại ' . $availableSeats . ' vé.')->withInput();
            }

            $bookingId = DB::table('bookings')->insertGetId([
                'user_id' => Auth::id(),
                'bus_route_id' => $validated['bus_route_id'],
                'booking_date' => $bookingDateForDb,
                'booking_code' => Str::upper(Str::random(8)),
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_email' => $validated['customer_email'],
                'pickup_stop_id' => $validated['pickup_stop_id'],
                'dropoff_stop_id' => $validated['dropoff_stop_id'],
                'quantity' => $requestedQuantity,
                'total_price' => $validated['total_price'],
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'unpaid',
                'status' => $validated['payment_method'] === 'online_banking' ? 'pending' : 'confirmed',
                'notes' => $validated['notes'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            try {
                $mailDetails = $this->prepareMailDetails($bookingId);

                if ($mailDetails) {
                    Mail::to($validated['customer_email'])->queue(new BookingConfirmMail($mailDetails));
                    Mail::to("kingexpressbus@gmail.com")->queue(new BookingConfirmMail($mailDetails));
                } else {
                    Log::error('Không thể chuẩn bị dữ liệu mail cho booking ID: ' . $bookingId);
                }
            } catch (\Throwable $mailException) {
                Log::error('Lỗi khi đưa email vào queue', [
                    'booking_id' => $bookingId,
                    'error' => $mailException->getMessage(),
                    'trace' => $mailException->getTraceAsString(),
                ]);
            }

            return redirect()->route('client.booking.success')->with('booking_id', $bookingId);
        } catch (\Throwable $exception) {
            DB::rollBack();
            Log::error('Đặt vé phía client thất bại', ['error' => $exception->getMessage()]);
            return back()->with('error', 'Hệ thống đang quá tải, vui lòng thử lại sau.')->withInput();
        }
    }

    private function prepareMailDetails(int $bookingId): ?array
    {
        $details = DB::table('bookings as b')
            ->join('bus_routes as br', 'b.bus_route_id', '=', 'br.id')
            ->join('buses as bus', 'br.bus_id', '=', 'bus.id')
            ->join('company_routes as cr', 'br.company_route_id', '=', 'cr.id')
            ->join('routes as r', 'cr.route_id', '=', 'r.id')
            ->join('companies as c', 'cr.company_id', '=', 'c.id')
            ->join('stops as p_stop', 'b.pickup_stop_id', '=', 'p_stop.id')
            ->join('stops as d_stop', 'b.dropoff_stop_id', '=', 'd_stop.id')
            ->join('provinces as start_prov', 'r.province_start_id', '=', 'start_prov.id')
            ->join('provinces as end_prov', 'r.province_end_id', '=', 'end_prov.id')
            ->select([
                'b.*',
                'r.name as route_name',
                'c.name as company_name',
                'c.hotline as company_hotline',
                'br.start_time',
                'bus.name as bus_name',
                'bus.model_name as bus_model_name',
                'p_stop.name as pickup_name',
                'p_stop.address as pickup_address',
                'd_stop.name as dropoff_name',
                'd_stop.address as dropoff_address',
                'start_prov.name as start_province',
                'end_prov.name as end_province',
            ])
            ->where('b.id', $bookingId)
            ->first();

        if (!$details) {
            return null;
        }

        $result = (array)$details;
        $webProfile = DB::table('web_profiles')->where('is_default', true)->first();

        $result['web_title'] = $webProfile->title ?? config('app.name');
        $result['web_phone'] = $webProfile->hotline ?? $webProfile->phone ?? 'N/A';
        $result['web_email'] = $webProfile->email ?? 'N/A';
        $result['web_link'] = url('/');
        $result['departure_date'] = isset($result['booking_date']) ? Carbon::parse($result['booking_date'])->format('d/m/Y') : 'N/A';
        $result['start_time'] = isset($result['start_time']) ? Carbon::parse($result['start_time'])->format('H:i') : 'N/A';
        $result['bus_type_name'] = $result['bus_model_name'] ?? 'Đang cập nhật';
        $result['pickup_info'] = sprintf('%s - %s', $result['pickup_name'] ?? 'N/A', $result['pickup_address'] ?? 'N/A');
        $result['needs_bank_transfer_info'] = ($result['payment_method'] === 'online_banking');

        return $result;
    }

    public function success()
    {
        $bookingId = session('booking_id');
        if (!$bookingId) {
            return redirect()->route('client.home');
        }

        $booking = DB::table('bookings as b')
            ->join('bus_routes as br', 'b.bus_route_id', '=', 'br.id')
            ->join('company_routes as cr', 'br.company_route_id', '=', 'cr.id')
            ->join('routes as r', 'cr.route_id', '=', 'r.id')
            ->join('companies as c', 'cr.company_id', '=', 'c.id')
            ->join('stops as p_stop', 'b.pickup_stop_id', '=', 'p_stop.id')
            ->join('stops as d_stop', 'b.dropoff_stop_id', '=', 'd_stop.id')
            ->select([
                'b.id',
                'b.booking_code',
                'b.customer_name',
                'b.customer_phone',
                'b.customer_email',
                'b.booking_date',
                'b.quantity',
                'b.total_price',
                'b.payment_method',
                'b.payment_status',
                'br.start_time',
                'br.end_time',
                'cr.name as company_route_name',
                'c.name as company_name',
                'c.hotline as company_hotline',
                'r.name as route_name',
                'r.slug as route_slug',
                'p_stop.name as pickup_name',
                'p_stop.address as pickup_address',
                'd_stop.name as dropoff_name',
                'd_stop.address as dropoff_address',
            ])
            ->where('b.id', $bookingId)
            ->first();

        return view('client.booking.success', [
            'booking' => $booking,
            'title' => 'Đặt vé thành công',
            'description' => 'Thông tin xác nhận đặt vé của bạn tại King Express Bus.',
        ]);
    }

    private function parseBookingDate(string $value): Carbon
    {
        $patterns = ['d-m-Y', 'd/m/Y', 'Y-m-d'];
        foreach ($patterns as $pattern) {
            try {
                return Carbon::createFromFormat($pattern, $value)->startOfDay();
            } catch (\Throwable $exception) {
                // try next pattern
            }
        }

        try {
            return Carbon::parse($value)->startOfDay();
        } catch (\Throwable $exception) {
            abort(400, 'Ngày khởi hành không hợp lệ.');
        }
    }
}
