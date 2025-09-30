<x-client.layout :title="$title ?? 'Đăng ký'" :description="$description ?? ''">
    @php
        $banner = $pageBanner ?? null;
        $highlights = $authHighlights ?? [];
        $redirectTarget = $redirectTo ?? route('client.profile.index');
    @endphp

    <section class="relative bg-gradient-to-br from-blue-900 via-slate-900 to-slate-900 text-white overflow-hidden">
        <div class="absolute inset-0 opacity-70">
            <img src="{{ $banner['image'] ?? '/userfiles/files/kingexpressbus/cabin/3.jpg' }}" alt="King Express Bus"
                 class="h-full w-full object-cover" loading="lazy">
        </div>
        <div class="absolute inset-0 bg-slate-900/85"></div>
        <div class="relative container mx-auto px-4 py-16 lg:py-24 grid grid-cols-1 lg:grid-cols-2 gap-10">
            <div class="space-y-6">
                <span class="inline-flex items-center gap-2 text-sm uppercase tracking-widest text-yellow-300">
                    <i class="fa-solid fa-id-card"></i>
                    Trở thành thành viên
                </span>
                <h1 class="text-3xl md:text-4xl font-extrabold">{{ $banner['headline'] ?? 'Đăng ký tài khoản' }}</h1>
                <p class="text-white/80 text-lg leading-relaxed">
                    {{ $banner['subline'] ?? 'Đặt vé nhanh, giữ lại thông tin hành khách và nhận thông báo lịch chạy mới nhất.' }}
                </p>
                @if (!empty($highlights))
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach ($highlights as $item)
                            <div class="bg-white/10 backdrop-blur rounded-2xl p-4">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="h-10 w-10 rounded-xl bg-yellow-400/20 flex items-center justify-center text-xl text-yellow-200">
                                        <i class="{{ $item['icon'] ?? 'fa-solid fa-circle-check' }}"></i>
                                    </div>
                                    <p class="font-semibold">{{ $item['title'] ?? '' }}</p>
                                </div>
                                <p class="text-sm text-white/75">{{ $item['description'] ?? '' }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div>
                <div class="bg-white text-slate-900 rounded-3xl shadow-xl p-8 lg:p-10 space-y-6">
                    <div>
                        <h2 class="text-2xl font-bold">Thông tin tài khoản</h2>
                        <p class="text-sm text-slate-500">Nhận quyền quản lý đặt chỗ và thông báo chuyến xe chỉ trong vài bước.</p>
                    </div>
                    <form method="POST" action="{{ route('client.register.submit') }}" class="space-y-5">
                        @csrf
                        <input type="hidden" name="redirect_to" value="{{ $redirectTarget }}">

                        <div class="space-y-2">
                            <label for="name" class="text-sm font-semibold text-slate-600">Họ tên</label>
                            <input id="name" name="name" type="text" value="{{ old('name') }}"
                                   class="w-full rounded-xl border border-slate-200 py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Nhập họ tên" required>
                            @error('name')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label for="email" class="text-sm font-semibold text-slate-600">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}"
                                       class="w-full rounded-xl border border-slate-200 py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Nhập email">
                                @error('email')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="space-y-2">
                    <label for="phone" class="text-sm font-semibold text-slate-600">Số điện thoại</label>
                                <input id="phone" name="phone" type="text" value="{{ old('phone') }}"
                                       class="w-full rounded-xl border border-slate-200 py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Nhập số điện thoại">
                                @error('phone')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-2">
                    <label for="password" class="text-sm font-semibold text-slate-600">Mật khẩu</label>
                                <input id="password" name="password" type="password"
                                       class="w-full rounded-xl border border-slate-200 py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Tối thiểu 6 ký tự" required>
                                @error('password')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="space-y-2">
                    <label for="password_confirmation" class="text-sm font-semibold text-slate-600">Xác nhận mật khẩu</label>
                                <input id="password_confirmation" name="password_confirmation" type="password"
                                       class="w-full rounded-xl border border-slate-200 py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Nhập lại mật khẩu" required>
                            </div>
                        </div>

                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold py-3 rounded-xl transition">
                            <i class="fa-solid fa-user-plus"></i>
                            <span>Đăng ký tài khoản</span>
                        </button>
                    </form>
                    <p class="text-sm text-slate-500 text-center">
                        Đã có tài khoản?
                        <a href="{{ route('client.login', ['redirect_to' => $redirectTarget]) }}" class="text-blue-600 hover:text-blue-700 font-semibold">Đăng nhập ngay</a>
                    </p>
                </div>
            </div>
        </div>
    </section>
</x-client.layout>
