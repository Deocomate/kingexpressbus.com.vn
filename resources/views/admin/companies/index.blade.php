<x-admin.layout title="Quản lý Nhà xe">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Quản lý Nhà xe</li>
    </x-slot:breadcrumb>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Danh sách Nhà xe</h3>
            <div class="card-tools">
                <button class="btn btn-success btn-sm" id="btn-add-company">
                    <i class="fas fa-plus"></i> Thêm Nhà xe mới
                </button>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.companies.index') }}" class="mb-4">
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group mb-0">
                            <label for="search" class="sr-only">Tìm kiếm</label>
                            <input type="text" class="form-control" id="search" name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Tên nhà xe, email, SĐT, email tài khoản...">
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
                        <th style="width: 10%">Logo</th>
                        <th>Tên Nhà xe</th>
                        <th>Liên hệ</th>
                        <th>Địa chỉ</th>
                        <th class="text-center" style="width: 8%">Ưu tiên</th>
                        <th style="width: 20%" class="text-right">Hành động</th>
                    </tr>
                    </thead>
                    <tbody id="company-table-body">
                    @forelse ($companies as $company)
                        <tr id="company-row-{{ $company->id }}">
                            <td>{{ $loop->iteration + ($companies->currentPage() - 1) * $companies->perPage() }}</td>
                            <td><img src="{{ $company->thumbnail_url ?: '/shared/dist/img/default-150x150.png' }}"
                                     alt="{{$company->name}}" class="img-thumbnail" width="80"></td>
                            <td><strong>{{ $company->name }}</strong></td>
                            <td>
                                @if($company->phone)
                                    <i class="fas fa-phone-alt"></i> {{ $company->phone }} <br/>
                                @endif
                                @if($company->email)
                                    <small><i class="fas fa-envelope"></i> {{ $company->email }}</small> <br/>
                                @endif
                                @if($company->user_email)
                                    <small><i class="fas fa-user-lock"></i> <b>Tài khoản:</b> {{ $company->user_email }}
                                    </small>
                                @endif
                            </td>
                            <td>{{ Str::limit($company->address, 50) }}</td>
                            <td class="text-center">{{ $company->priority }}</td>
                            <td class="project-actions text-right">
                                <button class="btn btn-info btn-sm btn-edit" data-id="{{ $company->id }}">
                                    <i class="fas fa-pencil-alt"></i> Sửa
                                </button>
                                <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $company->id }}">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Không có dữ liệu nhà xe nào.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer clearfix">
            {{ $companies->links("pagination::bootstrap-4") }}
        </div>
    </div>

    <div class="modal fade" id="companyModal" tabindex="-1" role="dialog" aria-labelledby="companyModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="companyForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="companyModalLabel">Thêm Nhà xe mới</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="company_id" name="id">
                        <h5>Thông tin nhà xe</h5>
                        <hr class="mt-2">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="input-name">Tên nhà xe <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" id="input-name" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="input-priority">Độ ưu tiên</label>
                                    <input type="number" class="form-control" name="priority" id="input-priority"
                                           value="0" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="input-phone">Số điện thoại liên hệ</label>
                                    <input type="text" class="form-control" name="phone" id="input-phone">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="input-email">Email liên hệ</label>
                                    <input type="email" class="form-control" name="email" id="input-email">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="input-address">Địa chỉ</label>
                            <input type="text" class="form-control" name="address" id="input-address">
                        </div>
                        <div class="form-group">
                            <label for="input-thumbnail_url">Ảnh đại diện (Logo)</label>
                            <div class="input-group">
                                <input readonly type="text" class="form-control" name="thumbnail_url"
                                       id="input-thumbnail_url">
                                <span class="input-group-append">
                                    <button type="button" class="btn btn-secondary ckfinder-button">
                                        Duyệt Ảnh
                                    </button>
                                </span>
                            </div>
                            <div style="margin-top: 10px;">
                                <img src="" alt="Image Preview" class="ckfinder-preview-image"
                                     style="max-width: 200px; max-height: 200px; display: none;">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="input-content">Nội dung giới thiệu</label>
                            <textarea name="content" id="input-content" class="form-control" rows="5"></textarea>
                        </div>
                        <h5 class="mt-4">Thông tin tài khoản đăng nhập</h5>
                        <hr class="mt-2">
                        <div class="form-group">
                            <label for="input-user_email">Email đăng nhập <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="user_email" id="input-user_email" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="input-password">Mật khẩu <span class="text-danger"
                                                                               id="password-required-star">*</span></label>
                                    <input type="password" class="form-control" name="password" id="input-password">
                                    <small class="form-text text-muted">Để trống nếu không muốn thay đổi mật khẩu khi
                                        cập nhật.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="input-password_confirmation">Xác nhận mật khẩu <span class="text-danger"
                                                                                                     id="password-confirm-required-star">*</span></label>
                                    <input type="password" class="form-control" name="password_confirmation"
                                           id="input-password_confirmation">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary" id="btn-save">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            $(document).ready(function () {
                const modal = $('#companyModal');
                const form = $('#companyForm');
                const modalLabel = $('#companyModalLabel');
                const btnSave = $('#btn-save');
                let contentEditor;
                let triggerButton = null;

                initSingleCKFinder('#input-thumbnail_url');
                if (document.querySelector('#input-content')) {
                    initCkEditor('#input-content')
                        .then(newEditor => {
                            contentEditor = newEditor;
                        })
                        .catch(error => {
                            console.error('Error initializing CKEditor:', error);
                        });
                }

                modal.on('hidden.bs.modal', function () {
                    if (triggerButton) {
                        $(triggerButton).focus();
                        triggerButton = null;
                    }
                });

                function resetForm() {
                    form[0].reset();
                    $('#company_id').val('');
                    if (contentEditor) {
                        contentEditor.setData('');
                    }
                    $('.ckfinder-preview-image').attr('src', '').hide();
                    form.find('.is-invalid').removeClass('is-invalid');
                    form.find('.invalid-feedback').remove();
                }

                function showValidationErrors(errors) {
                    form.find('.is-invalid').removeClass('is-invalid');
                    form.find('.invalid-feedback').remove();
                    $.each(errors, function (key, value) {
                        const fieldName = key.includes('password_confirmation') ? 'password_confirmation' : key;
                        const field = form.find(`[name="${fieldName}"]`);
                        field.addClass('is-invalid');
                        field.closest('.form-group').append(`<div class="invalid-feedback d-block">${value[0]}</div>`);
                    });
                }

                function showLoading(element) {
                    element.attr('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang xử lý...');
                }

                function hideLoading(element, defaultText) {
                    element.attr('disabled', false).html(defaultText);
                }

                $('#btn-add-company').on('click', function () {
                    triggerButton = this;
                    resetForm();
                    modalLabel.text('Thêm Nhà xe mới');
                    $('#input-password').attr('required', true);
                    $('#input-password_confirmation').attr('required', true);
                    $('#password-required-star, #password-confirm-required-star').show();
                    modal.modal('show');
                });

                $('body').on('click', '.btn-edit', function () {
                    triggerButton = this;
                    const companyId = $(this).data('id');
                    $.get(`/admin/companies/${companyId}`, function (response) {
                        if (response.success) {
                            resetForm();
                            const data = response.data;
                            modalLabel.text('Cập nhật Nhà xe: ' + data.name);
                            $('#company_id').val(data.id);
                            $('#input-name').val(data.name);
                            $('#input-priority').val(data.priority);
                            $('#input-phone').val(data.phone);
                            $('#input-email').val(data.email);
                            $('#input-address').val(data.address);
                            $('#input-thumbnail_url').val(data.thumbnail_url);
                            $('#input-user_email').val(data.user_email);
                            $('#input-password').val('');
                            $('#input-password_confirmation').val('');
                            $('#input-password').attr('required', false);
                            $('#input-password_confirmation').attr('required', false);
                            $('#password-required-star, #password-confirm-required-star').hide();
                            const previewImage = $('#input-thumbnail_url').closest('.form-group').find('.ckfinder-preview-image');
                            if (data.thumbnail_url) {
                                previewImage.attr('src', data.thumbnail_url).show();
                            } else {
                                previewImage.attr('src', '').hide();
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
                    const companyId = $('#company_id').val();
                    let url = companyId ? `/admin/companies/${companyId}` : `{{ route('admin.companies.store') }}`;
                    let method = 'POST';
                    let formData = new FormData(this);
                    if (companyId) {
                        formData.append('_method', 'PUT');
                    }
                    if (contentEditor) {
                        formData.set('content', contentEditor.getData());
                    }
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
                            hideLoading(btnSave, 'Lưu thay đổi');
                        }
                    });
                });

                $('body').on('click', '.btn-delete', function () {
                    const companyId = $(this).data('id');
                    Swal.fire({
                        title: 'Bạn có chắc chắn?',
                        text: "Bạn sẽ không thể hoàn tác hành động này! Tài khoản của nhà xe cũng sẽ bị xóa.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Vâng, xóa nó!',
                        cancelButtonText: 'Hủy'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `/admin/companies/${companyId}`,
                                type: 'DELETE',
                                success: function (response) {
                                    if (response.success) {
                                        $(`#company-row-${companyId}`).fadeOut(500, function () {
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
