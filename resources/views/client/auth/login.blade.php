<x-client.layout :title="$title ?? 'Đăng nhập'" :description="$description ?? ''">
    @php
        $banner = $pageBanner ?? null;
        $highlights = $authHighlights ?? [];
        $redirectTarget = $redirectTo ?? route('client.profile.index');
    @endphp

    <section class="relative bg-slate-900 text-white overflow-hidden">
        <div class="absolute inset-0 opacity-70">
            <img src="{{ $banner['image'] ?? '/userfiles/files/kingexpressbus/cabin/1.jpg' }}" alt="King Express Bus"
                 class="h-full w-full object-cover" loading="lazy">
        </div>
        <div class="absolute inset-0 bg-slate-900/80"></div>
        <div class="relative container mx-auto px-4 py-16 lg:py-24 grid grid-cols-1 lg:grid-cols-2 gap-10">
            <div class="space-y-6">
                <span class="inline-flex items-center gap-2 text-sm uppercase tracking-widest text-yellow-300">
                    <i class="fa-solid fa-shield-halved"></i>
                    Đăng nhập an toàn
                </span>
                <h1 class="text-3xl md:text-4xl font-extrabold">{{ $banner['headline'] ?? 'Đăng nhập King Express Bus' }}</h1>
                <p class="text-white/80 text-lg leading-relaxed">
                    {{ $banner['subline'] ?? 'Tiếp tục hành trình với những chuyến xe chất lượng cao.' }}
                </p>
                @if (!empty($highlights))
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach ($highlights as $item)
                            <div class="bg-white/10 backdrop-blur rounded-2xl p-4 flex items-start gap-3">
                                <div class="h-10 w-10 rounded-xl bg-blue-500/20 flex items-center justify-center text-xl text-blue-200">
                                    <i class="{{ $item['icon'] ?? 'fa-solid fa-circle-check' }}"></i>
                                </div>
                                <div>
                                    <p class="font-semibold">{{ $item['title'] ?? '' }}</p>
                                    <p class="text-sm text-white/75">{{ $item['description'] ?? '' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div>
                <div class="bg-white text-slate-900 rounded-3xl shadow-xl p-8 lg:p-10 relative">
                    <div class="absolute -top-4 right-6 bg-blue-600 text-white text-xs font-semibold px-3 py-1 rounded-full shadow">
                        Khách hàng thân thiết
                    </div>
                    <form method="POST" action="{{ route('client.login.submit') }}" class="space-y-5">
                        @csrf
                        <input type="hidden" name="redirect_to" value="{{ $redirectTarget }}">

                        <div class="space-y-2">
                            <label for="login" class="text-sm font-semibold text-slate-600">Email hoặc số điện thoại</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                                    <i class="fa-regular fa-user"></i>
                                </span>
                                <input id="login" name="login" type="text" value="{{ old('login') }}"
                                       class="w-full rounded-xl border border-slate-200 py-3 pl-10 pr-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Nhập email hoặc số điện thoại" autocomplete="username" required>
                            </div>
                            @error('login')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="password" class="text-sm font-semibold text-slate-600">Mật khẩu</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                                    <i class="fa-solid fa-lock"></i>
                                </span>
                                <input id="password" name="password" type="password"
                                       class="w-full rounded-xl border border-slate-200 py-3 pl-10 pr-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Nhập mật khẩu" autocomplete="current-password" required>
                            </div>
                            @error('password')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between text-sm">
                            <label class="inline-flex items-center gap-2">
                                <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                <span>Lưu thông tin đăng nhập</span>
                            </label>
                            <a href="{{ route('client.register', ['redirect_to' => $redirectTarget]) }}" class="text-blue-600 hover:text-blue-700 font-semibold">
                                Chưa có tài khoản?
                            </a>
                        </div>

                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold py-3 rounded-xl transition">
                            <i class="fa-solid fa-right-to-bracket"></i>
                            <span>Đăng nhập</span>
                        </button>

                        <p class="text-xs text-slate-500 text-center">
                            Bằng việc tiếp tục, bạn đồng ý với điều khoản sử dụng và chính sách bảo mật của chúng tôi.
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </section>
</x-client.layout>
