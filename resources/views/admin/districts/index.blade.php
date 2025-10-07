<x-admin.layout title="Quản lý Quận/Huyện">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Quản lý Quận/Huyện</li>
    </x-slot:breadcrumb>

    @push('styles')
        <style>
            .province-column {
                background-color: #f4f6f9;
                border-radius: 5px;
                padding: 15px;
                margin-bottom: 20px;
                min-height: 400px;
                border: 1px solid #e3e6f0;
            }

            .province-header {
                font-size: 1.1rem;
                font-weight: 600;
                color: #495057;
                margin-bottom: 15px;
                padding-bottom: 10px;
                border-bottom: 2px solid #007bff;
            }

            .district-list {
                min-height: 300px;
                list-style: none;
                padding: 0;
            }

            .district-card {
                background-color: #fff;
                border: 1px solid #dee2e6;
                border-radius: .3rem;
                margin-bottom: 10px;
                padding: 10px 15px;
                cursor: grab;
                box-shadow: 0 1px 3px rgba(0, 0, 0, .05);
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .district-card:active {
                cursor: grabbing;
            }

            .district-name {
                font-weight: 500;
            }

            .district-type {
                font-size: 0.8em;
                color: #6c757d;
            }

            .sortable-ghost {
                background: #e9ecef;
                border: 1px dashed #adb5bd;
            }

            .action-buttons .btn {
                margin-left: 5px;
            }

            /* Style for Select2 in modal */
            .select2-container--bootstrap4 .select2-selection--single {
                height: calc(2.25rem + 2px) !important;
            }
        </style>
    @endpush

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Quản lý Quận/Huyện</h3>
            <div class="card-tools">
                <button class="btn btn-success btn-sm" id="btn-add-district">
                    <i class="fas fa-plus"></i> Thêm Quận/Huyện
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                @forelse ($provinces as $province)
                    <div class="col-md-4">
                        <div class="province-column">
                            <h5 class="province-header">{{ $province->name }}</h5>
                            <ul class="district-list" data-province-id="{{ $province->id }}">
                                @if (isset($districtsByProvince[$province->id]))
                                    @foreach ($districtsByProvince[$province->id] as $district)
                                        <li class="district-card" data-district-id="{{ $district->id }}">
                                            <div>
                                                <span class="district-name">{{ $district->name }}</span><br>
                                                <small class="district-type">{{ $district->district_type_name }}</small>
                                            </div>
                                            <div class="action-buttons">
                                                <button class="btn btn-info btn-xs btn-edit"
                                                        data-id="{{ $district->id }}"><i class="fas fa-pencil-alt"></i>
                                                </button>
                                                <button class="btn btn-danger btn-xs btn-delete"
                                                        data-id="{{ $district->id }}"><i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-center">Chưa có dữ liệu Tỉnh/Thành phố. Vui lòng thêm Tỉnh/Thành phố trước.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="modal fade" id="districtModal" tabindex="-1" role="dialog" aria-labelledby="districtModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="districtForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="districtModalLabel">Thêm Quận/Huyện</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="district_id" name="id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="input-name">Tên Quận/Huyện <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" id="input-name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="input-province_id">Thuộc Tỉnh/Thành phố <span
                                            class="text-danger">*</span></label>
                                    <select name="province_id" id="input-province_id" class="form-control select2"
                                            style="width: 100%;" required>
                                        <option value="">-- Chọn Tỉnh/Thành --</option>
                                        @foreach($provinces as $province)
                                            <option value="{{ $province->id }}">{{ $province->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="input-district_type_id">Loại địa điểm <span
                                            class="text-danger">*</span></label>
                                    <select name="district_type_id" id="input-district_type_id"
                                            class="form-control select2" style="width: 100%;" required>
                                        <option value="">-- Chọn loại địa điểm --</option>
                                        @foreach($district_types as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="input-priority">Độ ưu tiên</label>
                                    <input type="number" class="form-control" name="priority" id="input-priority"
                                           value="0" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="input-title">Tiêu đề (SEO)</label>
                            <input type="text" class="form-control" name="title" id="input-title">
                        </div>
                        <div class="form-group">
                            <label for="input-description">Mô tả (SEO)</label>
                            <textarea name="description" id="input-description" class="form-control"
                                      rows="2"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="input-thumbnail_url">Ảnh đại diện</label>
                            <div class="input-group">
                                <input readonly type="text" class="form-control" name="thumbnail_url"
                                       id="input-thumbnail_url">
                                <span class="input-group-append">
                                    <button type="button" class="btn btn-secondary ckfinder-button">Duyệt Ảnh</button>
                                </span>
                            </div>
                            <div style="margin-top: 10px;">
                                <img src="" alt="Image Preview" class="ckfinder-preview-image"
                                     onerror="this.onerror=null;this.src='/shared/dist/img/placeholder.png';"
                                     style="max-width: 200px; max-height: 200px; display: none;">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="input-content">Nội dung chi tiết</label>
                            <textarea name="content" id="input-content" class="form-control" rows="5"></textarea>
                        </div>
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
                // Initialize Select2 Elements
                $('.select2').select2({
                    theme: 'bootstrap4',
                    dropdownParent: $('#districtModal')
                });

                const modal = $('#districtModal');
                const form = $('#districtForm');
                let contentEditor;

                // Initialize SortableJS for all district lists
                document.querySelectorAll('.district-list').forEach(function (list) {
                    new Sortable(list, {
                        group: 'districts',
                        animation: 150,
                        ghostClass: 'sortable-ghost',
                        onEnd: function (evt) {
                            const provinceId = evt.to.dataset.provinceId;
                            const order = Array.from(evt.to.children).map(item => item.dataset.districtId);

                            $.ajax({
                                url: '{{ route("admin.districts.updateOrder") }}',
                                type: 'POST',
                                data: {provinceId, order},
                                success: function (response) {
                                    if (response.success) {
                                        toastr.success(response.message);
                                    } else {
                                        toastr.error(response.message || 'Cập nhật thất bại.');
                                    }
                                },
                                error: function () {
                                    toastr.error('Lỗi server, không thể cập nhật.');
                                }
                            });
                        }
                    });
                });

                // --- Modal & Form Logic ---
                initSingleCKFinder('#input-thumbnail_url');
                initCkEditor('#input-content').then(editor => contentEditor = editor).catch(e => console.error(e));

                function resetForm() {
                    form[0].reset();
                    $('#district_id').val('');
                    // Reset Select2 fields
                    $('.select2').val(null).trigger('change');
                    if (contentEditor) contentEditor.setData('');
                    $('.ckfinder-preview-image').attr('src', '').hide();
                    form.find('.is-invalid').removeClass('is-invalid');
                    form.find('.invalid-feedback').remove();
                }

                $('#btn-add-district').on('click', function () {
                    resetForm();
                    $('#districtModalLabel').text('Thêm Quận/Huyện');
                    modal.modal('show');
                });

                $('body').on('click', '.btn-edit', function () {
                    const districtId = $(this).data('id');
                    $.get(`/admin/districts/${districtId}`, function (response) {
                        if (response.success) {
                            resetForm();
                            const data = response.data;
                            $('#districtModalLabel').text('Cập nhật: ' + data.name);
                            $('#district_id').val(data.id);
                            $('#input-name').val(data.name);
                            // Set value and trigger change for Select2
                            $('#input-province_id').val(data.province_id).trigger('change');
                            $('#input-district_type_id').val(data.district_type_id).trigger('change');
                            $('#input-priority').val(data.priority);
                            $('#input-title').val(data.title);
                            $('#input-description').val(data.description);
                            $('#input-thumbnail_url').val(data.thumbnail_url);

                            if (data.thumbnail_url) $('.ckfinder-preview-image').attr('src', data.thumbnail_url).show();
                            if (contentEditor) contentEditor.setData(data.content || '');

                            modal.modal('show');
                        }
                    });
                });

                form.on('submit', function (e) {
                    e.preventDefault();
                    const districtId = $('#district_id').val();
                    let url = districtId ? `/admin/districts/${districtId}` : '{{ route("admin.districts.store") }}';
                    let method = 'POST';

                    let formData = new FormData(this);
                    if (districtId) formData.append('_method', 'PUT');
                    if (contentEditor) formData.set('content', contentEditor.getData());

                    $.ajax({
                        url: url,
                        type: method,
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            if (response.success) {
                                modal.modal('hide');
                                toastr.success(response.message);
                                setTimeout(() => location.reload(), 1500);
                            }
                        },
                        error: function (xhr) {
                            if (xhr.status === 422) {
                                form.find('.is-invalid').removeClass('is-invalid');
                                form.find('.invalid-feedback').remove();
                                let firstError = true;
                                $.each(xhr.responseJSON.errors, function (key, value) {
                                    const field = form.find(`[name="${key}"]`);
                                    // For select2, find the next sibling container to apply error class
                                    if (field.hasClass('select2')) {
                                        field.next('.select2-container').find('.select2-selection').addClass('is-invalid');
                                        field.parent().append(`<div class="invalid-feedback d-block">${value[0]}</div>`);
                                    } else {
                                        field.addClass('is-invalid').after(`<div class="invalid-feedback">${value[0]}</div>`);
                                    }
                                    if (firstError) {
                                        toastr.error(value[0]);
                                        firstError = false;
                                    }
                                });
                            } else {
                                toastr.error('Đã xảy ra lỗi server. Vui lòng thử lại.');
                            }
                        }
                    });
                });

                $('body').on('click', '.btn-delete', function () {
                    const districtId = $(this).data('id');
                    Swal.fire({
                        title: 'Bạn có chắc chắn?',
                        text: "Hành động này không thể hoàn tác!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Vâng, xóa nó!',
                        cancelButtonText: 'Hủy'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `/admin/districts/${districtId}`,
                                type: 'DELETE',
                                success: function (response) {
                                    if (response.success) {
                                        $(`.district-card[data-district-id="${districtId}"]`).fadeOut(500, function () {
                                            $(this).remove();
                                        });
                                        Swal.fire('Đã xóa!', response.message, 'success');
                                    } else {
                                        Swal.fire('Lỗi!', response.message, 'error');
                                    }
                                },
                                error: function () {
                                    Swal.fire('Lỗi!', 'Đã xảy ra lỗi server.', 'error');
                                }
                            });
                        }
                    });
                });
            });
        </script>
    @endpush
</x-admin.layout>
