@php
    $brandTitle = data_get($webProfile, 'title', config('app.name'));
    $brandLogo = data_get($webProfile, 'logo_url');
@endphp
<footer class="bg-gray-800 text-white">
    <div class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
            <div>
                @if($webProfile)
                    <h3 class="text-xl font-bold mb-4 flex items-center">
                        @if($brandLogo)
                            <img src="{{ $brandLogo }}" alt="Logo" class="h-8 mr-2 filter rounded">
                        @endif
                        <span>{{ $brandTitle }}</span>
                    </h3>
                    <p class="text-gray-400 text-sm leading-relaxed">{{ data_get($webProfile, 'description', 'Nhà xe uy tín, chất lượng hàng đầu cho mọi hành trình của bạn.') }}</p>
                    <div class="flex space-x-4 mt-6">
                        @if(data_get($webProfile, 'facebook_url'))
                            <a href="{{ data_get($webProfile, 'facebook_url') }}" target="_blank" aria-label="Facebook"
                               class="text-gray-400 hover:text-white transition-colors duration-200 text-xl"><i
                                    class="fab fa-facebook-f"></i></a>
                        @endif
                        @if(data_get($webProfile, 'zalo_url'))
                            <a href="{{ data_get($webProfile, 'zalo_url') }}" target="_blank" aria-label="Zalo"
                               class="text-gray-400 hover:text-white transition-colors duration-200 text-xl"><i
                                    class="fa-solid fa-comment-dots"></i></a>
                        @endif
                        @if(data_get($webProfile, 'whatsapp'))
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', data_get($webProfile, 'whatsapp')) }}" target="_blank" aria-label="WhatsApp"
                               class="text-gray-400 hover:text-white transition-colors duration-200 text-xl"><i
                                    class="fab fa-whatsapp"></i></a>
                        @endif
                        <a href="#" aria-label="Youtube"
                           class="text-gray-400 hover:text-white transition-colors duration-200 text-xl"><i
                                class="fab fa-youtube"></i></a>
                        <a href="#" aria-label="Instagram"
                           class="text-gray-400 hover:text-white transition-colors duration-200 text-xl"><i
                                class="fab fa-instagram"></i></a>
                    </div>
                @endif
            </div>
            <div>
                <h4 class="font-bold text-lg mb-4 tracking-wider">Về chúng tôi</h4>
                <ul class="space-y-3">
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Giới thiệu</a>
                    </li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Đội xe</a>
                    </li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Tin tức</a>
                    </li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-lg mb-4 tracking-wider">Hỗ trợ</h4>
                <ul class="space-y-3">
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Chính sách hủy vé</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Điều khoản dịch vụ</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-200">Bảo mật thông tin</a></li>
                </ul>
            </div>
            <div>
                @if($webProfile)
                    <h4 class="font-bold text-lg mb-4 tracking-wider">Liên hệ</h4>
                    <ul class="space-y-3 text-gray-400 text-sm">
                        @if(data_get($webProfile, 'address'))
                            <li class="flex items-start"><i class="fas fa-map-marker-alt mt-1 mr-3 w-4 text-center"></i><span>{{ data_get($webProfile, 'address') }}</span>
                            </li>
                        @endif
                        @if(data_get($webProfile, 'phone'))
                            <li class="flex items-center"><i class="fas fa-phone-alt mr-3 w-4 text-center"></i><a
                                    href="tel:{{ str_replace([' ', '.'], '', data_get($webProfile, 'phone')) }}"
                                    class="hover:text-white transition-colors duration-200">{{ data_get($webProfile, 'phone') }}</a>
                            </li>
                        @endif
                        @if(data_get($webProfile, 'hotline'))
                            <li class="flex items-center"><i class="fas fa-headset mr-3 w-4 text-center"></i><a
                                    href="tel:{{ str_replace([' ', '.'], '', data_get($webProfile, 'hotline')) }}"
                                    class="hover:text-white transition-colors duration-200">{{ data_get($webProfile, 'hotline') }}
                                    (Hotline)</a></li>
                        @endif
                        @if(data_get($webProfile, 'email'))
                            <li class="flex items-center"><i class="fas fa-envelope mr-3 w-4 text-center"></i><a
                                    href="mailto:{{ data_get($webProfile, 'email') }}"
                                    class="hover:text-white transition-colors duration-200">{{ data_get($webProfile, 'email') }}</a>
                            </li>
                        @endif
                    </ul>
                @endif
            </div>
        </div>
        <div class="mt-10 pt-8 border-t border-gray-700 text-center text-gray-500 text-sm">
            &copy; {{ date('Y') }} {{ $brandTitle }}. All rights reserved.
        </div>
    </div>
</footer>
