<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AuthenticateController extends Controller
{
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'role' => ['required', Rule::in(['admin', 'company'])],
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->role === 'admin') {
                return redirect()->intended(route('admin.dashboard.index'));
            }

            if ($user->role === 'company') {
                return redirect()->intended(route('company.dashboard.index'));
            }
        }

        return back()->with('error', 'Email, mật khẩu hoặc vai trò không chính xác.')->withInput($request->only('email', 'role'));
    }
}
