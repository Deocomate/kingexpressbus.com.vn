<?php

namespace App\Http\Middleware\Roles;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CustomerAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()
                ->route('client.login', ['redirect_to' => $request->fullUrl()])
                ->with('warning', 'Vui long Đăng nhập de tiep tuc.');
        }

        $user = Auth::user();

        if ($user->role !== 'customer') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('client.login')
                ->with('error', 'Tai khoan cua ban khong phu hop de truy cap chuc nang nay.');
        }

        return $next($request);
    }
}
