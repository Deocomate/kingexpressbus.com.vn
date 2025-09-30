{{-- Xóa toàn bộ khối @props và @php ở đây --}}

<li class="nav-item {{ $isActive ? 'menu-open' : '' }}">
    {{-- Nếu không có slot (menu con), thì link của menu cha sẽ là route được truyền vào --}}
    <a href="{{ $slot->isEmpty() ? $route : '#' }}" class="nav-link {{ $isActive ? 'active' : '' }}">
        <i class="nav-icon {{ $icon }}"></i>
        <p>
            {{ $name }}
            {{-- Chỉ hiển thị mũi tên khi có menu con --}}
            @if(!$slot->isEmpty())
                <i class="right fas fa-angle-left"></i>
            @endif
        </p>
    </a>
    {{-- Hiển thị menu con nếu có --}}
    @if(!$slot->isEmpty())
        <ul class="nav nav-treeview">
            {{ $slot }}
        </ul>
    @endif
</li>
