<x-client.layout :title="$title" :description="$description">
    {{-- Hero Section --}}
    <div class="relative h-64 bg-cover bg-center"
         style="background-image: url('{{asset('/userfiles/files/city_imgs/ha-noi.jpg')}}')">
        <div class="absolute inset-0 bg-black opacity-50"></div>
        <div
            class="relative container mx-auto px-4 h-full flex flex-col justify-center items-center text-white text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold leading-tight">{{ $title }}</h1>
            <p class="text-lg md:text-xl mt-4 max-w-3xl">{{ $description }}</p>
        </div>
    </div>

    <div class="bg-gray-50 py-16">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                {{-- Left Column: Support Channels --}}
                <div class="lg:col-span-2">
                    <div class="bg-white p-8 rounded-2xl shadow-lg">
                        <h2 class="text-3xl font-bold text-gray-800 mb-8">{{ __('client.contact.headings.support_channels') }}</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            @foreach ($supportChannels as $channel)
                                <a href="{{ $channel['href'] }}" target="_blank"
                                   class="group flex items-center p-5 bg-gray-100 rounded-xl shadow-sm hover:shadow-lg hover:bg-blue-50 transition-all duration-300">
                                    <div
                                        class="flex-shrink-0 w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center">
                                        <i class="{{ $channel['icon'] }} text-2xl"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="font-semibold text-lg text-gray-800">{{ $channel['label'] }}</p>
                                        <p class="text-gray-600">{{ $channel['value'] }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Right Column: Working Hours, FAQ, Offices --}}
                <div class="space-y-8">
                    {{-- Working Hours --}}
                    <div class="bg-white p-6 rounded-2xl shadow-lg">
                        <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fa-regular fa-clock text-blue-600 mr-3"></i>
                            {{ __('client.contact.headings.working_hours') }}
                        </h3>
                        <div class="text-gray-700 space-y-2">
                            <p><span
                                    class="font-semibold">{{ $workingHours['weekday_label'] }}:</span> {{ $workingHours['weekday_hours'] }}
                            </p>
                            <p><span
                                    class="font-semibold">{{ $workingHours['weekend_label'] }}:</span> {{ $workingHours['weekend_hours'] }}
                            </p>
                        </div>
                    </div>

                    {{-- Offices --}}
                    <div class="bg-white p-6 rounded-2xl shadow-lg">
                        <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fa-solid fa-building text-blue-600 mr-3"></i>
                            {{ __('client.contact.headings.offices') }}
                        </h3>
                        <div class="space-y-4 max-h-80 overflow-y-auto pr-2">
                            @foreach ($offices as $office)
                                <div class="border-b border-gray-200 pb-3 mb-3">
                                    <p class="font-semibold text-gray-800">{{ $office->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $office->address }}
                                        , {{ $office->district_name }},
                                        {{ $office->province_name }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- FAQs Section --}}
            <div class="mt-16">
                <div class="bg-white p-8 rounded-2xl shadow-lg max-w-4xl mx-auto">
                    <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">{{ __('client.contact.headings.faq') }}</h2>
                    <div class="space-y-4">
                        @foreach ($faqs as $faq)
                            <div x-data="{ open: false }" class="border-b border-gray-200 pb-4">
                                <button @click="open = !open"
                                        class="w-full flex justify-between items-center text-left text-lg font-semibold text-gray-800">
                                    <span>{{ $faq['question'] }}</span>
                                    <i class="fas transition-transform duration-300"
                                       :class="open ? 'fa-minus' : 'fa-plus'"></i>
                                </button>
                                <div x-show="open" x-transition class="text-gray-600 mt-3 pl-2">
                                    {{ $faq['answer'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Map --}}
            @if ($mapEmbed)
                <div class="mt-16 bg-white p-8 rounded-2xl shadow-lg">
                    <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">{{ __('client.contact.headings.map') }}</h2>
                    <div class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden">
                        {!! $mapEmbed !!}
                    </div>
                </div>
            @endif

        </div>
    </div>
    @push('styles')
        <style>
            .aspect-w-16 {
                position: relative;
                padding-bottom: 56.25%;
            }

            .aspect-h-9 {
            }

            .aspect-w-16 > iframe {
                position: absolute;
                width: 100%;
                height: 100%;
                top: 0;
                left: 0;
                border: 0;
            }
        </style>
    @endpush
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @endpush
</x-client.layout>
