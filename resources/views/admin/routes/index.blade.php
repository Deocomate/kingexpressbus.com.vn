<x-admin.layout title="Quản lý Tuyến đường">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Quản lý Tuyến đường</li>
    </x-slot:breadcrumb>

    @push('styles')
        <style>
            .route-list {
                list-style: none;
                padding: 0;
            }

            .route-card {
                background-color: #fff;
                border: 1px solid #dee2e6;
                border-left: 3px solid #17a2b8;
                border-radius: .3rem;
                margin-bottom: 10px;
                padding: 12px 15px;
                cursor: grab;
                box-shadow: 0 1px 3px rgba(0, 0, 0, .05);
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .route-card:active {
                cursor: grabbing;
            }

            .route-name {
                font-weight: 600;
            }

            .route-details {
                font-size: 0.85em;
                color: #6c757d;
            }

            .sortable-ghost {
                background: #e9ecef;
                border: 1px dashed #adb5bd;
            }

            .card-header-action {
                margin-left: auto;
            }

            .select2-container--bootstrap4 .select2-selection--single {
                height: calc(2.25rem + 2px) !important;
            }
        </style>
    @endpush

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Danh sách Tuyến đường</h3>
            <div class="card-tools">
                <button class="btn btn-success btn-sm" id="btn-add">
                    <i class="fas fa-plus"></i> Thêm Tuyến đường
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="accordion" id="accordionProvinces">
                @forelse ($startProvinces as $province)
                    <div class="card">
                        <div class="card-header" id="heading-{{ $province->id }}">
                            <h2 class="mb-0 d-flex">
                                <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse"
                                        data-target="#collapse-{{ $province->id }}">
                                    <i class="fas fa-map-marker-alt text-info mr-2"></i> Xuất phát từ
                                    <strong>{{ $province->name }}</strong>
                                </button>
                            </h2>
                        </div>
                        <div id="collapse-{{ $province->id }}" class="collapse {{ $loop->first ? 'show' : '' }}"
                             data-parent="#accordionProvinces">
                            <div class="card-body">
                                <ul class="route-list" data-province-id="{{ $province->id }}">
                                    @if (isset($routes[$province->id]))
                                        @foreach ($routes[$province->id] as $route)
                                            <li class="route-card" data-route-id="{{ $route->id }}">
                                                <div>
                                                    <span class="route-name">{{ $route->name }}</span><br>
                                                    <small class="route-details">
                                                        <i class="fas fa-arrow-right"></i> Đích
                                                        đến: {{ $route->end_province_name }} |
                                                        <i class="far fa-clock"></i> {{ $route->duration ?? 'N/A' }} |
                                                        <i class="fas fa-road"></i> {{ $route->distance_km ? $route->distance_km.' km' : 'N/A' }}
                                                    </small>
                                                </div>
                                                <div class="action-buttons">
                                                    <button class="btn btn-info btn-sm btn-edit"
                                                            data-id="{{ $route->id }}"><i class="fas fa-pencil-alt"></i>
                                                        Sửa
                                                    </button>
                                                    <button class="btn btn-danger btn-sm btn-delete"
                                                            data-id="{{ $route->id }}"><i class="fas fa-trash"></i> Xóa
                                                    </button>
                                                </div>
                                            </li>
                                        @endforeach
                                    @else
                                        <p class="text-muted">Chưa có tuyến đường nào.</p>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center">Chưa có dữ liệu tuyến đường.</p>
                @endforelse
            </div>
        </div>
    </div>
    <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <form id="modalForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formModalLabel">Thêm Tuyến đường</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="route_id" name="id">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group"><label>Tên tuyến đường <span
                                            class="text-danger">*</span></label><input type="text" class="form-control"
                                                                                       name="name" id="input-name"
                                                                                       required
                                                                                       placeholder="Ví dụ: Hà Nội - Sapa">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group"><label>Độ ưu tiên</label><input type="number"
                                                                                        class="form-control"
                                                                                        name="priority"
                                                                                        id="input-priority" value="0"
                                                                                        required></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group"><label>Tỉnh/Thành đi <span
                                            class="text-danger">*</span></label><select name="province_start_id"
                                                                                        id="input-province_start_id"
                                                                                        class="form-control select2"
                                                                                        style="width: 100%;"
                                                                                        required></select></div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group"><label>Tỉnh/Thành đến <span
                                            class="text-danger">*</span></label><select
                                        name="province_end_id" id="input-province_end_id" class="form-control select2"
                                        style="width: 100%;" required></select></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group"><label>Thời gian di chuyển</label><input type="text"
                                                                                                 class="form-control"
                                                                                                 name="duration"
                                                                                                 id="input-duration"
                                                                                                 placeholder="Ví dụ: 4-5 tiếng">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group"><label>Khoảng cách (km)</label><input type="number"
                                                                                              class="form-control"
                                                                                              name="distance_km"
                                                                                              id="input-distance_km"
                                                                                              placeholder="Ví dụ: 300">
                                </div>
                            </div>
                        </div>
                        <div class="form-group"><label>Tiêu đề (SEO)</label><input type="text" class="form-control"
                                                                                   name="title" id="input-title"></div>
                        <div class="form-group"><label>Mô tả (SEO)</label><textarea name="description"
                                                                                    id="input-description"
                                                                                    class="form-control"
                                                                                    rows="2"></textarea></div>
                        <div class="form-group"><label>Ảnh đại diện</label>
                            <div class="input-group">
                                <input readonly type="text" class="form-control" name="thumbnail_url"
                                       id="input-thumbnail_url">
                                <span class="input-group-append"><button type="button"
                                                                         class="btn btn-secondary ckfinder-button">Duyệt Ảnh</button></span>
                            </div>
                            <div style="margin-top: 10px;"><img src="" alt="Preview" class="ckfinder-preview-image"
                                                                onerror="this.onerror=null;this.src='/shared/dist/img/placeholder.png';"
                                                                style="max-width: 200px; display: none;"></div>
                        </div>
                        <div class="form-group"><label>Nội dung chi tiết</label><textarea name="content"
                                                                                          id="input-content"
                                                                                          class="form-control"
                                                                                          rows="5"></textarea></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary" id="btn-save">Lưu lại</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                const provincesData = @json($all_provinces_for_modal);
                let contentEditor;
                // Initialize Select2
                $('#input-province_start_id, #input-province_end_id').select2({
                    theme: 'bootstrap4', dropdownParent: $('#formModal'),
                    data: [{id: '', text: '-- Chọn Tỉnh/Thành --'}, ...provincesData.map(p => ({
                        id: p.id,
                        text: p.name
                    }))]
                });
                // Initialize SortableJS
                document.querySelectorAll('.route-list').forEach(list => {
                    new Sortable(list, {
                        animation: 150, ghostClass: 'sortable-ghost',
                        onEnd: function (evt) {
                            const order = Array.from(evt.target.children).map(item => item.dataset.routeId);
                            $.post('{{ route("admin.routes.updateOrder") }}', {order}, res => toastr.success(res.message))
                                .fail(() => toastr.error('Lỗi server, không thể cập nhật.'));
                        }
                    });
                });
                // --- Modal & Form Logic ---
                initSingleCKFinder('#input-thumbnail_url');
                initCkEditor('#input-content').then(editor => contentEditor = editor).catch(e => console.error(e));

                function resetForm() {
                    $('#modalForm')[0].reset();
                    $('#route_id').val('');
                    $('#input-province_start_id, #input-province_end_id').val(null).trigger('change');
                    if (contentEditor) contentEditor.setData('');
                    $('.ckfinder-preview-image').attr('src', '').hide();
                    $('#modalForm').find('.is-invalid, .is-invalid ~ .select2-container .select2-selection').removeClass('is-invalid');
                    $('#modalForm').find('.invalid-feedback').remove();
                }

                $('#btn-add').on('click', function () {
                    resetForm();
                    $('#formModalLabel').text('Thêm Tuyến đường');
                    $('#formModal').modal('show');
                });
                $('body').on('click', '.btn-edit', function () {
                    const routeId = $(this).data('id');
                    $.get(`/admin/routes/${routeId}`, function (response) {
                        if (response.success) {
                            resetForm();
                            const data = response.data;
                            $('#formModalLabel').text('Cập nhật: ' + data.name);
                            $('#route_id').val(data.id);
                            $('#input-name').val(data.name);
                            $('#input-priority').val(data.priority);
                            $('#input-province_start_id').val(data.province_start_id).trigger('change');
                            $('#input-province_end_id').val(data.province_end_id).trigger('change');
                            $('#input-duration').val(data.duration);
                            $('#input-distance_km').val(data.distance_km);
                            $('#input-title').val(data.title);
                            $('#input-description').val(data.description);
                            $('#input-thumbnail_url').val(data.thumbnail_url);
                            if (data.thumbnail_url) $('.ckfinder-preview-image').attr('src', data.thumbnail_url).show();
                            if (contentEditor) contentEditor.setData(data.content || '');
                            $('#formModal').modal('show');
                        }
                    });
                });
                $('#modalForm').on('submit', function (e) {
                    e.preventDefault();
                    const routeId = $('#route_id').val();
                    let url = routeId ? `/admin/routes/${routeId}` : '{{ route("admin.routes.store") }}';
                    let formData = new FormData(this);
                    if (routeId) formData.append('_method', 'PUT');
                    if (contentEditor) formData.set('content', contentEditor.getData());
                    $.ajax({
                        url: url, type: 'POST', data: formData, processData: false, contentType: false,
                        success: res => {
                            if (res.success) {
                                $('#formModal').modal('hide');
                                toastr.success(res.message);
                                setTimeout(() => location.reload(), 1500);
                            }
                        },
                        error: function (xhr) {
                            if (xhr.status === 422) {
                                $('#modalForm').find('.is-invalid, .is-invalid ~ .select2-container .select2-selection').removeClass('is-invalid');
                                $('#modalForm').find('.invalid-feedback').remove();
                                $.each(xhr.responseJSON.errors, function (key, value) {
                                    const field = $('#modalForm').find(`[name="${key}"]`);
                                    if (field.hasClass('select2')) {
                                        field.next('.select2-container').find('.select2-selection').addClass('is-invalid');
                                        field.parent().append(`<div class="invalid-feedback d-block">${value[0]}</div>`);
                                    } else {
                                        field.addClass('is-invalid').after(`<div class="invalid-feedback">${value[0]}</div>`);
                                    }
                                });
                                toastr.error(Object.values(xhr.responseJSON.errors)[0][0]);
                            } else {
                                toastr.error('Đã xảy ra lỗi server.');
                            }
                        }
                    });
                });
                $('body').on('click', '.btn-delete', function () {
                    const routeId = $(this).data('id');
                    Swal.fire({
                        title: 'Bạn có chắc chắn?', text: "Hành động này không thể hoàn tác!", icon: 'warning',
                        showCancelButton: true, confirmButtonText: 'Vâng, xóa nó!', cancelButtonText: 'Hủy'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `/admin/routes/${routeId}`, type: 'DELETE',
                                success: (res) => {
                                    if (res.success) {
                                        $(`.route-card[data-route-id="${routeId}"]`).fadeOut(500, function () {
                                            $(this).remove();
                                        });
                                        Swal.fire('Đã xóa!', res.message, 'success');
                                    } else {
                                        Swal.fire('Lỗi!', res.message, 'error');
                                    }
                                },
                                error: () => Swal.fire('Lỗi!', 'Lỗi server.', 'error')
                            });
                        }
                    })
                });
            });
        </script>
    @endpush
</x-admin.layout>
