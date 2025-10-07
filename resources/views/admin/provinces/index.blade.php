<x-admin.layout title="Quản lý Tỉnh/Thành phố">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Quản lý Tỉnh/Thành phố</li>
    </x-slot:breadcrumb>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Danh sách Tỉnh/Thành phố</h3>
            <div class="card-tools">
                <button class="btn btn-success btn-sm" id="btn-add-province">
                    <i class="fas fa-plus"></i> Thêm mới
                </button>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.provinces.index') }}" class="mb-4">
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group mb-0">
                            <label for="search" class="sr-only">Tìm kiếm</label>
                            <input type="text" class="form-control" id="search" name="search"
                                   value="{{ request('search') }}" placeholder="Tên tỉnh/thành phố...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Lọc
                        </button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped projects">
                    <thead>
                    <tr>
                        <th style="width: 1%">#</th>
                        <th style="width: 10%">Ảnh đại diện</th>
                        <th>Tên Tỉnh/Thành</th>
                        <th>Tiêu đề SEO</th>
                        <th class="text-center" style="width: 8%">Ưu tiên</th>
                        <th style="width: 20%" class="text-right">Hành động</th>
                    </tr>
                    </thead>
                    <tbody id="province-table-body">
                    @forelse ($provinces as $province)
                        <tr id="province-row-{{ $province->id }}">
                            <td>{{ $loop->iteration + ($provinces->currentPage() - 1) * $provinces->perPage() }}</td>
                            <td>
                                <img src="{{ $province->thumbnail_url ?: '/shared/dist/img/placeholder.png' }}"
                                     onerror="this.onerror=null;this.src='/shared/dist/img/placeholder.png';"
                                     alt="{{$province->name}}" class="img-thumbnail" width="80">
                            </td>
                            <td><strong>{{ $province->name }}</strong><br><small>/{{$province->slug}}</small></td>
                            <td>{{ Str::limit($province->title, 50) }}</td>
                            <td class="text-center">{{ $province->priority }}</td>
                            <td class="project-actions text-right">
                                <button class="btn btn-info btn-sm btn-edit" data-id="{{ $province->id }}">
                                    <i class="fas fa-pencil-alt"></i> Sửa
                                </button>
                                <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $province->id }}">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Không có dữ liệu.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($provinces->hasPages())
            <div class="card-footer clearfix">
                {{ $provinces->links("pagination::bootstrap-4") }}
            </div>
        @endif
    </div>

    <div class="modal fade" id="provinceModal" tabindex="-1" role="dialog" aria-labelledby="provinceModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="provinceForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="provinceModalLabel">Thêm Tỉnh/Thành phố</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="province_id" name="id">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="input-name">Tên Tỉnh/Thành phố <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" id="input-name" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="input-priority">Độ ưu tiên <span class="text-danger">*</span></label>
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
                const modal = $('#provinceModal');
                const form = $('#provinceForm');
                const modalLabel = $('#provinceModalLabel');
                const btnSave = $('#btn-save');
                let contentEditor;
                let triggerButton = null;

                initSingleCKFinder('#input-thumbnail_url');
                initCkEditor('#input-content').then(editor => contentEditor = editor).catch(e => console.error(e));

                modal.on('hidden.bs.modal', function () {
                    if (triggerButton) $(triggerButton).focus();
                    triggerButton = null;
                });

                function resetForm() {
                    form[0].reset();
                    $('#province_id').val('');
                    if (contentEditor) contentEditor.setData('');
                    $('.ckfinder-preview-image').attr('src', '').hide();
                    form.find('.is-invalid').removeClass('is-invalid');
                    form.find('.invalid-feedback').remove();
                }

                function showValidationErrors(errors) {
                    form.find('.is-invalid').removeClass('is-invalid');
                    form.find('.invalid-feedback').remove();
                    $.each(errors, function (key, value) {
                        const field = form.find(`[name="${key}"]`);
                        field.addClass('is-invalid').after(`<div class="invalid-feedback">${value[0]}</div>`);
                    });
                }

                function showLoading(element) {
                    element.attr('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang xử lý...');
                }

                function hideLoading(element, defaultText) {
                    element.attr('disabled', false).html(defaultText);
                }

                $('#btn-add-province').on('click', function () {
                    triggerButton = this;
                    resetForm();
                    modalLabel.text('Thêm Tỉnh/Thành phố');
                    modal.modal('show');
                });

                $('body').on('click', '.btn-edit', function () {
                    triggerButton = this;
                    const provinceId = $(this).data('id');
                    $.get(`/admin/provinces/${provinceId}`, function (response) {
                        if (response.success) {
                            resetForm();
                            const data = response.data;
                            modalLabel.text('Cập nhật: ' + data.name);
                            $('#province_id').val(data.id);
                            $('#input-name').val(data.name);
                            $('#input-priority').val(data.priority);
                            $('#input-title').val(data.title);
                            $('#input-description').val(data.description);
                            $('#input-thumbnail_url').val(data.thumbnail_url);

                            const previewImage = $('.ckfinder-preview-image');
                            if (data.thumbnail_url) {
                                previewImage.attr('src', data.thumbnail_url).show();
                            } else {
                                previewImage.hide();
                            }

                            if (contentEditor) {
                                contentEditor.setData(data.content || '');
                            }
                            modal.modal('show');
                        }
                    });
                });

                form.on('submit', function (e) {
                    e.preventDefault();
                    showLoading(btnSave);
                    const provinceId = $('#province_id').val();
                    const url = provinceId ? `/admin/provinces/${provinceId}` : '{{ route("admin.provinces.store") }}';
                    const method = 'POST';

                    let formData = new FormData(this);
                    if (provinceId) formData.append('_method', 'PUT');
                    if (contentEditor) formData.set('content', contentEditor.getData());

                    $.ajax({
                        url: url,
                        method: method,
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            if (response.success) {
                                modal.modal('hide');
                                toastr.success(response.message);
                                setTimeout(() => location.reload(), 1000);
                            } else {
                                toastr.error(response.message || 'Đã xảy ra lỗi.');
                            }
                        },
                        error: function (xhr) {
                            if (xhr.status === 422) {
                                showValidationErrors(xhr.responseJSON.errors);
                            } else {
                                toastr.error('Đã xảy ra lỗi server. Vui lòng thử lại.');
                            }
                        },
                        complete: function () {
                            hideLoading(btnSave, 'Lưu lại');
                        }
                    });
                });

                $('body').on('click', '.btn-delete', function () {
                    const provinceId = $(this).data('id');
                    Swal.fire({
                        title: 'Bạn có chắc chắn?',
                        text: "Hành động này không thể hoàn tác!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Vâng, xóa nó!',
                        cancelButtonText: 'Hủy'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `/admin/provinces/${provinceId}`,
                                type: 'DELETE',
                                success: function (response) {
                                    if (response.success) {
                                        $(`#province-row-${provinceId}`).fadeOut(500, function () {
                                            $(this).remove();
                                        });
                                        Swal.fire('Đã xóa!', response.message, 'success');
                                    } else {
                                        Swal.fire('Lỗi!', response.message || 'Không thể xóa.', 'error');
                                    }
                                },
                                error: function () {
                                    Swal.fire('Lỗi!', 'Đã xảy ra lỗi server.', 'error');
                                }
                            });
                        }
                    })
                });
            });
        </script>
    @endpush
</x-admin.layout>
