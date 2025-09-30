<x-client.layout :title="$title ?? 'Danh sách nhà xe đối tác'" :description="$description ?? ''">
    @php
        $searchTerm = $filters['search'] ?? '';
        $companyFallback = '/userfiles/files/web information/logo.jpg';
        $featuredRoutes = $featuredRoutes ?? collect();
    @endphp

    <section class="relative overflow-hidden bg-slate-900 text-white">
        <div class="absolute inset-0">
            <img src="/userfiles/files/kingexpressbus/sleeper/6.jpg" alt="King Express Bus" class="h-full w-full object-cover" loading="lazy">
            <div class="absolute inset-0 bg-slate-900/80"></div>
        </div>
        <div class="relative container mx-auto px-4 py-16 space-y-8">
            <div class="max-w-3xl space-y-4">
                <span class="inline-flex items-center gap-2 text-sm uppercase tracking-widest text-yellow-300">
                    <i class="fa-solid fa-building"></i>
                    Hệ sinh thái đối tác
                </span>
                <h1 class="text-3xl md:text-4xl font-extrabold leading-tight">Nhà xe đồng hành cùng King Express Bus</h1>
                <p class="text-white/80 text-lg">
                    Khám phá danh sách các nhà xe đã được kiểm duyệt, đáp ứng tiêu chuẩn dịch vụ và an toàn để phục vụ hành khách trên khắp cả nước.
                </p>
            </div>
            <form method="GET" action="{{ route('client.companies.index') }}" class="bg-white/10 backdrop-blur rounded-2xl p-4 flex flex-col md:flex-row gap-3 md:items-center">
                <div class="flex-1 flex items-center gap-3 bg-white rounded-xl px-4 py-3">
                    <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                    <input type="text" name="q" value="{{ $searchTerm }}" class="flex-1 text-slate-900 border-none focus:ring-0" placeholder="Tìm theo tên nhà xe hoặc địa điểm">
                </div>
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-yellow-400 text-slate-900 font-semibold rounded-xl shadow hover:bg-yellow-300 transition">
                    <i class="fa-solid fa-search"></i>
                    Tìm kiếm
                </button>
            </form>
        </div>
    </section>

    <!-- Dedicated route search section -->
    <section id="search-section" class="py-10 bg-slate-50">
        <div class="container mx-auto px-4">
            <div class="bg-white border border-slate-100 rounded-3xl p-4 shadow-sm">
                <x-client.search-bar :search-data="$searchData" submit-label="Tìm tuyến" />
            </div>
        </div>
    </section>

    <section class="py-14 bg-slate-50">
        <div class="container mx-auto px-4 grid grid-cols-1 xl:grid-cols-3 gap-8 items-start">
            <div class="xl:col-span-2 space-y-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-slate-900">{{ $companies->total() }} nhà xe phù hợp</h2>
                    @if ($searchTerm)
                        <a href="{{ route('client.companies.index') }}" class="text-sm text-blue-600 hover:text-blue-700">Xóa bộ lọc</a>
                    @endif
                </div>

                <div class="space-y-6">
                    @forelse ($companies as $company)
                        <article class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm hover:shadow-lg transition">
                            <div class="flex flex-col md:flex-row md:items-center gap-6">
                                <div class="w-20 h-20 flex-shrink-0 rounded-2xl bg-slate-100 overflow-hidden">
                                    <img src="{{ $company->thumbnail_url ?: $companyFallback }}" alt="{{ $company->name }}" class="h-full w-full object-cover" loading="lazy">
                                </div>
                                <div class="flex-1 space-y-3">
                                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                                        <div>
                                            <h3 class="text-xl font-semibold text-slate-900">{{ $company->name }}</h3>
                                            <p class="text-sm text-slate-500">{{ \Illuminate\Support\Str::limit($company->description, 150) }}</p>
                                        </div>
                                        <a href="{{ route('client.companies.show', $company->slug) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-700">
                                            Xem chi tiết
                                            <i class="fa-solid fa-arrow-right"></i>
                                        </a>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm text-slate-600">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-route text-blue-500"></i>
                                            <span>{{ $company->route_count }} tuyến đang khai thác</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-ticket text-emerald-500"></i>
                                            <span>{{ $company->active_trip_count }} chuyến đang mở bán</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-coins text-yellow-500"></i>
                                            <span>{{ $company->min_price ? 'Giá từ ' . number_format($company->min_price) . ' đ' : 'Liên hệ giá' }}</span>
                                        </div>
                                    </div>
                                    @if ($company->address || $company->hotline)
                                        <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 border-t border-slate-100 pt-3">
                                            @if ($company->address)
                                                <span class="inline-flex items-center gap-2"><i class="fa-solid fa-location-dot"></i>{{ $company->address }}</span>
                                            @endif
                                            @if ($company->hotline)
                                                <span class="inline-flex items-center gap-2"><i class="fa-solid fa-phone"></i>{{ $company->hotline }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="bg-white border border-slate-100 rounded-3xl p-10 text-center text-slate-500">
                            <i class="fa-regular fa-face-frown-open text-3xl mb-3"></i>
                            <p>Không tìm thấy nhà xe phù hợp. Hãy thay đổi từ khóa và thử lại.</p>
                        </div>
                    @endforelse
                </div>

                <div class="pt-4">
                    {{ $companies->onEachSide(1)->links() }}
                </div>
            </div>

            <aside class="space-y-6">

                @if ($featuredRoutes->isNotEmpty())
                    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
                        <h3 class="text-lg font-semibold text-slate-900">Tuyến nổi bật</h3>
                        <div class="space-y-4">
                            @foreach ($featuredRoutes as $route)
                                <a href="{{ route('client.routes.show', ['slug' => $route->slug]) }}" class="flex items-center gap-4 group">
                                    <div class="w-16 h-16 rounded-2xl overflow-hidden bg-slate-100">
                                        <img src="{{ $route->thumbnail_url ?: '/userfiles/files/city_imgs/ha-noi.jpg' }}" alt="{{ $route->name }}" class="h-full w-full object-cover" loading="lazy">
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-slate-900 group-hover:text-blue-600 transition">{{ $route->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $route->min_price ? 'Giá từ ' . number_format($route->min_price) . ' đ' : 'Liên hệ giá' }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="bg-blue-50 border border-blue-100 rounded-3xl p-6 space-y-4 text-sm text-blue-700">
                    <h3 class="text-base font-semibold text-blue-900">Lợi ích khi đặt qua King Express Bus</h3>
                    <ul class="space-y-2">
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check"></i> Xác thực nhà xe, đảm bảo chất lượng</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check"></i> Hỗ trợ đổi vé linh hoạt, minh bạch</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check"></i> Nhiều ưu đãi dành riêng cho thành viên</li>
                    </ul>
                </div>
            </aside>
        </div>
    </section>
</x-client.layout>
