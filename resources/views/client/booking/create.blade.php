<x-client.layout :web-profile="$web_profile ?? null" :main-menu="$mainMenu ?? []"
                 :title="$title ?? __('client.booking.create.meta_title')" :description="$description ?? ''">
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css"/>
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
        <style>
            .litepicker {
                font-family: 'Inter', sans-serif;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                border-radius: 1rem;
                border: 1px solid #e5e7eb;
            }

            .quantity-btn {
                transition: all 0.2s ease-in-out;
            }

            .quantity-btn:hover {
                transform: scale(1.1);
            }

            .payment-method-label {
                transition: all 0.2s ease-in-out;
                cursor: pointer;
            }

            .payment-method-label.selected {
                border-color: #3b82f6;
                box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.4);
            }

            .select2-container--default .select2-selection--single {
                border-radius: 0.75rem;
                border-color: #e5e7eb;
                background-color: #f9fafb;
                height: 48px;
                box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 46px;
                padding-left: 1rem;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 46px;
                right: 0.5rem;
            }

            .select2-dropdown {
                border-radius: 0.75rem;
                border-color: #e5e7eb;
            }

            .select2-results__option {
                padding: 0.75rem 1rem;
            }
        </style>
    @endpush

    @php
        $busImage = $busImages[0] ?? $trip->bus_thumbnail ?? '/userfiles/files/kingexpressbus/cabin/1.jpg';
        $seatPrice = (int) ($trip->price ?? 0);
    @endphp

    <section class="bg-gray-900 text-white">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 items-center">
                <div class="space-y-4 lg:col-span-2">
                    <span class="inline-flex items-center gap-2 text-sm uppercase tracking-widest text-yellow-300">
                        <i class="fa-solid fa-ticket"></i>
                        {{ __('client.booking.create.header_subtitle') }}
                    </span>
                    <h1 class="text-3xl md:text-4xl font-extrabold leading-tight">
                        {{ $trip->route_name }} · {{ $trip->company_name }}
                    </h1>
                    <div class="flex flex-wrap gap-x-6 gap-y-2 text-base text-white/80">
                        <span class="inline-flex items-center gap-2"><i class="fa-solid fa-clock w-4 text-center"></i> {{ \Carbon\Carbon::parse($trip->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($trip->end_time)->format('H:i') }}</span>
                        <span class="inline-flex items-center gap-2"><i
                                class="fa-solid fa-calendar-day w-4 text-center"></i> {{ $bookingDate->format('d/m/Y') }}</span>
                        <span class="inline-flex items-center gap-2"><i class="fa-solid fa-couch w-4 text-center"></i> {{ $trip->bus_name }} ({{ $trip->bus_model ?? __('client.booking.common.updating') }})</span>
                    </div>
                </div>
                <div class="relative h-48 lg:h-56 rounded-3xl overflow-hidden shadow-xl">
                    <img src="{{ $busImage }}" alt="{{ $trip->bus_name }}" class="h-full w-full object-cover"
                         loading="lazy">
                    <span
                        class="absolute bottom-4 left-4 inline-flex items-center gap-2 bg-white/90 text-gray-900 px-4 py-2 rounded-full text-sm font-semibold">
                        <i class="fa-solid fa-shield-heart text-blue-600"></i>
                        {{ __('client.booking.create.insurance_badge') }}
                    </span>
                </div>
            </div>
        </div>
    </section>

    <section class="py-10 bg-gray-50">
        <div class="container mx-auto px-4 grid grid-cols-1 xl:grid-cols-3 gap-8">
            <form class="xl:col-span-2 space-y-8" method="POST" action="{{ route('client.booking.store') }}"
                  id="booking-form">
                @if (session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl" role="alert">
                        <strong class="font-bold">{{ __('client.booking.create.error_title') }}</strong>
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl" role="alert">
                        <strong class="font-bold">{{ __('client.booking.create.validation_error_title') }}</strong>
                        <ul class="mt-2 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @csrf
                <input type="hidden" name="bus_route_id" value="{{ $trip->bus_route_id }}">
                <input type="hidden" name="total_price" id="total-price-input" value="0">

                <section class="bg-white border border-gray-100 rounded-3xl p-6 space-y-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-gray-900">{{ __('client.booking.create.trip_info_title') }}</h2>
                        <span
                            class="text-xs text-gray-500 uppercase tracking-wider">{{ __('client.booking.create.step_1') }}</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="booking_date"
                                   class="block text-sm font-semibold text-gray-700 mb-2">{{ __('client.booking.create.departure_date_label') }}</label>
                            <input type="text" id="booking_date" name="booking_date"
                                   value="{{ $bookingDate->format('d/m/Y') }}"
                                   class="w-full rounded-xl border-gray-200 bg-gray-50 p-3 text-base shadow-sm transition-all duration-300 focus:bg-white focus:border-blue-400 focus:ring-0"
                                   required>
                        </div>
                        <div>
                            <label for="quantity"
                                   class="block text-sm font-semibold text-gray-700 mb-2">{{ __('client.booking.create.quantity_label') }}</label>
                            <div class="flex items-center gap-4">
                                <button type="button" id="decrease-quantity"
                                        class="quantity-btn p-2 rounded-full bg-gray-200 text-gray-700 hover:bg-gray-300">
                                    <i class="fa-solid fa-minus"></i>
                                </button>
                                <input type="number" name="quantity" id="quantity"
                                       value="{{ old('quantity', request('quantity', 1)) }}" min="1"
                                       max="{{ $availableSeats }}"
                                       class="w-20 text-center font-bold text-lg rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm"
                                       readonly>
                                <button type="button" id="increase-quantity"
                                        class="quantity-btn p-2 rounded-full bg-gray-200 text-gray-700 hover:bg-gray-300">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                                <span
                                    class="text-sm text-gray-500">{!! trans_choice('client.booking.create.seats_left', $availableSeats, ['count' => $availableSeats]) !!}</span>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="bg-white border border-gray-100 rounded-3xl p-6 space-y-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-gray-900">{{ __('client.booking.create.passenger_info_title') }}</h2>
                        <span
                            class="text-xs text-gray-500 uppercase tracking-wider">{{ __('client.booking.create.step_2') }}</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="customer_name"
                                   class="block text-sm font-semibold text-gray-700 mb-2">{{ __('client.booking.create.name_label') }}</label>
                            <input type="text" id="customer_name" name="customer_name"
                                   value="{{ old('customer_name', request('customer_name', $user->name ?? '')) }}"
                                   class="w-full rounded-xl border-gray-200 bg-gray-50 p-3 text-base shadow-sm transition-all duration-300 focus:bg-white focus:border-blue-400 focus:ring-0"
                                   required placeholder="{{ __('client.booking.create.name_placeholder') }}">
                        </div>
                        <div>
                            <label for="customer_phone"
                                   class="block text-sm font-semibold text-gray-700 mb-2">{{ __('client.booking.create.phone_label') }}</label>
                            <input type="tel" id="customer_phone" name="customer_phone"
                                   value="{{ old('customer_phone', request('customer_phone', $user->phone ?? '')) }}"
                                   class="w-full rounded-xl border-gray-200 bg-gray-50 p-3 text-base shadow-sm transition-all duration-300 focus:bg-white focus:border-blue-400 focus:ring-0"
                                   required placeholder="{{ __('client.booking.create.phone_placeholder') }}">
                        </div>
                        <div>
                            <label for="customer_email"
                                   class="block text-sm font-semibold text-gray-700 mb-2">{{ __('client.booking.create.email_label') }}</label>
                            <input type="email" id="customer_email" name="customer_email"
                                   value="{{ old('customer_email', request('customer_email', $user->email ?? '')) }}"
                                   class="w-full rounded-xl border-gray-200 bg-gray-50 p-3 text-base shadow-sm transition-all duration-300 focus:bg-white focus:border-blue-400 focus:ring-0"
                                   required placeholder="{{ __('client.booking.create.email_placeholder') }}">
                        </div>
                        <div>
                            <label for="notes"
                                   class="block text-sm font-semibold text-gray-700 mb-2">{{ __('client.booking.create.notes_label') }}</label>
                            <textarea id="notes" name="notes" rows="3"
                                      class="w-full rounded-xl border-gray-200 bg-gray-50 p-3 text-base shadow-sm transition-all duration-300 focus:bg-white focus:border-blue-400 focus:ring-0"
                                      placeholder="{{ __('client.booking.create.notes_placeholder') }}">{{ old('notes', request('notes')) }}</textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="pickup_stop_id"
                                   class="block text-sm font-semibold text-gray-700 mb-2">{{ __('client.booking.create.pickup_label') }}</label>
                            <select id="pickup_stop_id" name="pickup_stop_id" class="w-full" required>
                                <option value="">{{ __('client.booking.create.pickup_placeholder') }}</option>
                                @if($trip->available_hotel_pickup)
                                    <option
                                        value="hotel_pickup" @selected(old('pickup_stop_id', request('pickup_stop_id')) == 'hotel_pickup')>
                                        {{ __('client.booking.create.pickup_at_hotel') }}
                                    </option>
                                @endif
                                @foreach ($stops->where('stop_type', '!=', 'dropoff') as $point)
                                    <option
                                        value="{{ $point->id }}"
                                        data-address="{{ $point->address }}"
                                        @selected(old('pickup_stop_id', request('pickup_stop_id')) == $point->id)>
                                        {{ $point->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="dropoff_stop_id"
                                   class="block text-sm font-semibold text-gray-700 mb-2">{{ __('client.booking.create.dropoff_label') }}</label>
                            <select id="dropoff_stop_id" name="dropoff_stop_id" class="w-full" required>
                                <option value="">{{ __('client.booking.create.dropoff_placeholder') }}</option>
                                @foreach ($stops->where('stop_type', '!=', 'pickup') as $point)
                                    <option
                                        value="{{ $point->id }}"
                                        data-address="{{ $point->address }}"
                                        @selected(old('dropoff_stop_id', request('dropoff_stop_id')) == $point->id)>
                                        {{ $point->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="hotel-pickup-address-wrapper" class="hidden mt-4">
                        <label for="hotel_pickup_address"
                               class="block text-sm font-semibold text-gray-700 mb-2">{{ __('client.booking.create.hotel_address_label') }}</label>
                        <input type="text" id="hotel_pickup_address" name="hotel_pickup_address"
                               value="{{ old('hotel_pickup_address', request('hotel_pickup_address')) }}"
                               class="w-full rounded-xl border-gray-200 bg-gray-50 p-3 text-base shadow-sm transition-all duration-300 focus:bg-white focus:border-blue-400 focus:ring-0"
                               placeholder="{{ __('client.booking.create.hotel_address_placeholder') }}">
                    </div>

                    <div>
                        <h3 class="text-base font-semibold text-gray-800 mb-3">{{ __('client.booking.create.payment_method_title') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($paymentMethods as $method)
                                <label class="payment-method-label block border border-gray-200 rounded-2xl p-4">
                                    <input type="radio" name="payment_method" value="{{ $method['key'] }}"
                                           class="hidden payment-method-input" @checked(old('payment_method', request('payment_method', 'cash_on_pickup')) === $method['key'])>
                                    <div class="flex items-start gap-4">
                                        <div
                                            class="mt-1 w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center radio-icon">
                                            <div class="w-2.5 h-2.5 rounded-full bg-blue-600 hidden"></div>
                                        </div>
                                        <div class="flex-1">
                                            <span
                                                class="text-sm font-semibold text-gray-900">{{ $method['label'] }}</span>
                                            <p class="text-sm text-gray-600 mt-1">{{ $method['description'] }}</p>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </section>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="text-sm text-gray-600">
                        {!! __('client.booking.create.terms_agreement', ['link' => '#']) !!}
                    </div>
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-3 px-8 py-4 bg-blue-600 text-white font-semibold rounded-xl shadow-lg hover:bg-blue-700 transition-transform transform hover:scale-105 disabled:opacity-60 disabled:cursor-not-allowed"
                            id="submit-booking">
                        <span id="submit-text">{{ __('client.booking.create.submit_button') }}</span>
                        <i id="submit-spinner" class="fa-solid fa-spinner animate-spin hidden"></i>
                    </button>
                </div>
            </form>

            <aside class="space-y-6">
                <div class="bg-white border border-gray-100 rounded-3xl p-6 space-y-4">
                    <h2 class="text-xl font-semibold text-gray-900">{{ __('client.booking.create.summary_title') }}</h2>
                    <div class="space-y-3 text-base text-gray-600">
                        <div class="flex items-center justify-between">
                            <span>{{ __('client.booking.create.summary_price_per_ticket') }}</span>
                            <span
                                class="font-semibold text-gray-900">{{ $seatPrice > 0 ? number_format($seatPrice) . 'đ' : __('client.booking.create.summary_contact_price') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>{{ __('client.booking.create.summary_quantity') }}</span>
                            <span class="font-semibold text-gray-900" id="summary-quantity">1</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>{{ __('client.booking.create.summary_service_fee') }}</span>
                            <span
                                class="text-green-600 font-semibold">{{ __('client.booking.create.summary_free') }}</span>
                        </div>
                    </div>
                    <div
                        class="border-t border-gray-200 pt-4 mt-4 flex items-center justify-between text-xl font-bold text-gray-900">
                        <span>{{ __('client.booking.create.summary_total') }}</span>
                        <span id="summary-total-price">0đ</span>
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-100 rounded-3xl p-6 space-y-4 text-sm text-blue-800">
                    <h3 class="text-base font-semibold text-blue-900">{{ __('client.booking.create.amenities_title') }}</h3>
                    <ul class="grid grid-cols-2 gap-x-4 gap-y-2">
                        @forelse ($services as $service)
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-blue-500"></i>
                                {{ $service }}
                            </li>
                        @empty
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-blue-500"></i>
                                {{ __('client.booking.create.amenity_ac') }}
                            </li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-blue-500"></i>
                                {{ __('client.booking.create.amenity_blanket') }}
                            </li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-blue-500"></i>
                                {{ __('client.booking.create.amenity_water') }}
                            </li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-blue-500"></i>
                                {{ __('client.booking.create.amenity_wifi') }}
                            </li>
                        @endforelse
                    </ul>
                </div>

                <div class="bg-white border border-gray-100 rounded-3xl p-6 space-y-4">
                    <h3 class="text-xl font-semibold text-gray-900">{{ __('client.booking.create.support_title') }}</h3>
                    <p class="text-gray-600">{{ __('client.booking.create.support_description') }}</p>
                    <div class="space-y-3">
                        @if(!empty($web_profile->hotline))
                            <a href="tel:{{ preg_replace('/[^\d+]/', '', $web_profile->hotline) }}"
                               class="flex items-center gap-3 p-3 rounded-lg bg-gray-100 hover:bg-gray-200 transition">
                                <i class="fa-solid fa-phone-volume text-blue-600 text-lg"></i>
                                <span class="font-semibold text-gray-800">{{ __('client.booking.create.support_hotline') }}: {{ $web_profile->hotline }}</span>
                            </a>
                        @endif
                        @if(!empty($web_profile->phone))
                            <a href="tel:{{ preg_replace('/[^\d+]/', '', $web_profile->phone) }}"
                               class="flex items-center gap-3 p-3 rounded-lg bg-gray-100 hover:bg-gray-200 transition">
                                <i class="fa-solid fa-headset text-blue-600 text-lg"></i>
                                <span class="font-semibold text-gray-800">{{ __('client.booking.create.support_call_center') }}: {{ $web_profile->phone }}</span>
                            </a>
                        @endif
                        @if(!empty($web_profile->email))
                            <a href="mailto:{{ $web_profile->email }}"
                               class="flex items-center gap-3 p-3 rounded-lg bg-gray-100 hover:bg-gray-200 transition">
                                <i class="fa-regular fa-envelope text-blue-600 text-lg"></i>
                                <span class="font-semibold text-gray-800">{{ __('client.booking.create.support_email') }}: {{ $web_profile->email }}</span>
                            </a>
                        @endif
                        @if(!empty($web_profile->zalo_url))
                            <a href="{{ $web_profile->zalo_url }}" target="_blank"
                               class="flex items-center gap-3 p-3 rounded-lg bg-gray-100 hover:bg-gray-200 transition">
                                <i class="fa-solid fa-comment-dots text-blue-600 text-lg"></i>
                                <span
                                    class="font-semibold text-gray-800">{{ __('client.booking.create.support_zalo') }}</span>
                            </a>
                        @endif
                        @if(!empty($web_profile->whatsapp))
                            <a href="https://wa.me/{{ preg_replace('/[^\d]/', '', $web_profile->whatsapp) }}"
                               target="_blank"
                               class="flex items-center gap-3 p-3 rounded-lg bg-gray-100 hover:bg-gray-200 transition">
                                <i class="fab fa-whatsapp text-blue-600 text-lg"></i>
                                <span
                                    class="font-semibold text-gray-800">{{ __('client.booking.create.support_whatsapp') }}</span>
                            </a>
                        @endif
                        @if(!empty($web_profile->facebook_url))
                            <a href="{{ $web_profile->facebook_url }}" target="_blank"
                               class="flex items-center gap-3 p-3 rounded-lg bg-gray-100 hover:bg-gray-200 transition">
                                <i class="fa-brands fa-facebook-messenger text-blue-600 text-lg"></i>
                                <span
                                    class="font-semibold text-gray-800">{{ __('client.booking.create.support_facebook') }}</span>
                            </a>
                        @endif
                    </div>
                </div>
            </aside>
        </div>
    </section>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/litepicker/dist/litepicker.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const bookingForm = document.getElementById('booking-form');
                const submitButton = document.getElementById('submit-booking');
                const submitText = document.getElementById('submit-text');
                const submitSpinner = document.getElementById('submit-spinner');

                bookingForm.addEventListener('submit', function () {
                    if (document.getElementById('quantity').value > 0) {
                        submitButton.disabled = true;
                        submitText.classList.add('hidden');
                        submitSpinner.classList.remove('hidden');
                    }
                });

                const quantityInput = document.getElementById('quantity');
                const decreaseBtn = document.getElementById('decrease-quantity');
                const increaseBtn = document.getElementById('increase-quantity');
                const maxQuantity = {{ $availableSeats }};
                const seatPrice = {{ $seatPrice }};
                const summaryQuantity = document.getElementById('summary-quantity');
                const summaryTotalPrice = document.getElementById('summary-total-price');
                const totalPriceInput = document.getElementById('total-price-input');

                const updateSummary = () => {
                    const quantity = parseInt(quantityInput.value);
                    const totalPrice = quantity * seatPrice;
                    summaryQuantity.textContent = quantity;
                    summaryTotalPrice.textContent = totalPrice > 0 ? totalPrice.toLocaleString('vi-VN') + 'đ' : '0đ';
                    totalPriceInput.value = totalPrice;
                    submitButton.disabled = quantity === 0;
                };

                decreaseBtn.addEventListener('click', () => {
                    let currentVal = parseInt(quantityInput.value);
                    if (currentVal > 1) {
                        quantityInput.value = currentVal - 1;
                        updateSummary();
                    }
                });

                increaseBtn.addEventListener('click', () => {
                    let currentVal = parseInt(quantityInput.value);
                    if (currentVal < maxQuantity) {
                        quantityInput.value = currentVal + 1;
                        updateSummary();
                    }
                });

                const picker = new Litepicker({
                    element: document.getElementById('booking_date'),
                    format: 'DD/MM/YYYY',
                    minDate: new Date(),
                    singleMode: true,
                    setup: (picker) => {
                        picker.on('selected', (date) => {
                            const selectedDate = new Date(date.dateInstance).toLocaleDateString('fr-CA'); // YYYY-MM-DD
                            const url = new URL(window.location.href);
                            url.searchParams.set('date', selectedDate);
                            const formData = new FormData(bookingForm);
                            for (let [key, value] of formData.entries()) {
                                if (key !== 'booking_date' && key !== '_token' && value) {
                                    url.searchParams.set(key, value);
                                }
                            }
                            window.location.href = url.toString();
                        });
                    },
                });

                const urlParams = new URLSearchParams(window.location.search);
                urlParams.forEach((value, key) => {
                    const field = bookingForm.querySelector(`[name="${key}"]`);
                    if (field) {
                        if (field.type === 'radio') {
                            if (field.value === value) {
                                field.checked = true;
                            }
                        } else {
                            field.value = value;
                        }
                    }
                });

                const paymentLabels = document.querySelectorAll('.payment-method-label');
                const updatePaymentSelection = () => {
                    paymentLabels.forEach(label => {
                        const input = label.querySelector('.payment-method-input');
                        const radioIcon = label.querySelector('.radio-icon');
                        const innerDot = radioIcon.querySelector('div');
                        if (input.checked) {
                            label.classList.add('selected');
                            radioIcon.classList.add('border-blue-600');
                            innerDot.classList.remove('hidden');
                        } else {
                            label.classList.remove('selected');
                            radioIcon.classList.remove('border-blue-600');
                            innerDot.classList.add('hidden');
                        }
                    });
                };
                paymentLabels.forEach(label => {
                    label.addEventListener('click', () => {
                        label.querySelector('.payment-method-input').checked = true;
                        updatePaymentSelection();
                    });
                });

                function formatStop(stop) {
                    if (!stop.id) {
                        return stop.text;
                    }
                    var $stop = $(
                        '<span>' + stop.text + '</span><br/><small class="text-gray-500">' + (stop.element.getAttribute('data-address') || '') + '</small>'
                    );
                    return $stop;
                }

                $('#pickup_stop_id, #dropoff_stop_id').select2({
                    templateResult: formatStop,
                    templateSelection: function (stop) {
                        return stop.text;
                    }
                });

                const hotelPickupWrapper = document.getElementById('hotel-pickup-address-wrapper');
                const hotelPickupInput = document.getElementById('hotel_pickup_address');
                const pickupSelect = $('#pickup_stop_id');

                function toggleHotelPickupField() {
                    if (pickupSelect.val() === 'hotel_pickup') {
                        hotelPickupWrapper.classList.remove('hidden');
                        hotelPickupInput.required = true;
                    } else {
                        hotelPickupWrapper.classList.add('hidden');
                        hotelPickupInput.required = false;
                    }
                }

                pickupSelect.on('change', toggleHotelPickupField);

                toggleHotelPickupField();
                updateSummary();
                updatePaymentSelection();
            });
        </script>
    @endpush
</x-client.layout>
