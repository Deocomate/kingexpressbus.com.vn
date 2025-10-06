<x-client.layout :title="$title" :description="$description">
    {{-- Hero Section --}}
    <section class="relative bg-slate-900 text-white overflow-hidden">
        <div class="absolute inset-0 opacity-60">
            <img src="/userfiles/files/kingexpressbus/cabin/5.jpg" alt="{{ __('client.about.hero.image_alt') }}"
                 class="h-full w-full object-cover">
        </div>
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/80 to-transparent"></div>
        <div class="relative container mx-auto px-4 py-20 text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold">{{ __('client.about.hero.title') }}</h1>
            <p class="mt-4 text-lg md:text-xl text-white/80 max-w-3xl mx-auto">{{ __('client.about.hero.subtitle') }}</p>
        </div>
    </section>

    {{-- Introduction Section --}}
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4 text-center max-w-4xl">
            <h2 class="text-3xl font-bold text-slate-900">{{ __('client.about.intro.title') }}</h2>
            <p class="mt-4 text-lg text-slate-600 leading-relaxed">
                {{ __('client.about.intro.paragraph1') }}
            </p>
            <p class="mt-4 text-lg text-slate-600 leading-relaxed">
                {{ __('client.about.intro.paragraph2') }}
            </p>
        </div>
    </section>

    {{-- Strengths Section --}}
    <section class="py-16 bg-slate-50">
        <div class="container mx-auto px-4 space-y-12">
            <div class="text-center max-w-3xl mx-auto">
                <h2 class="text-3xl font-bold text-slate-900">{{ __('client.about.strengths.title') }}</h2>
                <p class="mt-4 text-lg text-slate-600">{{ __('client.about.strengths.subtitle') }}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach(__('client.about.strengths.items') as $item)
                    <div class="bg-white p-6 rounded-2xl shadow-sm hover:shadow-lg transition-shadow duration-300">
                        <div
                            class="flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 text-blue-600 mb-4">
                            <i class="{{ $item['icon'] }} text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-slate-800">{{ $item['title'] }}</h3>
                        <p class="mt-2 text-slate-600">{{ $item['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Stats Section --}}
    <section class="py-16 bg-blue-600 text-white">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div>
                    <p class="text-4xl font-extrabold">{{ number_format($stats['route_count']) }}+</p>
                    <p class="mt-2 text-lg text-blue-200">{{ __('client.about.stats.routes') }}</p>
                </div>
                <div>
                    <p class="text-4xl font-extrabold">{{ number_format($stats['company_count']) }}+</p>
                    <p class="mt-2 text-lg text-blue-200">{{ __('client.about.stats.partners') }}</p>
                </div>
                <div>
                    <p class="text-4xl font-extrabold">{{ number_format($stats['customer_count']) }}+</p>
                    <p class="mt-2 text-lg text-blue-200">{{ __('client.about.stats.customers') }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Vision Section --}}
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4 text-center max-w-4xl">
            <h2 class="text-3xl font-bold text-slate-900">{{ __('client.about.vision.title') }}</h2>
            <p class="mt-4 text-lg text-slate-600 leading-relaxed">
                {{ __('client.about.vision.description') }}
            </p>
        </div>
    </section>

    {{-- Call to Action Section --}}
    <section class="py-16 bg-slate-50">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-slate-900">{{ __('client.about.cta.title') }}</h2>
            <p class="mt-4 text-lg text-slate-600 max-w-2xl mx-auto">
                {{ __('client.about.cta.subtitle') }}
            </p>
            <a href="{{ route('client.home') }}"
               class="mt-8 inline-block bg-yellow-400 text-slate-900 font-bold px-8 py-4 rounded-xl shadow-lg hover:bg-yellow-500 transition-transform transform hover:scale-105">
                {{ __('client.about.cta.button') }}
            </a>
        </div>
    </section>
</x-client.layout>
