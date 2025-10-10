{{-- Social Sharing Component --}}
@props(['url' => null, 'title' => null, 'description' => null])

@php
    $shareUrl = $url ?? url()->current();
    $shareTitle = $title ?? ($web_profile->title ?? config('app.name'));
    $shareDescription = $description ?? ($web_profile->description ?? '');
    $encodedUrl = urlencode($shareUrl);
    $encodedTitle = urlencode($shareTitle);
    $encodedDescription = urlencode($shareDescription);
@endphp

<div class="flex items-center gap-3" {{ $attributes }}>
    <span class="text-sm font-medium text-slate-700">{{ __('client.share.label') }}:</span>

    {{-- Facebook --}}
    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $encodedUrl }}"
       target="_blank"
       rel="noopener noreferrer"
       class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-600 text-white hover:bg-blue-700 transition"
       aria-label="Share on Facebook">
        <i class="fab fa-facebook-f"></i>
    </a>

    {{-- Twitter/X --}}
    <a href="https://twitter.com/intent/tweet?url={{ $encodedUrl }}&text={{ $encodedTitle }}"
       target="_blank"
       rel="noopener noreferrer"
       class="flex items-center justify-center w-10 h-10 rounded-full bg-black text-white hover:bg-gray-800 transition"
       aria-label="Share on Twitter">
        <i class="fab fa-twitter"></i>
    </a>

    {{-- LinkedIn --}}
    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $encodedUrl }}"
       target="_blank"
       rel="noopener noreferrer"
       class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-700 text-white hover:bg-blue-800 transition"
       aria-label="Share on LinkedIn">
        <i class="fab fa-linkedin-in"></i>
    </a>

    {{-- Email --}}
    <a href="mailto:?subject={{ $encodedTitle }}&body={{ $encodedDescription }}%20{{ $encodedUrl }}"
       class="flex items-center justify-center w-10 h-10 rounded-full bg-slate-600 text-white hover:bg-slate-700 transition"
       aria-label="Share via Email">
        <i class="fas fa-envelope"></i>
    </a>

    {{-- Copy Link --}}
    <button type="button"
            onclick="copyToClipboard('{{ $shareUrl }}')"
            class="flex items-center justify-center w-10 h-10 rounded-full bg-slate-200 text-slate-700 hover:bg-slate-300 transition"
            aria-label="Copy link">
        <i class="fas fa-link"></i>
    </button>
</div>

@once
    @push('scripts')
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                toastr.success('{{ __("client.share.copied") }}');
            }, function() {
                toastr.error('{{ __("client.share.copy_failed") }}');
            });
        }
    </script>
    @endpush
@endonce
