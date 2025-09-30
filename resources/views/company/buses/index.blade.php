@extends('layouts.shared.main')
@section('title', 'Quản lý Xe')

@push('styles')
    <style>
        .form-section-title {
            border-left: 4px solid #17a2b8;
            padding-left: 10px;
            margin-bottom: 1.5rem;
            margin-top: 1rem;
            font-size: 1.2rem;
        }

        .service-checkbox {
            margin-right: 15px;
        }

        #buses-table img {
            width: 100px;
            height: 75px;
            object-fit: cover;
            border-radius: 5px;
        }

        /* CSS cho Seat Map Generator */
        #seat-map-preview {
            background-color: #e9ecef;
            border: 1px solid #ced4da;
            border-radius: 5px;
            padding: 15px;
            margin-top: 10px;
            min-height: 150px;
        }

        .seat-deck {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px dashed #adb5bd;
        }

        .seat-deck:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .seat-deck-title {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .seat-row {
            display: flex;
            margin-bottom: 5px;
        }

        .seat {
            width: 35px;
            height: 35px;
            line-height: 35px;
            text-align: center;
            border: 1px solid #adb5bd;
            border-radius: 4px;
            margin-right: 5px;
            background-color: #fff;
            font-size: 12px;
            cursor: pointer;
            user-select: none; /* Ngăn bôi đen chữ khi click nhanh */
            transition: all 0.2s ease;
        }

        .seat:hover {
            background-color: #d1ecf1;
            border-color: #007bff;
        }

        .seat.seat-disabled {
            background-color: #6c757d;
            color: white;
            text-decoration: line-through;
            border-color: #5a6268;
            cursor: not-allowed;
        }

        .seat-aisle { /* Lối đi */
            width: 20px;
            height: 35px;
            margin-right: 5px;
        }

        .seat-map-legend {
            display: flex;
            gap: 15px;
            font-size: 14px;
        }

        .legend-item {
            display: flex;
            align-items: center;
        }

        .legend-color-box {
            width: 20px;
            height: 20px;
            border: 1px solid #ccc;
            margin-right: 5px;
        }
    </style>
@endpush

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Danh sách Xe</h3>
            <button class="btn btn-success btn-sm" id="btn-add">
                <i class="fas fa-plus"></i> Thêm Xe mới
            </button>
        </div>
        <div class="card-body">
            <table id="buses-table" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th style="width: 120px;">Ảnh đại diện</th>
                    <th>Tên xe</th>
                    <th>Dòng xe</th>
                    <th class="text-center">Số ghế</th>
                    <th class="text-center">Ưu tiên</th>
                    <th style="width: 120px;" class="text-right">Hành động</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="bus-modal" tabindex="-1" role="dialog" aria-labelledby="bus-modal-label"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <form id="bus-form" novalidate>
                    <div class="modal-header">
                        <h5 class="modal-title" id="bus-modal-label">Thêm Xe mới</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="bus_id" name="id">

                        <h4 class="form-section-title">Thông tin cơ bản</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Tên xe (Tên gợi nhớ) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                           placeholder="Ví dụ: Xe giường nằm 01" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="model_name">Dòng xe</label>
                                    <input type="text" class="form-control" id="model_name" name="model_name"
                                           placeholder="Ví dụ: Hyundai Universe">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="seat_count">Số ghế</label>
                                    <input type="number" class="form-control" id="seat_count" name="seat_count"
                                           readonly>
                                    <small class="form-text text-muted">Số ghế được tự động tính từ sơ đồ ghế.</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="priority">Độ ưu tiên</label>
                                    <input type="number" class="form-control" id="priority" name="priority" value="0"
                                           required>
                                </div>
                            </div>
                        </div>

                        <h4 class="form-section-title">Sơ đồ ghế & Dịch vụ</h4>
                        <div class="row">
                            <div class="col-md-7">
                                <label>Trình tạo sơ đồ ghế</label>
                                <div class="card p-3">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="generator_decks">Số tầng</label>
                                                <select id="generator_decks" class="form-control">
                                                    <option value="1">1 tầng</option>
                                                    <option value="2">2 tầng</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="generator_rows">Số hàng</label>
                                                <input type="number" id="generator_rows" class="form-control" value="10"
                                                       min="1">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="generator_layout">Bố cục dãy ghế</label>
                                                <select id="generator_layout" class="form-control">
                                                    <option value="1-1">2 Dãy (Ghế - Lối đi - Ghế)</option>
                                                    <option value="1-1-1">3 Dãy (Ghế - Lối đi - Ghế - Lối đi - Ghế)
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <div class="form-group w-100">
                                                <button type="button" id="btn-generate-map" class="btn btn-info w-100">
                                                    Tạo
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="seat-map-preview"></div>
                                    <div class="seat-map-legend mt-2">
                                        <div class="legend-item">
                                            <div class="legend-color-box" style="background-color: #fff;"></div>
                                            <span>Còn trống</span>
                                        </div>
                                        <div class="legend-item">
                                            <div class="legend-color-box seat-disabled"
                                                 style="text-decoration: none;"></div>
                                            <span>Bị vô hiệu hóa</span>
                                        </div>
                                    </div>
                                </div>
                                <textarea class="form-control" id="seat_map" name="seat_map" rows="3" required
                                          style="display: none;"></textarea>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Các dịch vụ đi kèm</label>
                                    <div class="p-3 border rounded h-100">
                                        @forelse($services as $service)
                                            <div class="form-check form-check-inline service-checkbox">
                                                <input class="form-check-input" type="checkbox" name="services[]"
                                                       id="service-{{ $service->id }}" value="{{ $service->name }}">
                                                <label class="form-check-label" for="service-{{ $service->id }}">
                                                    <i class="{{ $service->icon }} mr-1"></i>{{ $service->name }}
                                                </label>
                                            </div>
                                        @empty
                                            <p class="text-muted">Chưa có dịch vụ nào. Vui lòng thêm trong trang
                                                Admin.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h4 class="form-section-title">Hình ảnh & Mô tả</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="thumbnail_url">Ảnh đại diện</label>
                                    <div class="dropzone-wrapper">
                                        <input type="hidden" name="thumbnail_url" id="thumbnail_url" value="">
                                        <div id="dropzone-logo" class="dropzone"
                                             data-upload-url="{{ route('ckfinder_upload') }}">
                                            <div class="dz-message" data-dz-message>
                                                <span>Kéo thả ảnh hoặc <button type="button"
                                                                               class="dz-button">chọn ảnh</button></span>
                                            </div>
                                        </div>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-secondary btn-ckfinder-browse"
                                                data-target-dz="dropzone-logo">
                                            <i class="far fa-folder-open"></i> Duyệt thư viện
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="image_list_url">Album ảnh</label>
                                    <div class="dropzone-wrapper">
                                        <input type="hidden" name="image_list_url" id="image_list_url" value="[]">
                                        <div id="dropzone-album" class="dropzone"
                                             data-upload-url="{{ route('ckfinder_upload') }}">
                                            <div class="dz-message" data-dz-message>
                                                <span>Kéo thả nhiều ảnh hoặc <button type="button" class="dz-button">chọn ảnh</button></span>
                                            </div>
                                        </div>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-secondary btn-ckfinder-browse"
                                                data-target-dz="dropzone-album">
                                            <i class="far fa-folder-open"></i> Duyệt thư viện
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="content">Nội dung giới thiệu xe</label>
                            <textarea name="content" id="content" class="form-control"></textarea>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary" id="save-bus-btn">Lưu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function () {
            let contentEditor;
            initCkEditor('#content').then(editor => contentEditor = editor).catch(e => console.error(e));
            initDropzoneDefault('dropzone-logo');
            initDropzoneMultipleImages('dropzone-album');

            const table = $('#buses-table').DataTable({
                //... Cấu hình DataTables giữ nguyên
                processing: true,
                serverSide: true,
                ajax: '{{ route("company.buses.list") }}',
                columns: [
                    {
                        data: 'thumbnail_url', name: 'thumbnail_url', orderable: false, searchable: false,
                        render: function (data) {
                            const placeholder = '{{ asset("/shared/dist/img/placeholder.png") }}';
                            const imageUrl = data ? data : placeholder;
                            return `<img src="${imageUrl}" alt="Bus thumbnail" onerror="this.onerror=null;this.src='${placeholder}';"/>`;
                        }
                    },
                    {data: 'name', name: 'name'},
                    {data: 'model_name', name: 'model_name'},
                    {data: 'seat_count', name: 'seat_count', className: 'text-center'},
                    {data: 'priority', name: 'priority', className: 'text-center'},
                    {data: 'action', name: 'action', className: 'text-right', orderable: false, searchable: false},
                ]
            });

            const modal = $('#bus-modal');
            const form = $('#bus-form');

            // ==========================================================
            // === LOGIC CHO TRÌNH TẠO SƠ ĐỒ GHẾ - BẮT ĐẦU
            // ==========================================================
            const previewContainer = $('#seat-map-preview');
            const hiddenSeatMapInput = $('#seat_map');
            const seatCountInput = $('#seat_count');

            function generateSeatMap() {
                const decks = parseInt($('#generator_decks').val(), 10);
                const rows = parseInt($('#generator_rows').val(), 10);
                const layout = $('#generator_layout').val().split('-').map(Number);

                let seatMapJson = [];
                let totalSeats = 0;
                previewContainer.empty();

                for (let d = 1; d <= decks; d++) {
                    const deckDiv = $('<div class="seat-deck"></div>');
                    if (decks > 1) {
                        deckDiv.append(`<div class="seat-deck-title">Tầng ${d}</div>`);
                    }

                    for (let r = 1; r <= rows; r++) {
                        const rowDiv = $('<div class="seat-row"></div>');
                        let charCode = 65; // 'A'

                        for (let i = 0; i < layout.length; i++) {
                            // Vẽ số ghế trong 1 cụm
                            for (let j = 0; j < layout[i]; j++) {
                                const seatLabel = `${String.fromCharCode(charCode++)}${r}`;
                                const seatNumber = `T${d}-${seatLabel}`;
                                rowDiv.append(`<div class="seat" data-seat-number="${seatNumber}">${seatLabel}</div>`);
                                seatMapJson.push({
                                    seat_number: seatNumber,
                                    status: 'available',
                                    deck: d
                                });
                                totalSeats++;
                            }
                            // Vẽ lối đi nếu không phải cụm ghế cuối cùng
                            if (i < layout.length - 1) {
                                rowDiv.append('<div class="seat-aisle"></div>');
                            }
                        }
                        deckDiv.append(rowDiv);
                    }
                    previewContainer.append(deckDiv);
                }

                hiddenSeatMapInput.val(JSON.stringify(seatMapJson, null, 2));
                seatCountInput.val(totalSeats);
            }

            function renderSeatMapPreview(seatMapString) {
                previewContainer.empty();
                if (!seatMapString) return;
                try {
                    const seatMap = JSON.parse(seatMapString);
                    if (!Array.isArray(seatMap)) return;

                    const decks = {};
                    seatMap.forEach(seat => {
                        const deckNum = seat.deck || 1;
                        if (!decks[deckNum]) decks[deckNum] = [];
                        decks[deckNum].push(seat);
                    });

                    Object.keys(decks).sort().forEach(deckNumber => {
                        const deckDiv = $('<div class="seat-deck"></div>');
                        if (Object.keys(decks).length > 1) {
                            deckDiv.append(`<div class="seat-deck-title">Tầng ${deckNumber}</div>`);
                        }

                        const rows = {};
                        decks[deckNumber].forEach(seat => {
                            const rowNumber = parseInt(seat.seat_number.match(/\d+$/)[0], 10);
                            if (!rows[rowNumber]) rows[rowNumber] = [];
                            rows[rowNumber].push(seat);
                        });

                        Object.keys(rows).sort((a, b) => a - b).forEach(rowNumber => {
                            const rowDiv = $('<div class="seat-row"></div>');
                            const rowSeats = rows[rowNumber].sort((a, b) => a.seat_number.localeCompare(b.seat_number));

                            rowSeats.forEach(seat => {
                                const seatLabel = seat.seat_number.replace(`T${deckNumber}-`, '');
                                const isDisabled = seat.status !== 'available';
                                rowDiv.append(`<div class="seat ${isDisabled ? 'seat-disabled' : ''}" data-seat-number="${seat.seat_number}">${seatLabel}</div>`);
                            });
                            deckDiv.append(rowDiv);
                        });
                        previewContainer.append(deckDiv);
                    });

                } catch (e) {
                    console.error("Invalid seat map JSON", e);
                }
            }

            $('#btn-generate-map').on('click', generateSeatMap);

            previewContainer.on('click', '.seat', function () {
                const clickedSeat = $(this);
                const seatNumber = clickedSeat.data('seat-number');

                try {
                    let seatMap = JSON.parse(hiddenSeatMapInput.val());
                    const targetSeat = seatMap.find(s => s.seat_number === seatNumber);

                    if (targetSeat) {
                        // Toggle status
                        targetSeat.status = (targetSeat.status === 'available') ? 'disabled' : 'available';

                        // Toggle UI class
                        clickedSeat.toggleClass('seat-disabled');

                        // Update hidden input
                        hiddenSeatMapInput.val(JSON.stringify(seatMap, null, 2));
                        toastr.info(`Đã đổi trạng thái ghế ${seatNumber.replace('T1-', '')}`);
                    }
                } catch (e) {
                    toastr.error('Lỗi dữ liệu sơ đồ ghế. Vui lòng tạo lại.');
                }
            });

            // ==========================================================
            // === LOGIC CHO TRÌNH TẠO SƠ ĐỒ GHẾ - KẾT THÚC
            // ==========================================================

            function resetForm() {
                form[0].reset();
                form.find('input[type="hidden"]').val('');
                form.find('.is-invalid').removeClass('is-invalid').next('.invalid-feedback').remove();
                if (contentEditor) contentEditor.setData('');
                Dropzone.forElement("#dropzone-logo").removeAllFiles(true);
                Dropzone.forElement("#dropzone-album").removeAllFiles(true);
                $('input[name="services[]"]').prop('checked', false);
                previewContainer.empty();
                seatCountInput.val(0);
            }

            $('#btn-add').on('click', function () {
                resetForm();
                $('#bus-modal-label').text('Thêm Xe mới');
                modal.modal('show');
            });

            $('#buses-table').on('click', '.edit-btn', function () {
                const id = $(this).data('id');
                $.get(`/company/buses/${id}`, function (response) {
                    if (response.success) {
                        resetForm();
                        const data = response.data;
                        $('#bus-modal-label').text('Chỉnh sửa xe: ' + data.name);
                        $('#bus_id').val(data.id);
                        $('#name').val(data.name);
                        $('#model_name').val(data.model_name);
                        $('#priority').val(data.priority);

                        const seatMapStr = data.seat_map ? JSON.stringify(JSON.parse(data.seat_map), null, 2) : '[]';
                        hiddenSeatMapInput.val(seatMapStr);
                        seatCountInput.val(data.seat_count);
                        renderSeatMapPreview(data.seat_map);

                        if (data.services && Array.isArray(data.services)) {
                            data.services.forEach(service => {
                                $(`input[name="services[]"][value="${service}"]`).prop('checked', true);
                            });
                        }
                        if (contentEditor) contentEditor.setData(data.content || '');
                        const logoDz = Dropzone.forElement("#dropzone-logo");
                        $('#thumbnail_url').val(data.thumbnail_url || '');
                        if (data.thumbnail_url) {
                            const mockFile = {
                                name: data.thumbnail_url.split('/').pop(),
                                size: 12345,
                                serverUrl: data.thumbnail_url
                            };
                            logoDz.emit("addedfile", mockFile);
                            logoDz.emit("thumbnail", mockFile, data.thumbnail_url);
                            logoDz.emit("complete", mockFile);
                            logoDz.files.push(mockFile);
                        }
                        const albumDz = Dropzone.forElement("#dropzone-album");
                        $('#image_list_url').val(data.image_list_url || '[]');
                        try {
                            const images = JSON.parse(data.image_list_url || '[]');
                            if (Array.isArray(images)) {
                                images.forEach(url => {
                                    const mockFile = {name: url.split('/').pop(), size: 12345, serverUrl: url};
                                    albumDz.emit("addedfile", mockFile);
                                    albumDz.emit("thumbnail", mockFile, url);
                                    albumDz.emit("complete", mockFile);
                                    albumDz.files.push(mockFile);
                                });
                            }
                        } catch (e) {
                        }

                        modal.modal('show');
                    }
                });
            });

            form.on('submit', function (e) {
                e.preventDefault();
                const id = $('#bus_id').val();
                const url = id ? `/company/buses/${id}` : '{{ route("company.buses.store") }}';
                const method = 'POST';

                let formData = new FormData(this);
                if (id) formData.append('_method', 'PUT');
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
                            table.ajax.reload();
                            toastr.success(response.message);
                        }
                    },
                    error: function (xhr) {
                        form.find('.is-invalid').removeClass('is-invalid').next('.invalid-feedback').remove();
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                $('#' + key).addClass('is-invalid').after(`<div class="invalid-feedback">${value[0]}</div>`);
                            });
                            toastr.error('Vui lòng kiểm tra lại thông tin đã nhập.');
                        } else {
                            toastr.error('Đã xảy ra lỗi. Vui lòng thử lại.');
                        }
                    }
                });
            });

            $('#buses-table').on('click', '.delete-btn', function () {
                // ... Logic xóa giữ nguyên
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Bạn có chắc chắn muốn xóa?',
                    text: "Hành động này không thể hoàn tác!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Vâng, xóa nó!',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/company/buses/${id}`,
                            method: 'DELETE',
                            success: function (response) {
                                table.ajax.reload();
                                toastr.success(response.message);
                            },
                            error: (xhr) => toastr.error(xhr.responseJSON.message || 'Đã xảy ra lỗi khi xóa.')
                        });
                    }
                });
            });
        });
    </script>
@endpush
