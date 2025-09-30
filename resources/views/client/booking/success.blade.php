<x-client.layout :web-profile="$web_profile ?? null" :main-menu="$mainMenu ?? []" :title="$title ?? 'Đặt vé thành công'" :description="$description ?? ''">
    <section class="bg-green-50 border-b border-green-100">
        <div class="container mx-auto px-4 py-16 text-center space-y-6">
            <span class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 text-green-600 text-2xl">
                <i class="fa-solid fa-circle-check"></i>
            </span>
            <h1 class="text-3xl md:text-4xl font-extrabold text-green-700">Đặt vé thành công!</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">Cảm ơn quý khách đã tin tưởng King Express Bus. Thông tin vé đã được gửi đến email <strong>{{ $booking->customer_email ?? 'của bạn' }}</strong>. Nhà xe sẽ sớm liên hệ với quý khách để xác nhận.</p>
            <div class="inline-flex items-center gap-3 bg-white border border-green-100 rounded-full px-6 py-3 shadow-sm text-sm text-gray-600">
                <i class="fa-solid fa-ticket text-green-500"></i>
                Mã đặt chỗ của bạn: <strong class="text-gray-900">{{ $booking->booking_code ?? 'Đang cập nhật' }}</strong>
            </div>
        </div>
    </section>

    <section class="py-12 bg-white">
        <div class="container mx-auto px-4 grid grid-cols-1 lg:grid-cols-3 gap-8">
            <article class="lg:col-span-2 space-y-8">
                <section class="border border-gray-100 rounded-3xl p-6 shadow-sm">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Thông tin hành khách</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                        <div>
                            <p class="text-gray-500">Họ và tên</p>
                            <p class="text-gray-900 font-semibold">{{ $booking->customer_name ?? 'Đang cập nhật' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Số điện thoại</p>
                            <p class="text-gray-900 font-semibold">{{ $booking->customer_phone ?? 'Đang cập nhật' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-gray-500">Email</p>
                            <p class="text-gray-900 font-semibold">{{ $booking->customer_email ?? 'Đang cập nhật' }}</p>
                        </div>
                    </div>
                </section>

                <section class="border border-gray-100 rounded-3xl p-6 shadow-sm">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Thông tin hành trình</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                        <div>
                            <p class="text-gray-500">Tuyến xe</p>
                            <p class="text-gray-900 font-semibold">{{ $booking->route_name ?? 'Đang cập nhật' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Nhà xe</p>
                            <p class="text-gray-900 font-semibold">{{ $booking->company_name ?? 'King Express Bus' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Ngày khởi hành</p>
                            <p class="text-gray-900 font-semibold">{{ isset($booking->booking_date) ? \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') : 'Đang cập nhật' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Giờ xuất bến</p>
                            <p class="text-gray-900 font-semibold">{{ isset($booking->start_time) ? \Carbon\Carbon::parse($booking->start_time)->format('H:i') : 'Đang cập nhật' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Số lượng vé</p>
                            <p class="text-gray-900 font-semibold">{{ $booking->quantity ?? 'Đang cập nhật' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Trạng thái thanh toán</p>
                            <p class="font-semibold {{ $booking->payment_status === 'paid' ? 'text-green-600' : 'text-amber-600' }}">{{ $booking->payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' }}</p>
                        </div>
                    </div>
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                        <div class="rounded-2xl bg-gray-50 p-4">
                            <p class="text-gray-500 font-semibold mb-2">Điểm đón</p>
                            <p class="text-gray-900 font-semibold">{{ $booking->pickup_name ?? 'Đang cập nhật' }}</p>
                            <p>{{ $booking->pickup_address ?? 'Địa chỉ sẽ được cập nhật' }}</p>
                        </div>
                        <div class="rounded-2xl bg-gray-50 p-4">
                            <p class="text-gray-500 font-semibold mb-2">Điểm trả</p>
                            <p class="text-gray-900 font-semibold">{{ $booking->dropoff_name ?? 'Đang cập nhật' }}</p>
                            <p>{{ $booking->dropoff_address ?? 'Địa chỉ sẽ được cập nhật' }}</p>
                        </div>
                    </div>
                </section>

                <section class="border border-gray-100 rounded-3xl p-6 shadow-sm">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Thao tác tiếp theo</h2>
                    <div class="space-y-4 text-sm text-gray-600">
                        <div class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-semibold">1</span>
                            <p>Kiểm tra email xác nhận để nhận mã vé và hướng dẫn thanh toán chi tiết (nếu có).</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-semibold">2</span>
                            <p>Chuẩn bị đến điểm đón trước giờ khởi hành 15 phút để đảm bảo không lỡ chuyến.</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-semibold">3</span>
                            <p>Liên hệ hotline nhà xe <strong class="text-gray-800">{{ $booking->company_hotline ?? ($web_profile->hotline ?? '0865 095 066') }}</strong> khi cần hỗ trợ về chuyến đi.</p>
                        </div>
                    </div>
                </section>
            </article>

            <aside class="space-y-6">
                <div class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900">Thông tin thanh toán</h2>
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <span>Tổng tiền</span>
                        <span class="text-gray-900 font-semibold text-lg">{{ $booking->total_price ? number_format($booking->total_price) . 'đ' : 'Liên hệ' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <span>Phương thức</span>
                        <span class="text-gray-900 font-semibold">{{ $booking->payment_method === 'online_banking' ? 'Chuyển khoản' : 'Thanh toán khi lên xe' }}</span>
                    </div>
                    @if($booking->payment_method === 'online_banking' && $booking->payment_status !== 'paid')
                    <div class="rounded-2xl bg-blue-50 border border-blue-100 p-4 text-sm text-blue-700">
                        <p>Nếu bạn chọn chuyển khoản, vui lòng làm theo hướng dẫn trong email để hoàn tất thanh toán và xác nhận vé.</p>
                    </div>
                    @endif
                </div>

                <div class="bg-gray-900 text-white rounded-3xl p-6 space-y-4">
                    <h3 class="text-lg font-semibold">Cần hỗ trợ chung?</h3>
                    <p class="text-sm text-white/80">Đội ngũ chăm sóc khách hàng của King Express Bus luôn sẵn sàng trợ giúp bạn 24/7.</p>
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $web_profile->hotline ?? '0865095066') }}" class="inline-flex items-center gap-3 px-5 py-3 bg-yellow-400 text-gray-900 font-semibold rounded-lg shadow hover:bg-yellow-300 transition">
                        <i class="fa-solid fa-phone"></i>
                        Gọi tổng đài
                    </a>
                    <a href="{{ route('client.contact') }}" class="inline-flex items-center gap-2 text-sm text-white/80 hover:text-white">
                        <i class="fa-solid fa-headset"></i>
                        Liên hệ chăm sóc khách hàng
                    </a>
                </div>

                <div class="bg-white border border-gray-100 rounded-3xl p-6 space-y-4 text-sm text-gray-600">
                    <h3 class="text-base font-semibold text-gray-900">Tuyến xe khác bạn có thể thích</h3>
                    <ul class="space-y-3">
                        <li><a href="{{ route('client.routes.search', ['from' => 'ha-noi', 'to' => 'sapa']) }}" class="text-blue-600 hover:text-blue-700 hover:underline">Hà Nội ⇆ Sa Pa</a></li>
                        <li><a href="{{ route('client.routes.search', ['from' => 'ha-noi', 'to' => 'ninh-binh']) }}" class="text-blue-600 hover:text-blue-700 hover:underline">Hà Nội ⇆ Ninh Bình</a></li>
                        <li><a href="{{ route('client.routes.search', ['from' => 'hue', 'to' => 'hoi-an']) }}" class="text-blue-600 hover:text-blue-700 hover:underline">Huế ⇆ Hội An</a></li>
                    </ul>
                </div>
            </aside>
        </div>
    </section>
</x-client.layout>
