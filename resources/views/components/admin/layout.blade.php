<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }}</title>
    <x-admin.styles/>
    @stack('styles')
</head>
<body class="{{ $bodyClass }}">
<div class="wrapper">

    {{-- Navbar Component --}}
    <x-admin.navbar :auth-user="$authUser"/>

    {{-- Sidebar Component --}}
    <x-admin.sidebar :auth-user="$authUser" :web-profile="$webProfile"/>

    {{-- Content Wrapper --}}
    <div class="content-wrapper">
        {{-- Content Header --}}
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ $title }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard.index') }}">Dashboard</a>
                            </li>
                            @isset($breadcrumb)
                                {{ $breadcrumb }}
                            @else
                                <li class="breadcrumb-item active">{{ $title }}</li>
                            @endisset
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        {{-- Main Content --}}
        <section class="content">
            <div class="container-fluid">
                {{-- Alert Messages Component --}}
                <x-admin.alert-messages/>

                {{-- Page Content Slot --}}
                {{ $slot }}
            </div>
        </section>
    </div>

    {{-- Footer Component --}}
    <x-admin.footer :web-profile="$webProfile"/>

</div>
<x-admin.scripts/>
@stack('scripts')
</body>
</html>
