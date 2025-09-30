<x-client.layout :title="$title ?? 'Tài khoản của tôi'" :description="$description ?? ''">
    @php
    $userName = $user->name ?? 'Khách hàng';
        $userEmail = $user->email ?? null;
        $userPhone = $user->phone ?? null;
        $preferredRoutes = ($preferredRoutes ?? collect())->values();
    @endphp

    <section class="bg-slate-900 text-white py-16">
        <div class="container mx-auto px-4 space-y-4">
            <span class="inline-flex items-center gap-2 text-sm uppercase tracking-widest text-yellow-300">
                <i class="fa-solid fa-user-circle"></i>
                Hồ sơ khách hàng
            </span>
            <h1 class="text-3xl md:text-4xl font-extrabold">Xin chào, {{ $userName }}</h1>
            <div class="flex flex-wrap items-center gap-4 text-white/80 text-sm">
                @if ($userEmail)
                    <span class="inline-flex items-center gap-2"><i class="fa-regular fa-envelope"></i>{{ $userEmail }}</span>
                @endif
                @if ($userPhone)
                    <span class="inline-flex items-center gap-2"><i class="fa-solid fa-phone"></i>{{ $userPhone }}</span>
                @endif
                <form method="POST" action="{{ route('client.logout') }}" class="inline-flex">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 text-red-300 hover:text-red-200 font-semibold">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        Đăng xuất
                    </button>
                </form>
            </div>
        </div>
    </section>

    <section class="py-12 bg-white">
        <div class="container mx-auto px-4 space-y-10">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="rounded-3xl border border-slate-100 bg-slate-50 p-6 text-center shadow-sm">
                    <p class="text-3xl font-bold text-slate-900">{{ number_format($stats['total_bookings'] ?? 0) }}</p>
                    <p class="text-sm text-slate-500">Tổng số vé đã đặt</p>
                </div>
                <div class="rounded-3xl border border-slate-100 bg-slate-50 p-6 text-center shadow-sm">
                    <p class="text-3xl font-bold text-blue-600">{{ number_format($stats['upcoming'] ?? 0) }}</p>
                    <p class="text-sm text-slate-500">Chuyến sắp khởi hành</p>
                </div>
                <div class="rounded-3xl border border-slate-100 bg-slate-50 p-6 text-center shadow-sm">
                    <p class="text-3xl font-bold text-emerald-600">{{ number_format($stats['completed'] ?? 0) }}</p>
                    <p class="text-sm text-slate-500">Chuyến đã hoàn thành</p>
                </div>
                <div class="rounded-3xl border border-slate-100 bg-slate-50 p-6 text-center shadow-sm">
                    <p class="text-3xl font-bold text-red-500">{{ number_format($stats['cancelled'] ?? 0) }}</p>
                    <p class="text-sm text-slate-500">Chuyến đã hủy</p>
                </div>
            </div>

            @if (($stats['total_spent'] ?? 0) > 0)
                <div class="bg-blue-50 border border-blue-100 rounded-3xl p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-blue-900">Tổng chi tiêu</h2>
                        <p class="text-sm text-blue-700">Tổng chi phí đã thanh toán cho các chuyến đi hoàn thành.</p>
                    </div>
                    <p class="text-2xl font-bold text-blue-600">{{ number_format($stats['total_spent']) }} đ</p>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-6">
                    <section class="border border-slate-100 rounded-3xl shadow-sm overflow-hidden">
                        <div class="bg-slate-50 px-6 py-4 flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-slate-900">Chuyến sắp khởi hành</h2>
                            <span class="text-sm text-slate-500">{{ $upcomingBookings->count() }} chuyến</span>
                        </div>
                        <div class="divide-y divide-slate-100">
                            @forelse ($upcomingBookings as $booking)
                                <article class="px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                    <div class="space-y-1">
                                        <div class="inline-flex items-center gap-2 text-sm text-slate-500">
                                            <i class="fa-regular fa-calendar"></i>
                                            {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}
                                        </div>
                                        <p class="text-lg font-semibold text-slate-900">{{ $booking->route_name }}</p>
                                        <p class="text-sm text-slate-500">{{ $booking->company_name ?? 'Nhà xe' }} &bull; Mã vé {{ $booking->booking_code }}</p>
                                    </div>
                                    <div class="text-sm text-slate-500 space-y-1">
                                        <p><i class="fa-solid fa-location-dot text-blue-600"></i> Đón: {{ $booking->pickup_name ?? 'Đang cập nhật' }}</p>
                                        <p><i class="fa-solid fa-location-crosshairs text-emerald-600"></i> Trả: {{ $booking->dropoff_name ?? 'Đang cập nhật' }}</p>
                                    </div>
                                    <div class="text-right space-y-1">
                                        <p class="text-lg font-bold text-blue-600">{{ number_format($booking->total_price) }} đ</p>
                                        <p class="text-sm text-slate-500 capitalize">Trạng thái: {{ $booking->status }}</p>
                                        <a href="{{ route('client.routes.show', ['slug' => $booking->route_slug]) }}"
                                           class="inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-700">
                                            Xem tuyến
                                            <i class="fa-solid fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </article>
                            @empty
                                <div class="px-6 py-10 text-center text-slate-500">
                                    <i class="fa-regular fa-face-smile-beam text-3xl mb-3"></i>
                                    <p>Chưa có chuyến sắp khởi hành. Đặt vé ngay để trải nghiệm hành trình thoải mái.</p>
                                    <a href="{{ route('client.routes.search') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-blue-600 text-white rounded-xl">
                                        <i class="fa-solid fa-ticket"></i>
                                        Tìm tuyến xe
                                    </a>
                                </div>
                            @endforelse
                        </div>
                    </section>

                    <section id="history" class="border border-slate-100 rounded-3xl shadow-sm overflow-hidden">
                        <div class="bg-slate-50 px-6 py-4 flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-slate-900">Lịch sử đặt vé</h2>
                            <span class="text-sm text-slate-500">{{ $bookingHistory->count() }} chuyến</span>
                        </div>
                        <div class="divide-y divide-slate-100">
                            @forelse ($bookingHistory as $booking)
                                <article class="px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                    <div class="space-y-1">
                                        <div class="inline-flex items-center gap-2 text-sm text-slate-500">
                                            <i class="fa-regular fa-calendar-check"></i>
                                            {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}
                                        </div>
                                        <p class="text-lg font-semibold text-slate-900">{{ $booking->route_name }}</p>
                                        <p class="text-sm text-slate-500">{{ $booking->company_name ?? 'Nhà xe' }} &bull; Mã vé {{ $booking->booking_code }}</p>
                                    </div>
                                    <div class="text-right space-y-1">
                                        <p class="text-lg font-bold text-slate-900">{{ number_format($booking->total_price) }} đ</p>
                                        <p class="text-sm text-slate-500 capitalize">Trạng thái: {{ $booking->status }}</p>
                                    </div>
                                </article>
                            @empty
                                <div class="px-6 py-10 text-center text-slate-500">
                                    <i class="fa-regular fa-calendar text-3xl mb-3"></i>
                                    <p>Bạn chưa có lịch sử đặt vé. Tìm và đặt chuyến ngay hôm nay.</p>
                                </div>
                            @endforelse
                        </div>
                    </section>
                </div>
                <aside class="space-y-6">
                    @if ($preferredRoutes->isNotEmpty())
                        <div class="border border-slate-100 rounded-3xl shadow-sm p-6 space-y-4">
                            <h2 class="text-lg font-semibold text-slate-900">Tuyến ưa thích</h2>
                            <ul class="space-y-3 text-sm text-slate-600">
                                @foreach ($preferredRoutes as $item)
                                    <li class="flex items-center justify-between gap-3">
                                        <a href="{{ route('client.routes.show', ['slug' => $item['slug']]) }}" class="text-blue-600 hover:text-blue-700 font-semibold">
                                            {{ $item['name'] }}
                                        </a>
                                        <span class="inline-flex items-center gap-1 text-xs bg-blue-50 text-blue-600 px-2 py-1 rounded-lg">
                                            <i class="fa-solid fa-ticket"></i>
                                            {{ $item['count'] }} lần
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="border border-slate-100 rounded-3xl shadow-sm p-6 space-y-4">
                        <h2 class="text-lg font-semibold text-slate-900">Mẹo đặt vé nhanh</h2>
                        <ul class="list-disc list-inside text-sm text-slate-600 space-y-2">
                            <li>Lưu ý người đi cùng trong ghi chú để được hỗ trợ chọn chỗ.</li>
                            <li>Thanh toán trước giữ chỗ vé chắc chắn hơn trong giờ cao điểm.</li>
                            <li>Cập nhật email thường xuyên để nhận thông báo chuyến xe.</li>
                        </ul>
                        <a href="{{ route('client.routes.search') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl text-sm">
                            <i class="fa-solid fa-compass"></i>
                            Tìm tuyến mới
                        </a>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</x-client.layout>
