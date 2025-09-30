<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        abort_if(!$user, 403);

        $bookings = DB::table('bookings as b')
            ->join('bus_routes as br', 'b.bus_route_id', '=', 'br.id')
            ->join('company_routes as cr', 'br.company_route_id', '=', 'cr.id')
            ->join('routes as r', 'cr.route_id', '=', 'r.id')
            ->leftJoin('companies as c', 'cr.company_id', '=', 'c.id')
            ->leftJoin('stops as ps', 'b.pickup_stop_id', '=', 'ps.id')
            ->leftJoin('stops as ds', 'b.dropoff_stop_id', '=', 'ds.id')
            ->select([
                'b.id',
                'b.booking_code',
                'b.booking_date',
                'b.status',
                'b.payment_method',
                'b.payment_status',
                'b.quantity',
                'b.total_price',
                'b.created_at',
                'b.notes',
                'br.start_time',
                'br.end_time',
                'br.price as unit_price',
                'cr.slug as company_route_slug',
                'r.name as route_name',
                'r.slug as route_slug',
                'c.name as company_name',
                'c.slug as company_slug',
                'ps.name as pickup_name',
                'ps.address as pickup_address',
                'ds.name as dropoff_name',
                'ds.address as dropoff_address',
            ])
            ->where('b.user_id', $user->id)
            ->orderByDesc('b.booking_date')
            ->orderByDesc('b.created_at')
            ->get();

        $today = Carbon::today();
        $upcomingBookings = $bookings->filter(function ($booking) use ($today) {
            return Carbon::parse($booking->booking_date)->gte($today);
        });
        $bookingHistory = $bookings->reject(function ($booking) use ($today) {
            return Carbon::parse($booking->booking_date)->gte($today);
        });

        $stats = [
            'total_bookings' => $bookings->count(),
            'upcoming' => $upcomingBookings->count(),
            'completed' => $bookings->where('status', 'completed')->count(),
            'cancelled' => $bookings->where('status', 'cancelled')->count(),
            'total_spent' => $bookings->whereIn('status', ['confirmed', 'completed'])->sum('total_price'),
        ];

        $preferredRoutes = $bookings
            ->groupBy('route_slug')
            ->map(function ($items, $slug) {
                $total = $items->count();
                $routeName = $items->first()->route_name ?? $slug;
                return [
                    'slug' => $slug,
                    'name' => $routeName,
                    'count' => $total,
                ];
            })
            ->sortByDesc('count')
            ->values();

        return view('client.profile.index', [
            'user' => $user,
            'bookings' => $bookings,
            'upcomingBookings' => $upcomingBookings,
            'bookingHistory' => $bookingHistory,
            'stats' => $stats,
            'preferredRoutes' => $preferredRoutes,
            'title' => 'Tài khoản của tôi',
            'description' => 'Quản lý thông tin cá nhân và lịch sử đặt vé tại King Express Bus.',
        ]);
    }
}
