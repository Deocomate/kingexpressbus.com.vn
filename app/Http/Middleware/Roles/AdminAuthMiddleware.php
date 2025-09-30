<?php

namespace App\Http\Middleware\Roles;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return to_route('login');
        }

        if (Auth::user()->role !== 'admin') {
            // Quay lại trang trước đó với thông báo lỗi
            return back()->with('error', 'Unauthorized! Bạn không có quyền truy cập khu vực này.');
        }

        return $next($request);
    }
}
