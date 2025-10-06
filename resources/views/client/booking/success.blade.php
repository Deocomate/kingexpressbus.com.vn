{{-- ===== resources\views\client\booking\success.blade.php ===== --}}
<x-client.layout :web-profile="$web_profile ?? null" :main-menu="$mainMenu ?? []" :title="$title ?? __('client.booking.success.meta_title')" :description="$description ?? ''">
    <section class="bg-green-50 border-b border-green-100">
        <div class="container mx-auto px-4 py-16 text-center space-y-6">
            <span class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 text-green-600 text-2xl">
                <i class="fa-solid fa-circle-check"></i>
            </span>
            <h1 class="text-3xl md:text-4xl font-extrabold text-green-700">{{ __('client.booking.success.title') }}</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">{{ __('client.booking.success.thank_you_message', ['email' => $booking->customer_email ?? __('client.booking.success.your_email')]) }}</p>
            <div class="inline-flex items-center gap-3 bg-white border border-green-100 rounded-full px-6 py-3 shadow-sm text-sm text-gray-600">
                <i class="fa-solid fa-ticket text-green-500"></i>
                {{ __('client.booking.success.booking_code_label') }}: <strong class="text-gray-900">{{ $booking->booking_code ?? __('client.booking.common.updating') }}</strong>
            </div>
        </div>
    </section>

    <section class="py-12 bg-white">
        <div class="container mx-auto px-4 grid grid-cols-1 lg:grid-cols-3 gap-8">
            <article class="lg:col-span-2 space-y-8">
                <section class="border border-gray-100 rounded-3xl p-6 shadow-sm">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">{{ __('client.booking.success.passenger_info_title') }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                        <div>
                            <p class="text-gray-500">{{ __('client.booking.success.passenger_name') }}</p>
                            <p class="text-gray-900 font-semibold">{{ $booking->customer_name ?? __('client.booking.common.updating') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">{{ __('client.booking.success.passenger_phone') }}</p>
                            <p class="text-gray-900 font-semibold">{{ $booking->customer_phone ?? __('client.booking.common.updating') }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-gray-500">{{ __('client.booking.success.passenger_email') }}</p>
                            <p class="text-gray-900 font-semibold">{{ $booking->customer_email ?? __('client.booking.common.updating') }}</p>
                        </div>
                    </div>
                </section>

                <section class="border border-gray-100 rounded-3xl p-6 shadow-sm">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">{{ __('client.booking.success.trip_info_title') }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                        <div>
                            <p class="text-gray-500">{{ __('client.booking.success.route') }}</p>
                            <p class="text-gray-900 font-semibold">{{ $booking->route_name ?? __('client.booking.common.updating') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">{{ __('client.booking.success.company') }}</p>
                            <p class="text-gray-900 font-semibold">{{ $booking->company_name ?? 'King Express Bus' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">{{ __('client.booking.success.departure_date') }}</p>
                            <p class="text-gray-900 font-semibold">{{ isset($booking->booking_date) ? \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') : __('client.booking.common.updating') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">{{ __('client.booking.success.departure_time') }}</p>
                            <p class="text-gray-900 font-semibold">{{ isset($booking->start_time) ? \Carbon\Carbon::parse($booking->start_time)->format('H:i') : __('client.booking.common.updating') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">{{ __('client.booking.success.quantity') }}</p>
                            <p class="text-gray-900 font-semibold">{{ $booking->quantity ?? __('client.booking.common.updating') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">{{ __('client.booking.success.payment_status') }}</p>
                            <p class="font-semibold {{ $booking->payment_status === 'paid' ? 'text-green-600' : 'text-amber-600' }}">{{ $booking->payment_status === 'paid' ? __('client.booking.success.paid') : __('client.booking.success.unpaid') }}</p>
                        </div>
                    </div>
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                        <div class="rounded-2xl bg-gray-50 p-4">
                            <p class="text-gray-500 font-semibold mb-2">{{ __('client.booking.success.pickup_point') }}</p>
                            <p class="text-gray-900 font-semibold">{{ $booking->pickup_name ?? __('client.booking.common.updating') }}</p>
                            <p>{{ $booking->pickup_address ?? __('client.booking.success.address_updating') }}</p>
                        </div>
                        <div class="rounded-2xl bg-gray-50 p-4">
                            <p class="text-gray-500 font-semibold mb-2">{{ __('client.booking.success.dropoff_point') }}</p>
                            <p class="text-gray-900 font-semibold">{{ $booking->dropoff_name ?? __('client.booking.common.updating') }}</p>
                            <p>{{ $booking->dropoff_address ?? __('client.booking.success.address_updating') }}</p>
                        </div>
                    </div>
                </section>

                <section class="border border-gray-100 rounded-3xl p-6 shadow-sm">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">{{ __('client.booking.success.next_steps_title') }}</h2>
                    <div class="space-y-4 text-sm text-gray-600">
                        <div class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-semibold">1</span>
                            <p>{{ __('client.booking.success.step_1') }}</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-semibold">2</span>
                            <p>{{ __('client.booking.success.step_2') }}</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-semibold">3</span>
                            <p>{!! __('client.booking.success.step_3', ['hotline' => $booking->company_hotline ?? ($web_profile->hotline ?? '0865 095 066')]) !!}</p>
                        </div>
                    </div>
                </section>
            </article>

            <aside class="space-y-6">
                <div class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm space-y-4">
                    <h2 class="text-lg font-semibold text-gray-900">{{ __('client.booking.success.payment_info_title') }}</h2>
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <span>{{ __('client.booking.success.total_price') }}</span>
                        <span class="text-gray-900 font-semibold text-lg">{{ $booking->total_price ? number_format($booking->total_price) . 'đ' : __('client.booking.create.summary_contact_price') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <span>{{ __('client.booking.success.payment_method') }}</span>
                        <span class="text-gray-900 font-semibold">{{ $booking->payment_method === 'online_banking' ? __('client.booking.success.payment_method_online') : __('client.booking.success.payment_method_cash') }}</span>
                    </div>
                    @if($booking->payment_method === 'online_banking' && $booking->payment_status !== 'paid')
                    <div class="rounded-2xl bg-blue-50 border border-blue-100 p-4 text-sm text-blue-700">
                        <p>{{ __('client.booking.success.online_payment_note') }}</p>
                    </div>
                    @endif
                </div>

                <div class="bg-gray-900 text-white rounded-3xl p-6 space-y-4">
                    <h3 class="text-lg font-semibold">{{ __('client.booking.success.support_title') }}</h3>
                    <p class="text-sm text-white/80">{{ __('client.booking.success.support_description') }}</p>
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $web_profile->hotline ?? '0865095066') }}" class="inline-flex items-center gap-3 px-5 py-3 bg-yellow-400 text-gray-900 font-semibold rounded-lg shadow hover:bg-yellow-300 transition">
                        <i class="fa-solid fa-phone"></i>
                        {{ __('client.booking.success.call_button') }}
                    </a>
                    <a href="{{ route('client.contact') }}" class="inline-flex items-center gap-2 text-sm text-white/80 hover:text-white">
                        <i class="fa-solid fa-headset"></i>
                        {{ __('client.booking.success.contact_link') }}
                    </a>
                </div>

                <div class="bg-white border border-gray-100 rounded-3xl p-6 space-y-4 text-sm text-gray-600">
                    <h3 class="text-base font-semibold text-gray-900">{{ __('client.booking.success.other_routes_title') }}</h3>
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
