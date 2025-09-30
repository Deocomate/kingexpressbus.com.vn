<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Hiển thị trang dashboard của nhà xe với các số liệu thống kê.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        // Bước 1: Lấy ID của công ty dựa trên user đang đăng nhập
        $companyId = DB::table('companies')->where('user_id', Auth::id())->value('id');

        // Nếu nhà xe chưa cập nhật thông tin, hiển thị thông báo
        if (!$companyId) {
            return view('company.dashboard.index', [
                'error' => 'Thông tin nhà xe của bạn chưa được thiết lập. Vui lòng cập nhật trong mục "Thông tin Nhà xe".'
            ]);
        }

        // Bước 2: Lấy dữ liệu cho các Info Box (Thống kê tổng quan)
        $totalBookings = DB::table('bookings AS b')
            ->join('bus_routes AS br', 'b.bus_route_id', '=', 'br.id')
            ->join('company_routes AS cr', 'br.company_route_id', '=', 'cr.id')
            ->where('cr.company_id', $companyId)
            ->count();

        $totalRevenue = DB::table('bookings AS b')
            ->join('bus_routes AS br', 'b.bus_route_id', '=', 'br.id')
            ->join('company_routes AS cr', 'br.company_route_id', '=', 'cr.id')
            ->where('cr.company_id', $companyId)
            ->whereIn('b.status', ['confirmed', 'completed'])
            ->sum('b.total_price');

        $totalBuses = DB::table('buses')->where('company_id', $companyId)->count();
        $totalBusRoutes = DB::table('bus_routes AS br')
            ->join('company_routes AS cr', 'br.company_route_id', '=', 'cr.id')
            ->where('cr.company_id', $companyId)
            ->count();


        // Bước 3: Dữ liệu cho biểu đồ trạng thái đặt vé (Doughnut Chart)
        $bookingStatusStats = DB::table('bookings AS b')
            ->join('bus_routes AS br', 'b.bus_route_id', '=', 'br.id')
            ->join('company_routes AS cr', 'br.company_route_id', '=', 'cr.id')
            ->where('cr.company_id', $companyId)
            ->select('b.status', DB::raw('count(*) as count'))
            ->groupBy('b.status')
            ->get();

        $statusMap = [
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'cancelled' => 'Đã hủy',
            'completed' => 'Hoàn thành',
        ];
        $bookingStatusLabels = $bookingStatusStats->pluck('status')->map(fn($status) => $statusMap[$status] ?? ucfirst($status));
        $bookingStatusData = $bookingStatusStats->pluck('count');


        // Bước 4: Dữ liệu cho biểu đồ doanh thu 12 tháng gần nhất (Bar Chart)
        $monthlyRevenueLabels = [];
        $monthlyRevenueData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthlyRevenueLabels[] = 'Tháng ' . $date->format('m/Y');

            $revenue = DB::table('bookings AS b')
                ->join('bus_routes AS br', 'b.bus_route_id', '=', 'br.id')
                ->join('company_routes AS cr', 'br.company_route_id', '=', 'cr.id')
                ->where('cr.company_id', $companyId)
                ->whereIn('b.status', ['confirmed', 'completed'])
                ->whereYear('b.booking_date', $date->year)
                ->whereMonth('b.booking_date', $date->month)
                ->sum('b.total_price');
            $monthlyRevenueData[] = $revenue;
        }

        // Bước 5: Lấy 10 đặt vé mới nhất
        $latestBookings = DB::table('bookings as b')
            ->join('bus_routes as br', 'b.bus_route_id', '=', 'br.id')
            ->join('company_routes as cr', 'br.company_route_id', '=', 'cr.id')
            ->join('routes as r', 'cr.route_id', '=', 'r.id')
            ->where('cr.company_id', $companyId)
            ->select(
                'b.booking_code',
                'b.customer_name',
                'b.status',
                'b.booking_date',
                'b.total_price',
                'r.name as route_name'
            )
            ->orderByDesc('b.created_at')
            ->limit(10)
            ->get();

        return view('company.dashboard.index', compact(
            'totalBookings',
            'totalRevenue',
            'totalBuses',
            'totalBusRoutes',
            'bookingStatusLabels',
            'bookingStatusData',
            'monthlyRevenueLabels',
            'monthlyRevenueData',
            'latestBookings'
        ));
    }
}
