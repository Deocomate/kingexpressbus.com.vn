<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLoginForm(Request $request)
    {
        $redirectTo = $this->resolveRedirect($request, route('client.profile.index'));

        return view('client.auth.login', [
            'title' => 'Đăng nhập King Express Bus',
            'description' => 'Đăng nhập để theo dõi hành trình, đặt vé nhanh và quản lý thông tin cá nhân của bạn.',
            'redirectTo' => $redirectTo,
            'authHighlights' => $this->authHighlights(),
            'pageBanner' => [
                'headline' => 'Chào mừng trở lại',
                'subline' => 'Mở khóa hành trình của bạn chỉ với một lần đăng nhập.',
                'image' => '/userfiles/files/kingexpressbus/cabin/1.jpg',
            ],
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
            'redirect_to' => ['nullable', 'string'],
            'remember' => ['nullable'],
        ], [
            'login.required' => 'Vui lòng nhập email hoặc số điện thoại.',
            'password.required' => 'Mật khẩu không được để trống.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->only('login', 'redirect_to'));
        }

        $data = $validator->validated();
        $loginValue = trim($data['login']);
        $guardField = filter_var($loginValue, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $credentials = [
            $guardField => $guardField === 'phone' ? preg_replace('/[^0-9+]/', '', $loginValue) : $loginValue,
            'password' => $data['password'],
            'role' => 'customer',
        ];

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $redirectUrl = $this->resolveRedirect($request, route('client.profile.index'));

            return redirect()->intended($redirectUrl)
                ->with('success', 'Đăng nhập thành công.');
        }

        throw ValidationException::withMessages([
            'login' => 'Thông tin đăng nhập không chính xác.',
        ]);
    }

    public function showRegistrationForm(Request $request)
    {
        $redirectTo = $this->resolveRedirect($request, route('client.profile.index'));

        return view('client.auth.register', [
            'title' => 'Đăng ký tài khoản khách hàng',
            'description' => 'Tạo tài khoản mới để đặt vé nhanh, lưu thông tin hành khách và nhận ưu đãi riêng.',
            'redirectTo' => $redirectTo,
            'authHighlights' => $this->authHighlights(),
            'pageBanner' => [
                'headline' => 'Tạo tài khoản mới',
                'subline' => 'Sẵn sàng cho những chuyến đi tiện nghi cùng King Express Bus.',
                'image' => '/userfiles/files/kingexpressbus/cabin/3.jpg',
            ],
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'redirect_to' => ['nullable', 'string'],
        ], [
            'name.required' => 'Vui lòng nhập họ tên.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email này đã được sử dụng.',
            'phone.unique' => 'Số điện thoại này đã được sử dụng.',
            'password.required' => 'Mật khẩu không được để trống.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $validator->after(function ($validator) use ($request) {
            $email = $request->input('email');
            $phone = $request->input('phone');

            if (empty($email) && empty($phone)) {
                $validator->errors()->add('email', 'Cần nhập email hoặc số điện thoại.');
            }
        });

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        $data = $validator->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => isset($data['phone']) ? preg_replace('/[^0-9+]/', '', $data['phone']) : null,
            'password' => Hash::make($data['password']),
            'role' => 'customer',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        $redirectUrl = $this->resolveRedirect($request, route('client.profile.index'));

        return redirect($redirectUrl)->with('success', 'Đăng ký tài khoản thành công.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('client.home')->with('success', 'Bạn đã đăng xuất.');
    }

    private function authHighlights(): array
    {
        return [
            [
                'icon' => 'fa-regular fa-bell',
                'title' => 'Thông báo hành trình',
                'description' => 'Nhận lịch khởi hành và trạng thái thanh toán qua email.',
            ],
            [
                'icon' => 'fa-solid fa-ticket',
                'title' => 'Quản lý vé dễ dàng',
                'description' => 'Theo dõi mã đặt chỗ, ghế ngồi và thông tin hành khách tại một nơi.',
            ],
            [
                'icon' => 'fa-solid fa-shield-check',
                'title' => 'Bảo mật thông tin',
                'description' => 'Hệ thống mã hóa dữ liệu chuẩn, bảo vệ thông tin cá nhân.',
            ],
        ];
    }

    private function resolveRedirect(Request $request, string $fallback): string
    {
        $target = $request->input('redirect_to');

        if (is_string($target) && $target !== '') {
            if (!str_starts_with($target, 'http')) {
                return url($target);
            }

            if (str_starts_with($target, url('/'))) {
                return $target;
            }
        }

        return $fallback;
    }
}
