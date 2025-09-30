@extends('layouts.shared.main')
@section('title', 'Quản lý Loại địa điểm')
@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Danh sách Loại địa điểm</h3>
            <button class="btn btn-success btn-sm" id="btn-add">
                <i class="fas fa-plus"></i> Thêm mới
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped projects">
                    <thead>
                    <tr>
                        <th style="width: 1%">#</th>
                        <th>Tên Loại địa điểm</th>
                        <th class="text-center" style="width: 15%">Độ ưu tiên</th>
                        <th style="width: 20%" class="text-right">Hành động</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($district_types as $type)
                        <tr id="row-{{ $type->id }}">
                            <td>{{ $loop->iteration + ($district_types->currentPage() - 1) * $district_types->perPage() }}</td>
                            <td><strong>{{ $type->name }}</strong></td>
                            <td class="text-center">{{ $type->priority }}</td>
                            <td class="project-actions text-right">
                                <button class="btn btn-info btn-sm btn-edit" data-id="{{ $type->id }}">
                                    <i class="fas fa-pencil-alt"></i> Sửa
                                </button>
                                <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $type->id }}">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Không có dữ liệu.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer clearfix">
            {{ $district_types->links("pagination::bootstrap-4") }}
        </div>
    </div>

    <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="modalForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formModalLabel">Thêm Loại địa điểm</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="type_id" name="id">
                        <div class="form-group">
                            <label for="input-name">Tên Loại địa điểm <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" id="input-name" required>
                        </div>
                        <div class="form-group">
                            <label for="input-priority">Độ ưu tiên <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="priority" id="input-priority" value="0"
                                   required>
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
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            const modal = $('#formModal');
            const form = $('#modalForm');
            const modalLabel = $('#formModalLabel');
            const btnSave = $('#btn-save');

            function resetForm() {
                form[0].reset();
                $('#type_id').val('');
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

            $('#btn-add').on('click', function () {
                resetForm();
                modalLabel.text('Thêm Loại địa điểm');
                modal.modal('show');
            });

            $('body').on('click', '.btn-edit', function () {
                const typeId = $(this).data('id');
                $.get(`/admin/district-types/${typeId}`, function (response) {
                    if (response.success) {
                        resetForm();
                        const data = response.data;
                        modalLabel.text('Cập nhật: ' + data.name);
                        $('#type_id').val(data.id);
                        $('#input-name').val(data.name);
                        $('#input-priority').val(data.priority);
                        modal.modal('show');
                    }
                });
            });

            form.on('submit', function (e) {
                e.preventDefault();
                const typeId = $('#type_id').val();
                let url = typeId ? `/admin/district-types/${typeId}` : '{{ route("admin.district-types.store") }}';
                let method = typeId ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    data: $(this).serialize(),
                    success: function (response) {
                        if (response.success) {
                            modal.modal('hide');
                            toastr.success(response.message);
                            setTimeout(() => location.reload(), 1000);
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            showValidationErrors(xhr.responseJSON.errors);
                        } else {
                            toastr.error('Đã xảy ra lỗi server.');
                        }
                    }
                });
            });

            $('body').on('click', '.btn-delete', function () {
                const typeId = $(this).data('id');
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
                            url: `/admin/district-types/${typeId}`,
                            type: 'DELETE',
                            success: function (response) {
                                if (response.success) {
                                    $(`#row-${typeId}`).fadeOut(500, function () {
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
                })
            });
        });
    </script>
@endpush
