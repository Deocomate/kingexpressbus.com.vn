<x-admin.layout title="Quản lý Menu">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Quản lý Menu</li>
    </x-slot:breadcrumb>

    @push('styles')
        <style>
            .menu-builder-container {
                min-height: 300px;
                background-color: #f8f9fa;
                border: 1px dashed #ced4da;
                border-radius: .25rem;
                padding: 1rem;
            }

            .sortable-list {
                list-style: none;
                padding-left: 0;
                margin: 0;
            }

            .sortable-item {
                background-color: #fff;
                border: 1px solid #dee2e6;
                border-radius: .25rem;
                margin-bottom: 8px;
                padding: 0;
                box-shadow: 0 1px 3px rgba(0, 0, 0, .05);
            }

            .sortable-handle {
                display: flex;
                align-items: center;
                padding: .75rem 1.25rem;
                width: 100%;
            }

            .sortable-drag-handle {
                cursor: move;
                color: #adb5bd;
                margin-right: 15px;
            }

            .sortable-title {
                font-weight: 600;
                color: #495057;
            }

            .sortable-details {
                margin-left: 10px;
                font-size: 0.8em;
                color: #888;
            }

            .sortable-actions {
                margin-left: auto;
                display: flex;
                align-items: center;
            }

            .sortable-children {
                list-style: none;
                padding-left: 40px;
                margin: 0;
                border-top: 1px solid #f1f3f5;
            }

            .sortable-item .sortable-children {
                padding-top: 10px;
                padding-bottom: 10px;
            }

            .sortable-ghost {
                background-color: #e9ecef;
                border: 1px dashed #adb5bd;
                opacity: 0.7;
            }

            .sortable-drag {
                box-shadow: 0 4px 12px rgba(0, 0, 0, .15);
                opacity: 0.95;
            }
        </style>
    @endpush

    <div class="row">
        <div class="col-md-5">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Thêm mục vào menu</h3>
                </div>
                <div class="card-body">
                    <div class="accordion" id="menu-items-accordion">
                        <div class="card mb-2 shadow-none">
                            <div class="card-header p-0">
                                <h2 class="mb-0">
                                    <button class="btn btn-link btn-block text-left p-2" type="button"
                                            data-toggle="collapse" data-target="#collapseCustomLink">Liên kết tùy chỉnh
                                    </button>
                                </h2>
                            </div>
                            <div id="collapseCustomLink" class="collapse show" data-parent="#menu-items-accordion">
                                <div class="card-body">
                                    <div class="form-group"><label>Tên hiển thị</label><input type="text"
                                                                                              class="form-control"
                                                                                              id="custom-link-name"
                                                                                              placeholder="Tên menu">
                                    </div>
                                    <div class="form-group"><label>URL</label><input type="text" class="form-control"
                                                                                     id="custom-link-url"
                                                                                     placeholder="https://..."></div>
                                    <button type="button" class="btn btn-primary btn-sm btn-block add-to-menu"
                                            data-type="custom_link">Thêm vào menu
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-2 shadow-none">
                            <div class="card-header p-0">
                                <h2 class="mb-0">
                                    <button class="btn btn-link btn-block text-left p-2 collapsed" type="button"
                                            data-toggle="collapse" data-target="#collapseSystemPages">Trang hệ thống
                                    </button>
                                </h2>
                            </div>
                            <div id="collapseSystemPages" class="collapse" data-parent="#menu-items-accordion">
                                <div class="card-body">
                                    @foreach($systemPages as $page)
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span>{{ $page['name'] }}</span>
                                            <button type="button" class="btn btn-primary btn-xs add-to-menu"
                                                    data-type="system_page" data-name="{{ $page['name'] }}"
                                                    data-url="{{ $page['url'] }}"><i class="fas fa-plus"></i> Thêm
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="card mb-0 shadow-none">
                            <div class="card-header p-0">
                                <h2 class="mb-0">
                                    <button class="btn btn-link btn-block text-left p-2 collapsed" type="button"
                                            data-toggle="collapse" data-target="#collapseRoutes">Tuyến đường
                                    </button>
                                </h2>
                            </div>
                            <div id="collapseRoutes" class="collapse" data-parent="#menu-items-accordion">
                                <div class="card-body">
                                    <div class="form-group">
                                        <select name="route_id_select" class="form-control">
                                            <option value="">-- Chọn tuyến đường --</option>
                                            @foreach($routes as $route)
                                                <option value="{{ $route->id }}">{{ $route->title }}
                                                    ({{$route->start_province}} -> {{$route->end_province}})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="button" class="btn btn-primary btn-sm btn-block mt-2 add-to-menu"
                                            data-type="route">Thêm vào menu
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Cấu trúc Menu hiện tại</h3>
                    <div class="card-tools">
                        <button id="save-menu-order" class="btn btn-success btn-sm">
                            <i class="fas fa-save"></i> Lưu Cấu trúc Menu
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted"><i class="fas fa-info-circle"></i> Kéo thả để sắp xếp và tạo menu đa cấp.</p>
                    <div id="sortable-menu-container" class="menu-builder-container">
                        <ol class="sortable-list">
                            @foreach ($menuTree as $item)
                                @include('admin.menus.menu_item', ['item' => $item])
                            @endforeach
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editMenuModal" tabindex="-1" role="dialog" aria-labelledby="editMenuModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="editMenuForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editMenuModalLabel">Chỉnh sửa mục Menu</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit-menu-id" name="id">
                        <div class="form-group">
                            <label for="edit-menu-name">Tên hiển thị</label>
                            <input type="text" class="form-control" id="edit-menu-name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-menu-url">URL</label>
                            <input type="text" class="form-control" id="edit-menu-url" name="url">
                            <small id="edit-menu-url-help" class="form-text text-muted">Chỉ có thể sửa URL cho 'Liên kết
                                tùy chỉnh'.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const menuContainer = document.querySelector('#sortable-menu-container .sortable-list');
                const editModalEl = document.getElementById('editMenuModal');
                const editModal = new bootstrap.Modal(editModalEl);
                const editMenuForm = document.getElementById('editMenuForm');
                let triggerButton = null;

                editModalEl.addEventListener('hidden.bs.modal', function () {
                    if (triggerButton) {
                        triggerButton.focus();
                        triggerButton = null;
                    }
                });

                function initSortable(container) {
                    new Sortable(container, {
                        group: 'nested',
                        animation: 150,
                        fallbackOnBody: true,
                        swapThreshold: 0.65,
                        handle: '.sortable-drag-handle',
                        ghostClass: 'sortable-ghost',
                        dragClass: 'sortable-drag',
                        onEnd: function () {
                            document.getElementById('save-menu-order').classList.add('btn-warning');
                        }
                    });
                    container.querySelectorAll('.sortable-children').forEach(initSortable);
                }

                initSortable(menuContainer);

                function serializeMenu(list) {
                    const items = [];
                    list.querySelectorAll(':scope > .sortable-item').forEach(itemEl => {
                        const item = {id: itemEl.dataset.id};
                        const childrenList = itemEl.querySelector('.sortable-children');
                        if (childrenList && childrenList.children.length > 0) {
                            item.children = serializeMenu(childrenList);
                        }
                        items.push(item);
                    });
                    return items;
                }

                document.getElementById('save-menu-order').addEventListener('click', function (e) {
                    e.preventDefault();
                    const button = this;
                    const serializedData = serializeMenu(menuContainer);
                    button.disabled = true;
                    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';

                    fetch('{{ route("admin.menus.updateOrder") }}', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                        body: JSON.stringify({menuData: serializedData})
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                toastr.success(data.message);
                                button.classList.remove('btn-warning');
                            } else {
                                toastr.error('Lỗi: ' + (data.message || 'Không thể cập nhật.'));
                            }
                        })
                        .catch(() => toastr.error('Lỗi server. Vui lòng thử lại.'))
                        .finally(() => {
                            button.disabled = false;
                            button.innerHTML = '<i class="fas fa-save"></i> Lưu Cấu trúc Menu';
                        });
                });

                document.querySelectorAll('.add-to-menu').forEach(button => {
                    button.addEventListener('click', function (e) {
                        e.preventDefault();
                        const type = this.dataset.type;
                        let data = {type: type};
                        let isValid = true;

                        switch (type) {
                            case 'custom_link':
                                data.name = document.getElementById('custom-link-name').value.trim();
                                data.url = document.getElementById('custom-link-url').value.trim();
                                if (!data.name || !data.url) {
                                    toastr.warning('Vui lòng nhập đầy đủ Tên và URL.');
                                    isValid = false;
                                }
                                break;
                            case 'system_page':
                                data.name = this.dataset.name;
                                data.url = this.dataset.url;
                                break;
                            case 'route':
                                data.related_id = document.querySelector('select[name="route_id_select"]').value;
                                if (!data.related_id) {
                                    toastr.warning('Vui lòng chọn một Tuyến đường.');
                                    isValid = false;
                                }
                                break;
                        }

                        if (!isValid) return;

                        const originalText = this.innerHTML;
                        this.disabled = true;
                        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                        fetch('{{ route("admin.menus.addItem") }}', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                            body: JSON.stringify(data)
                        })
                            .then(response => response.json())
                            .then(resData => {
                                if (resData.success) {
                                    menuContainer.insertAdjacentHTML('beforeend', resData.html);
                                    toastr.success('Đã thêm mục vào menu. Hãy nhấn "Lưu Cấu trúc".');
                                    if (type === 'custom_link') {
                                        document.getElementById('custom-link-name').value = '';
                                        document.getElementById('custom-link-url').value = '';
                                    }
                                    document.getElementById('save-menu-order').classList.add('btn-warning');
                                } else {
                                    toastr.error('Lỗi: ' + (resData.message || 'Không thể thêm mục.'));
                                }
                            })
                            .catch(() => toastr.error('Lỗi server. Vui lòng thử lại.'))
                            .finally(() => {
                                this.disabled = false;
                                this.innerHTML = originalText;
                            });
                    });
                });

                menuContainer.addEventListener('click', function (e) {
                    const target = e.target;
                    const deleteButton = target.closest('.btn-delete-menu');
                    const editButton = target.closest('.btn-edit-menu');

                    if (deleteButton) {
                        e.preventDefault();
                        if (!confirm('Bạn có chắc muốn xóa mục menu này và các mục con của nó không?')) return;
                        const menuItem = deleteButton.closest('.sortable-item');
                        const menuId = menuItem.dataset.id;

                        fetch(`/admin/menus/${menuId}`, {
                            method: 'DELETE',
                            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    toastr.success(data.message);
                                    menuItem.remove();
                                } else {
                                    toastr.error('Lỗi: ' + data.message);
                                }
                            })
                            .catch(() => toastr.error('Lỗi server. Vui lòng thử lại.'));
                    }

                    if (editButton) {
                        e.preventDefault();
                        triggerButton = editButton;
                        const menuId = editButton.dataset.id;

                        fetch(`/admin/menus/${menuId}/edit`)
                            .then(response => response.json())
                            .then(res => {
                                if (res.success) {
                                    const menu = res.data;
                                    document.getElementById('edit-menu-id').value = menu.id;
                                    document.getElementById('edit-menu-name').value = menu.name;
                                    const urlInput = document.getElementById('edit-menu-url');
                                    urlInput.value = menu.url;

                                    if (menu.type === 'custom_link') {
                                        urlInput.readOnly = false;
                                        document.getElementById('edit-menu-url-help').style.display = 'block';
                                    } else {
                                        urlInput.readOnly = true;
                                        document.getElementById('edit-menu-url-help').style.display = 'none';
                                    }
                                    editModal.show();
                                } else {
                                    toastr.error('Lỗi: ' + res.message);
                                }
                            });
                    }
                });

                editMenuForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const menuId = document.getElementById('edit-menu-id').value;
                    const formData = new FormData(this);
                    const button = this.querySelector('button[type="submit"]');
                    const originalHtml = button.innerHTML;
                    button.disabled = true;
                    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';

                    fetch(`/admin/menus/${menuId}`, {
                        method: 'POST',
                        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                        body: new URLSearchParams({
                            _method: 'PUT',
                            name: formData.get('name'),
                            url: formData.get('url'),
                        })
                    })
                        .then(response => response.json())
                        .then(res => {
                            if (res.success) {
                                const updatedMenu = res.data;
                                const menuItemEl = menuContainer.querySelector(`.sortable-item[data-id="${updatedMenu.id}"]`);
                                if (menuItemEl) {
                                    menuItemEl.querySelector('.sortable-title').textContent = updatedMenu.name;
                                    menuItemEl.querySelector('.sortable-details').textContent = `(${updatedMenu.url.substring(0, 30)})`;
                                }
                                editModal.hide();
                                toastr.success(res.message);
                            } else {
                                toastr.error('Lỗi: ' + (res.message || 'Cập nhật thất bại.'));
                            }
                        })
                        .catch(() => toastr.error('Lỗi server. Vui lòng thử lại.'))
                        .finally(() => {
                            button.disabled = false;
                            button.innerHTML = originalHtml;
                        });
                });
            });
        </script>
    @endpush
</x-admin.layout>
