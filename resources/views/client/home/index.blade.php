<x-client.layout :web-profile="$web_profile ?? null" :main-menu="$mainMenu ?? []" :title="$web_profile->title ?? 'King Express Bus - Hành trình an toàn, trải nghiệm đẳng cấp'" :description="$web_profile->description ??
    'Dịch vụ xe khách chất lượng cao với limousine thương gia, giường nằm cao cấp và hệ sinh thái đối tác toàn quốc.'">
    @php
        $searchData = $searchData ?? [];
        $heroSlides = collect($heroSlides ?? []);
        $primaryHero = $heroSlides->first();
    @endphp

    @push('styles')
        <style>
            #hero-background-image {
                object-position: center;
                transition: opacity 0.6s ease;
            }

            #hero-section .hero-gradient {
                background: linear-gradient(180deg, rgba(15, 23, 42, 0.25) 0%, rgba(15, 23, 42, 0.55) 45%, rgba(15, 23, 42, 0.8) 100%);
            }

            .hero-floating-card {
                /*background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.82));*/
                backdrop-filter: blur(18px);
            }

            .hero-indicator {
                width: 12px;
                height: 12px;
                border-radius: 9999px;
                background: rgba(255, 255, 255, 0.35);
                transition: transform 0.3s ease, background 0.3s ease;
            }

            .hero-indicator[data-active="true"] {
                background: rgba(255, 255, 255, 0.95);
                transform: scale(1.2);
            }

            .media-card,
            .gallery-card,
            .bus-highlight-card,
            .testimonial-card {
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }

            .media-card:hover,
            .gallery-card:hover,
            .bus-highlight-card:hover,
            .testimonial-card:hover {
                transform: translateY(-6px);
                box-shadow: 0 18px 36px rgba(15, 23, 42, 0.25);
            }
        </style>
    @endpush

    @if ($primaryHero)
        <section id="hero-section" class="relative overflow-hidden text-white min-h-[70vh]">
            <div class="absolute inset-0">
                <img id="hero-background-image" src="{{ $primaryHero['image'] }}" alt="{{ $primaryHero['title'] }}"
                    class="h-full w-full object-cover" loading="lazy">
            </div>
            <div class="hero-gradient absolute inset-0 pointer-events-none"></div>
            <div class="relative z-10">
                <div class="container mx-auto px-4 py-24 lg:py-32">
                    <div class="w-full space-y-8">
                        <p class="text-sm font-semibold tracking-[0.35em] uppercase text-white/70">King Express Bus</p>
                        <h1 id="hero-title" class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight">
                            {{ $primaryHero['title'] }}</h1>
                        <p id="hero-subtitle" class="text-lg md:text-xl text-white/80 leading-relaxed">
                            {{ $primaryHero['subtitle'] }}</p>
                        <div class="hero-floating-card w-full rounded-3xl shadow-2xl p-6 md:p-8 text-slate-900">
                            <x-client.search-bar :search-data="$searchData" submit-label="Tìm tuyến" />
                        </div>
                        @if ($heroSlides->count() > 1)
                            <div class="flex items-center gap-3 pt-4">
                                @foreach ($heroSlides as $index => $slide)
                                    <button type="button" class="hero-indicator" data-hero-index="{{ $index }}"
                                        data-active="{{ $index === 0 ? 'true' : 'false' }}"
                                        aria-label="Hero slide {{ $index + 1 }}"></button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @endif


    @if ($popularRoutes->isNotEmpty())
        <section class="py-16 bg-slate-50">
            <div class="container mx-auto px-4 space-y-8">
                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
                    <div class="space-y-2">
                        <h2 class="text-3xl font-bold text-slate-900">Tuyến phổ biến</h2>
                        <p class="text-slate-600 max-w-2xl">Lựa chọn hành trình được khách hàng tin tưởng, giá vé minh
                            bạch, nhiều khung giờ.</p>
                    </div>
                    <a href="{{ route('client.routes.search') }}"
                        class="inline-flex items-center gap-2 text-blue-600 font-semibold">
                        Tìm thêm tuyến
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                    @foreach ($popularRoutes as $route)
                        <article
                            class="bg-white border border-slate-100 rounded-3xl shadow-sm hover:shadow-lg transition">
                            <div class="h-40 rounded-t-3xl overflow-hidden">
                                <img src="{{ $route->thumbnail_url ?? '/userfiles/files/city_imgs/ha-noi.jpg' }}"
                                    alt="{{ $route->name }}" class="h-full w-full object-cover" loading="lazy">
                            </div>
                            <div class="p-6 space-y-3">
                                <h3 class="text-lg font-semibold text-slate-900">{{ $route->name }}</h3>
                                <p class="text-sm text-slate-600 h-16 overflow-hidden">
                                    {{ \Illuminate\Support\Str::limit($route->description, 120) }}</p>
                                <div class="flex items-center justify-between text-sm text-slate-500">
                                    <span><i class="fa-solid fa-clock text-blue-500"></i>
                                        {{ $route->duration ?? 'Đang cập nhật' }}</span>
                                    <span><i class="fa-solid fa-users text-emerald-500"></i>
                                        {{ $route->company_count }} nhà xe</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span
                                        class="text-blue-600 font-semibold">{{ $route->min_price ? 'Giá từ ' . number_format($route->min_price) . ' đ' : 'Liên hệ giá' }}</span>
                                    <a href="{{ route('client.routes.show', ['slug' => $route->slug]) }}"
                                        class="text-sm text-blue-600 hover:text-blue-700 font-semibold">Chi tiết</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($busHighlights->isNotEmpty())
        <section class="py-16 bg-slate-900 text-white">
            <div class="container mx-auto px-4 space-y-8">
                <div class="space-y-2">
                    <h2 class="text-3xl font-bold">Đội xe cao cấp</h2>
                    <p class="text-white/70 max-w-2xl">Cabin riêng tư, giường nằm thông minh và tiện ích đạt tiêu chuẩn
                        hàng đầu.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                    @foreach ($busHighlights as $bus)
                        <article class="bus-highlight-card bg-white/10 rounded-3xl overflow-hidden">
                            <div class="h-44">
                                <img src="{{ $bus->thumbnail_url ?? '/userfiles/files/kingexpressbus/sleeper/8.jpg' }}"
                                    alt="{{ $bus->name }}" class="h-full w-full object-cover" loading="lazy">
                            </div>
                            <div class="p-6 space-y-3">
                                <h3 class="text-lg font-semibold text-white">{{ $bus->name }}</h3>
                                <p class="text-sm text-white/70">{{ $bus->model_name ?? 'Dòng xe tiện nghi' }}</p>
                                <div class="flex flex-wrap gap-2 text-xs text-blue-100/90">
                                    @foreach ($bus->services as $service)
                                        <span class="bg-white/15 px-3 py-1 rounded-full">{{ $service }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($featuredCompanies->isNotEmpty())
        <section class="py-16 bg-white">
            <div class="container mx-auto px-4 space-y-8">
                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
                    <div class="space-y-2">
                        <h2 class="text-3xl font-bold text-slate-900">Đối tác tiêu biểu</h2>
                        <p class="text-slate-600 max-w-2xl">Những nhà xe đồng hành giúp hành khách yên tâm trên mọi
                            chặng đường.</p>
                    </div>
                    <a href="{{ route('client.companies.index') }}"
                        class="inline-flex items-center gap-2 text-blue-600 font-semibold">
                        Xem danh sách nhà xe
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($featuredCompanies as $company)
                        <article
                            class="bg-slate-50 border border-slate-100 rounded-3xl p-6 space-y-4 shadow-sm hover:shadow-lg transition">
                            <div class="flex items-center gap-4">
                                <div class="h-14 w-14 rounded-2xl bg-white overflow-hidden">
                                    <img src="{{ $company->thumbnail_url ?? '/userfiles/files/web information/logo.jpg' }}"
                                        alt="{{ $company->name }}" class="h-full w-full object-cover" loading="lazy">
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $company->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $company->route_count }} tuyến đang khai thác
                                    </p>
                                </div>
                            </div>
                            <p class="text-sm text-slate-600">
                                {{ \Illuminate\Support\Str::limit($company->description, 100) }}</p>
                            <a href="{{ route('client.companies.show', ['slug' => $company->slug]) }}"
                                class="inline-flex items-center gap-2 text-blue-600 font-semibold">
                                Xem chi tiết
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if (!empty($serviceHighlights))
        <section class="py-16 bg-slate-50">
            <div class="container mx-auto px-4 space-y-10">
                <div class="space-y-3 text-center">
                    <h2 class="text-3xl font-bold text-slate-900">Tiện ích nổi bật</h2>
                    <p class="text-slate-600 max-w-2xl mx-auto">Tiện nghi được trang bị đồng nhất trên đội xe King
                        Express Bus, mang đến trải nghiệm sang trọng và thoải mái.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach ($serviceHighlights as $highlight)
                        <article class="bg-white rounded-3xl shadow-sm hover:shadow-xl transition overflow-hidden">
                            <div class="h-44">
                                <img src="{{ $highlight['image'] }}" alt="{{ $highlight['title'] }}"
                                    class="h-full w-full object-cover" loading="lazy">
                            </div>
                            <div class="p-6 space-y-3">
                                <div class="flex items-center gap-3 text-blue-600">
                                    <i class="{{ $highlight['icon'] }} text-xl"></i>
                                    <h3 class="text-lg font-semibold text-slate-900">{{ $highlight['title'] }}</h3>
                                </div>
                                <p class="text-sm text-slate-600">{{ $highlight['description'] }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if (!empty($mediaShowcase))
        <section class="py-16 bg-white">
            <div class="container mx-auto px-4 space-y-8">
                <div class="space-y-2">
                    <h2 class="text-3xl font-bold text-slate-900">Hình ảnh hành trình</h2>
                    <p class="text-slate-600 max-w-2xl">Trọn vẹn trải nghiệm từ cabin, tiện ích đến điểm đến.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach ($mediaShowcase as $media)
                        <article
                            class="media-card bg-slate-50 border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
                            <img src="{{ $media['asset'] }}" alt="{{ $media['title'] }}"
                                class="h-48 w-full object-cover" loading="lazy">
                            <div class="p-6 space-y-2">
                                <h3 class="text-lg font-semibold text-slate-900">{{ $media['title'] }}</h3>
                                <p class="text-sm text-slate-600">{{ $media['description'] }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if (!empty($galleryImages))
        <section class="py-16 bg-slate-50">
            <div class="container mx-auto px-4 space-y-8">
                <div class="space-y-2">
                    <h2 class="text-3xl font-bold text-slate-900">Thư viện hình ảnh</h2>
                    <p class="text-slate-600">Những khoảnh khắc đẹp trên các chặng đường phía trước.</p>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach ($galleryImages as $image)
                        <figure class="gallery-card rounded-3xl overflow-hidden">
                            <img src="{{ $image['url'] }}" alt="{{ $image['title'] }}"
                                class="h-48 w-full object-cover" loading="lazy">
                            <figcaption class="px-4 py-3 bg-white text-sm text-slate-600">
                                {{ $image['title'] }}
                            </figcaption>
                        </figure>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if (!empty($testimonials))
        <section class="py-16 bg-white">
            <div class="container mx-auto px-4 space-y-8">
                <div class="space-y-2">
                    <h2 class="text-3xl font-bold text-slate-900">Khách hàng nói gì</h2>
                    <p class="text-slate-600 max-w-2xl">Đánh giá thực tế từ hành khách đã trải nghiệm King Express Bus.
                    </p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach ($testimonials as $testimonial)
                        <blockquote
                            class="testimonial-card h-full bg-slate-50 border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-12 w-12 rounded-full bg-blue-500/10 text-blue-600 flex items-center justify-center text-xl">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $testimonial['name'] }}</p>
                                    <p class="text-xs text-slate-500">Tuyến {{ $testimonial['route'] }}</p>
                                </div>
                            </div>
                            <p class="text-sm text-slate-600 leading-relaxed">“{{ $testimonial['quote'] }}”</p>
                        </blockquote>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if (!empty($partnerLogos))
        <section class="py-16 bg-slate-900 text-white">
            <div class="container mx-auto px-4 space-y-8">
                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
                    <div>
                        <h2 class="text-3xl font-bold">Đối tác chiến lược</h2>
                        <p class="text-white/70 mt-3 max-w-2xl">Kết hợp cùng các nhà xe uy tín trên toàn quốc để đảm
                            bảo chất lượng dịch vụ.</p>
                    </div>
                    <a href="{{ route('client.companies.index') }}"
                        class="inline-flex items-center gap-2 font-semibold text-white/90 hover:text-white">
                        Xem tất cả đối tác
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-6">
                    @foreach ($partnerLogos as $logo)
                        <div
                            class="bg-white/10 hover:bg-white/20 rounded-2xl p-6 flex items-center justify-center transition-colors">
                            <img src="{{ $logo }}" alt="Đối tác King Express Bus"
                                class="max-h-16 w-auto object-contain" loading="lazy">
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const slides = @json($heroSlides);
                if (!Array.isArray(slides) || slides.length === 0) {
                    return;
                }

                const backgroundImage = document.getElementById('hero-background-image');
                const titleEl = document.getElementById('hero-title');
                const subtitleEl = document.getElementById('hero-subtitle');
                const indicators = document.querySelectorAll('[data-hero-index]');

                if (!backgroundImage || !titleEl || !subtitleEl) {
                    return;
                }

                let activeIndex = 0;

                const setIndicatorState = (index) => {
                    indicators.forEach((indicator) => {
                        indicator.dataset.active = indicator.dataset.heroIndex === String(index);
                    });
                };

                const applySlide = (index) => {
                    const slide = slides[index];
                    if (!slide) {
                        return;
                    }

                    const handleImageLoad = () => {
                        backgroundImage.classList.remove('opacity-0');
                    };

                    backgroundImage.classList.add('opacity-0');
                    backgroundImage.addEventListener('load', handleImageLoad, {
                        once: true
                    });
                    backgroundImage.src = slide.image;
                    backgroundImage.alt = slide.title;

                    titleEl.textContent = slide.title;
                    subtitleEl.textContent = slide.subtitle;

                    setIndicatorState(index);
                    activeIndex = index;
                };

                indicators.forEach((indicator) => {
                    indicator.addEventListener('click', () => {
                        const nextIndex = Number(indicator.dataset.heroIndex || 0);
                        applySlide(nextIndex);
                    });
                });

                setIndicatorState(activeIndex);

                if (backgroundImage.complete) {
                    backgroundImage.classList.remove('opacity-0');
                } else {
                    backgroundImage.addEventListener('load', () => {
                        backgroundImage.classList.remove('opacity-0');
                    }, {
                        once: true
                    });
                }

                if (slides.length > 1) {
                    setInterval(() => {
                        const next = (activeIndex + 1) % slides.length;
                        applySlide(next);
                    }, 6500);
                }
            });
        </script>
    @endpush
</x-client.layout>
