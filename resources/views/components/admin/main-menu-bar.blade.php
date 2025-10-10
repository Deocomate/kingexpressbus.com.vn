<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

    @if ($userRole === 'admin')
        {{-- ========================================================== --}}
        {{-- |                     MENU CHO ADMIN                     | --}}
        {{-- ========================================================== --}}
        <li class="nav-header">QUẢN LÝ TỔNG QUAN</li>

        <x-menus.menu-bar
            :route="route('admin.dashboard.index')"
            name="Tổng quan"
            icon="fas fa-tachometer-alt"
            :route-group="['admin.dashboard.*']"/>

        <x-menus.menu-bar
            :route="route('admin.bookings.index')"
            name="Quản lý Đặt vé"
            icon="fas fa-ticket-alt"
            :route-group="['admin.bookings.*']"/>

        <x-menus.menu-bar
            :route="route('admin.companies.index')"
            name="Quản lý Nhà xe"
            icon="fas fa-building"
            :route-group="['admin.companies.*']"/>

        <li class="nav-header">QUẢN LÝ DANH MỤC</li>

        <x-menus.menu-bar
            icon="fas fa-map-marked-alt"
            name="Quản lý Địa điểm"
            :route-group="['admin.provinces.*', 'admin.districts.*', 'admin.district-types.*', 'admin.stops.*']">
            <x-menus.menu-item name="Tỉnh/Thành phố" :route="route('admin.provinces.index')"/>
            <x-menus.menu-item name="Quận/Huyện" :route="route('admin.districts.index')"/>
            <x-menus.menu-item name="Loại địa điểm" :route="route('admin.district-types.index')"/>
            <x-menus.menu-item name="Điểm dừng" :route="route('admin.stops.index')"/>
        </x-menus.menu-bar>

        <x-menus.menu-bar
            :route="route('admin.bus-services.index')"
            name="Dịch vụ Xe"
            icon="fas fa-concierge-bell"
            :route-group="['admin.bus-services.*']"/>

        <x-menus.menu-bar
            :route="route('admin.routes.index')"
            name="Quản lý Tuyến đường"
            icon="fas fa-route"
            :route-group="['admin.routes.*']"/>

        <li class="nav-header">HỆ THỐNG</li>

        <x-menus.menu-bar
            icon="fas fa-cogs"
            name="Giao diện & Cài đặt"
            :route-group="['admin.menus.*', 'admin.web_profiles.*']">
            <x-menus.menu-item name="Quản lý Menu" :route="route('admin.menus.index')"/>
            <x-menus.menu-item name="Thông tin Website" :route="route('admin.web_profiles.index')"/>
        </x-menus.menu-bar>

    @elseif($userRole === 'company')
        {{-- ========================================================== --}}
        {{-- |                    MENU CHO COMPANY                    | --}}
        {{-- ========================================================== --}}
        <li class="nav-header">QUẢN LÝ NHÀ XE</li>

        <x-menus.menu-bar
            :route="route('company.dashboard.index')"
            name="Tổng quan"
            icon="fas fa-tachometer-alt"
            :route-group="['company.dashboard.*']"/>

        <x-menus.menu-bar
            :route="route('company.bookings.index')"
            name="Quản lý Đặt vé"
            icon="fas fa-ticket-alt"
            :route-group="['company.bookings.*']"/>

        <x-menus.menu-bar
            icon="fas fa-bus"
            name="Quản lý Vận hành"
            :route-group="['company.buses.*', 'company.routes.*', 'company.bus_routes.*']">
            <x-menus.menu-item name="Danh sách xe" :route="route('company.buses.index')"/>
            <x-menus.menu-item name="Tuyến đường của nhà xe" :route="route('company.company-routes.index')"/>
            <x-menus.menu-item name="Quản lý Chuyến xe" :route="route('company.bus-routes.index')"/>
        </x-menus.menu-bar>

        <x-menus.menu-bar
            :route="route('company.profile.index')"
            name="Thông tin Nhà xe"
            icon="fas fa-id-card"
            :route-group="['company.profile.*']"/>

    @endif
</ul>
