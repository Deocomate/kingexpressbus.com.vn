<li class="sortable-item" data-id="{{ $item->id }}">
    <div class="sortable-handle">
        <i class="fas fa-arrows-alt sortable-drag-handle"></i>
        <span class="sortable-title">{{ $item->name }}</span>
        <small class="sortable-details ml-2">({{ Str::limit($item->url, 30) }})</small>
        <div class="sortable-actions">
            <button class="btn btn-info btn-xs mr-2 btn-edit-menu" data-id="{{ $item->id }}">
                <i class="fas fa-pencil-alt"></i> Sửa
            </button>
            <button class="btn btn-danger btn-xs btn-delete-menu" data-id="{{ $item->id }}">
                <i class="fas fa-trash"></i> Xóa
            </button>
        </div>
    </div>
    <ol class="sortable-children">
    </ol>
    @if (!empty($item->children))
        <ol class="sortable-children">
            @foreach ($item->children as $child)
                @include('admin.menus.menu_item', ['item' => $child])
            @endforeach
        </ol>
    @endif
</li>
