{{-- Main Sidebar Container --}}
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('admin.dashboard.index') }}" class="brand-link">
        <img src="{{ asset('/admin/dist/img/AdminLTELogo.png') }}"
             alt="AdminLTE Logo"
             class="brand-image img-circle elevation-3"
             style="opacity: .8">
        <span class="brand-text font-weight-light">{{ config('app.name') }}</span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            {{-- Menu Component --}}
            <x-admin.main-menu-bar :auth-user="$authUser"/>
        </nav>
    </div>
</aside>
