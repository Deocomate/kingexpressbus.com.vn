<x-admin.layout title="Quản lý Điểm dừng">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Quản lý Điểm dừng</li>
    </x-slot:breadcrumb>

    @push('styles')
        <style>
            .district-column {
                background-color: #f4f6f9;
                border-radius: 5px;
                padding: 15px;
                margin-bottom: 20px;
                min-height: 300px;
                border: 1px solid #e3e6f0;
            }

            .district-header {
                font-size: 1rem;
                font-weight: 600;
                color: #495057;
                padding-bottom: 10px;
                margin-bottom: 15px;
                border-bottom: 1px solid #dee2e6;
            }

            .stop-list {
                min-height: 250px;
                list-style: none;
                padding: 0;
            }

            .stop-card {
                background-color: #fff;
                border: 1px solid #dee2e6;
                border-left: 3px solid #007bff;
                border-radius: .3rem;
                margin-bottom: 10px;
                padding: 10px 15px;
                cursor: grab;
                box-shadow: 0 1px 3px rgba(0, 0, 0, .05);
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .stop-card:active {
                cursor: grabbing;
            }

            .stop-name {
                font-weight: 500;
            }

            .stop-address {
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
        </style>
    @endpush

    <div class="card card-primary card-outline card-outline-tabs">
        <div class="card-header p-0 border-bottom-0">
            <div class="d-flex justify-content-between align-items-center p-2">
                <ul class="nav nav-tabs" id="province-tabs" role="tablist">
                    @foreach ($provinces as $province)
                        <li class="nav-item">
                            <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="tab-province-{{$province->id}}"
                               data-toggle="pill" href="#content-province-{{$province->id}}"
                               role="tab">{{ $province->name }}</a>
                        </li>
                    @endforeach
                </ul>
                <div class="card-tools">
                    <button class="btn btn-success btn-sm" id="btn-add">
                        <i class="fas fa-plus"></i> Thêm Điểm dừng
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="tab-content" id="province-tabs-content">
                @foreach ($provinces as $province)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                         id="content-province-{{$province->id}}" role="tabpanel">
                        <div class="row">
                            @if (isset($districts[$province->id]) && $districts[$province->id]->isNotEmpty())
                                @foreach ($districts[$province->id] as $district)
                                    <div class="col-md-4">
                                        <div class="district-column">
                                            <h6 class="district-header">{{ $district->name }}</h6>
                                            <ul class="stop-list" data-district-id="{{ $district->id }}">
                                                @if (isset($stops[$district->id]))
                                                    @foreach ($stops[$district->id] as $stop)
                                                        <li class="stop-card" data-stop-id="{{ $stop->id }}">
                                                            <div>
                                                                <span class="stop-name">{{ $stop->name }}</span><br>
                                                                <small
                                                                    class="stop-address">{{ Str::limit($stop->address, 40) }}</small>
                                                            </div>
                                                            <div class="action-buttons">
                                                                <button class="btn btn-info btn-xs btn-edit"
                                                                        data-id="{{ $stop->id }}"><i
                                                                        class="fas fa-pencil-alt"></i></button>
                                                                <button class="btn btn-danger btn-xs btn-delete"
                                                                        data-id="{{ $stop->id }}"><i
                                                                        class="fas fa-trash"></i></button>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12 text-center text-muted mt-3">
                                    <em>Chưa có dữ liệu Quận/Huyện cho Tỉnh/Thành này.</em>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="modalForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formModalLabel">Thêm Điểm dừng</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="stop_id" name="id">
                        <div class="form-group">
                            <label for="input-district_id">Quận/Huyện <span class="text-danger">*</span></label>
                            <select name="district_id" id="input-district_id" class="form-control select2"
                                    style="width: 100%;" required>
                                <option value="">-- Chọn Quận/Huyện --</option>
                                @foreach($all_districts_for_modal as $district)
                                    <option value="{{ $district->id }}">{{ $district->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="input-name">Tên Điểm dừng <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" id="input-name"
                                   placeholder="Ví dụ: Bến xe Mỹ Đình" required>
                        </div>
                        <div class="form-group">
                            <label for="input-address">Địa chỉ cụ thể <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="address" id="input-address"
                                   placeholder="Ví dụ: 20 Phạm Hùng, Nam Từ Liêm, Hà Nội" required>
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

    @push('scripts')
        <script>
            $(document).ready(function () {
                $('#input-district_id').select2({
                    theme: 'bootstrap4',
                    dropdownParent: $('#formModal')
                });
                document.querySelectorAll('.stop-list').forEach(function (list) {
                    new Sortable(list, {
                        group: 'stops',
                        animation: 150,
                        onEnd: function (evt) {
                            const districtId = evt.to.dataset.districtId;
                            const order = Array.from(evt.to.children).map(item => item.dataset.stopId);
                            $.ajax({
                                url: '{{ route("admin.stops.updateOrder") }}',
                                type: 'POST',
                                data: {districtId, order},
                                success: (res) => toastr.success(res.message),
                                error: () => toastr.error('Lỗi server, không thể cập nhật.')
                            });
                        }
                    });
                });
                const modal = $('#formModal');
                const form = $('#modalForm');
                const modalLabel = $('#formModalLabel');

                function resetForm() {
                    form[0].reset();
                    $('#stop_id').val('');
                    $('#input-district_id').val(null).trigger('change');
                    form.find('.is-invalid').removeClass('is-invalid');
                    form.find('.invalid-feedback').remove();
                }

                $('#btn-add').on('click', function () {
                    resetForm();
                    modalLabel.text('Thêm Điểm dừng');
                    modal.modal('show');
                });
                $('body').on('click', '.btn-edit', function () {
                    const stopId = $(this).data('id');
                    $.get(`/admin/stops/${stopId}`, function (response) {
                        if (response.success) {
                            resetForm();
                            const data = response.data;
                            modalLabel.text('Cập nhật: ' + data.name);
                            $('#stop_id').val(data.id);
                            $('#input-name').val(data.name);
                            $('#input-address').val(data.address);
                            $('#input-priority').val(data.priority);
                            $('#input-district_id').val(data.district_id).trigger('change');
                            modal.modal('show');
                        }
                    });
                });
                form.on('submit', function (e) {
                    e.preventDefault();
                    const stopId = $('#stop_id').val();
                    let url = stopId ? `/admin/stops/${stopId}` : '{{ route("admin.stops.store") }}';
                    let method = stopId ? 'PUT' : 'POST';
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
                                form.find('.is-invalid, .is-invalid ~ .select2-container .select2-selection').removeClass('is-invalid');
                                form.find('.invalid-feedback').remove();
                                $.each(xhr.responseJSON.errors, function (key, value) {
                                    const field = form.find(`[name="${key}"]`);
                                    if (field.hasClass('select2')) {
                                        field.next('.select2-container').find('.select2-selection').addClass('is-invalid');
                                        field.parent().append(`<div class="invalid-feedback d-block">${value[0]}</div>`);
                                    } else {
                                        field.addClass('is-invalid').after(`<div class="invalid-feedback">${value[0]}</div>`);
                                    }
                                });
                            } else {
                                toastr.error('Đã xảy ra lỗi server.');
                            }
                        }
                    });
                });
                $('body').on('click', '.btn-delete', function () {
                    const stopId = $(this).data('id');
                    Swal.fire({
                        title: 'Bạn có chắc chắn?', text: "Hành động này không thể hoàn tác!", icon: 'warning',
                        showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#d33',
                        confirmButtonText: 'Vâng, xóa nó!', cancelButtonText: 'Hủy'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `/admin/stops/${stopId}`, type: 'DELETE',
                                success: function (response) {
                                    if (response.success) {
                                        $(`.stop-card[data-stop-id="${stopId}"]`).fadeOut(500, function () {
                                            $(this).remove();
                                        });
                                        Swal.fire('Đã xóa!', response.message, 'success');
                                    } else {
                                        Swal.fire('Lỗi!', response.message, 'error');
                                    }
                                },
                                error: () => Swal.fire('Lỗi!', 'Đã xảy ra lỗi server.', 'error')
                            });
                        }
                    })
                });
            });
        </script>
    @endpush
</x-admin.layout>
