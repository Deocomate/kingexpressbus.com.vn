@extends('layouts.shared.main')
@section('title', 'Quản lý Chuyến xe')

@push('styles')
    <style>
        .bus-route-manager {
            display: flex;
            gap: 20px;
        }

        .bus-list-wrapper, .route-list-wrapper {
            flex-basis: 30%;
            flex-shrink: 0;
            background-color: #f4f6f9;
            border-radius: 5px;
            padding: 15px;
            height: 80vh;
            overflow-y: auto;
            border: 1px solid #e3e6f0;
        }

        .route-list-wrapper {
            flex-basis: 70%;
        }

        .manager-header {
            font-size: 1.1rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #007bff;
        }

        .bus-card, .bus-route-card {
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: .3rem;
            margin-bottom: 10px;
            padding: 10px 15px;
            cursor: grab;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .05);
        }

        .bus-card:active, .bus-route-card:active {
            cursor: grabbing;
        }

        .bus-name, .bus-route-bus-name {
            font-weight: 500;
        }

        .bus-details {
            font-size: 0.8em;
            color: #6c757d;
        }

        .draggable-list {
            min-height: 100px;
            list-style: none;
            padding: 0;
        }

        .company-route-container {
            background: #ffffff;
            border: 1px solid #dcdcdc;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .company-route-header {
            font-weight: bold;
            color: #17a2b8;
            margin-bottom: 10px;
        }

        .bus-route-card {
            border-left: 3px solid #28a745;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .sortable-ghost {
            background: #e9ecef;
            border: 1px dashed #adb5bd;
            opacity: 0.7;
        }

        .placeholder-text {
            text-align: center;
            padding: 20px;
            color: #6c757d;
            border: 2px dashed #ced4da;
            border-radius: 5px;
        }
    </style>
@endpush

@section('content')
    <div class="bus-route-manager">
        <!-- Cột danh sách xe -->
        <div class="bus-list-wrapper">
            <h5 class="manager-header"><i class="fas fa-bus"></i> Danh sách xe</h5>
            <ul id="bus-list" class="draggable-list">
                @forelse ($buses as $bus)
                    <li class="bus-card" data-bus-id="{{ $bus->id }}" data-bus-name="{{ $bus->name }}"
                        data-bus-model="{{ $bus->model_name }}">
                        <div class="bus-name">{{ $bus->name }}</div>
                        <div class="bus-details">{{ $bus->model_name }} - {{ $bus->seat_count }} ghế</div>
                    </li>
                @empty
                    <p class="text-muted">Chưa có xe nào. Vui lòng thêm xe trước.</p>
                @endforelse
            </ul>
        </div>

        <!-- Cột danh sách tuyến đường và chuyến xe -->
        <div class="route-list-wrapper">
            <h5 class="manager-header"><i class="fas fa-route"></i> Tuyến đường & Chuyến xe</h5>
            <div class="accordion" id="accordionProvinces">
                @forelse ($startProvinces as $province)
                    <div class="card shadow-none">
                        <div class="card-header bg-light" id="heading-{{ $province->id }}">
                            <h2 class="mb-0">
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
                                @if (isset($companyRoutesByProvince[$province->id]))
                                    @foreach ($companyRoutesByProvince[$province->id] as $companyRoute)
                                        <div class="company-route-container">
                                            <div class="company-route-header">{{ $companyRoute->name }}</div>
                                            <ul class="draggable-list bus-route-list"
                                                data-company-route-id="{{ $companyRoute->id }}">
                                                @if (isset($busRoutes[$companyRoute->id]))
                                                    @foreach ($busRoutes[$companyRoute->id] as $busRoute)
                                                        <li class="bus-route-card"
                                                            data-bus-route-id="{{ $busRoute->id }}">
                                                            <div>
                                                                <div class="bus-route-bus-name"><i
                                                                        class="fas fa-clock text-muted"></i> {{ \Carbon\Carbon::parse($busRoute->start_time)->format('H:i') }}
                                                                    - {{ \Carbon\Carbon::parse($busRoute->end_time)->format('H:i') }}
                                                                    | <i
                                                                        class="fas fa-bus text-muted"></i> {{ $busRoute->bus_name }}
                                                                </div>
                                                                <div class="bus-details">Giá
                                                                    vé: {{ number_format($busRoute->price) }}đ
                                                                </div>
                                                            </div>
                                                            <div class="action-buttons">
                                                                <button class="btn btn-info btn-xs edit-btn"
                                                                        data-id="{{ $busRoute->id }}"><i
                                                                        class="fas fa-pencil-alt"></i></button>
                                                                <button class="btn btn-danger btn-xs delete-btn"
                                                                        data-id="{{ $busRoute->id }}"><i
                                                                        class="fas fa-trash"></i></button>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                @else
                                                    <div class="placeholder-text">Kéo xe vào đây để tạo chuyến</div>
                                                @endif
                                            </ul>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center">Chưa có tuyến đường nào được tạo.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Modal for Adding/Editing Bus Route -->
    <div class="modal fade" id="bus-route-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="bus-route-form">
                    <div class="modal-header">
                        <h5 class="modal-title" id="bus-route-modal-label">Modal Title</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="bus_route_id" name="id">
                        <input type="hidden" id="bus_id" name="bus_id">
                        <input type="hidden" id="company_route_id" name="company_route_id">
                        <div class="form-group">
                            <label>Xe</label>
                            <input type="text" class="form-control" id="modal_bus_name" readonly>
                        </div>
                        <div class="form-group">
                            <label>Tuyến đường</label>
                            <input type="text" class="form-control" id="modal_company_route_name" readonly>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_time">Giờ khởi hành <span class="text-danger">*</span></label>
                                    <div class="input-group date" id="starttimepicker" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input"
                                               data-target="#starttimepicker" id="start_time" name="start_time"
                                               required/>
                                        <div class="input-group-append" data-target="#starttimepicker"
                                             data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="far fa-clock"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_time">Giờ đến (dự kiến) <span class="text-danger">*</span></label>
                                    <div class="input-group date" id="endtimepicker" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input"
                                               data-target="#endtimepicker" id="end_time" name="end_time" required/>
                                        <div class="input-group-append" data-target="#endtimepicker"
                                             data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="far fa-clock"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="price">Giá vé (VNĐ) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="price" name="price" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Lưu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
    <script>
        $(function () {
            const modal = $('#bus-route-modal');
            const form = $('#bus-route-form');
            let cleavePrice = null;

            // Initialize Time Pickers
            $('#starttimepicker, #endtimepicker').datetimepicker({
                format: 'HH:mm',
                icons: {time: 'far fa-clock'}
            });

            // Initialize Price Formatter
            cleavePrice = new Cleave('#price', {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand'
            });

            // --- SORTABLEJS LOGIC ---
            const busListEl = document.getElementById('bus-list');
            const busRouteLists = document.querySelectorAll('.bus-route-list');

            new Sortable(busListEl, {
                group: {name: 'bus-routes', pull: 'clone', put: false},
                animation: 150,
                sort: false
            });

            busRouteLists.forEach(list => {
                new Sortable(list, {
                    group: 'bus-routes',
                    animation: 150,
                    onAdd: function (evt) {
                        const itemEl = evt.item;
                        const companyRouteId = $(evt.to).data('company-route-id');
                        const companyRouteName = $(evt.to).prev('.company-route-header').text();

                        form[0].reset();
                        $('#bus_route_id').val('');
                        $('#bus_id').val($(itemEl).data('bus-id'));
                        $('#company_route_id').val(companyRouteId);
                        $('#modal_bus_name').val($(itemEl).data('bus-name'));
                        $('#modal_company_route_name').val(companyRouteName);
                        $('#bus-route-modal-label').text('Tạo Chuyến xe mới');
                        modal.modal('show');

                        $(evt.to).find('.placeholder-text').hide();
                        $(itemEl).remove();
                    },
                    onEnd: function (evt) {
                        const order = Array.from(evt.target.children).map(item => $(item).data('bus-route-id'));
                        const companyRouteId = $(evt.target).data('company-route-id');
                        $.post('{{ route("company.bus-routes.updateOrder") }}', {
                            order,
                            company_route_id: companyRouteId
                        })
                            .done(res => toastr.success(res.message))
                            .fail(() => toastr.error('Lỗi server, không thể cập nhật thứ tự.'));
                    }
                });
            });

            // --- MODAL & FORM LOGIC ---
            function renderBusRouteCard(busRoute) {
                const formattedPrice = new Intl.NumberFormat('vi-VN').format(busRoute.price);
                const startTime = busRoute.start_time.substring(0, 5);
                const endTime = busRoute.end_time.substring(0, 5);

                return `
                    <li class="bus-route-card" data-bus-route-id="${busRoute.id}">
                        <div>
                            <div class="bus-route-bus-name">
                                <i class="fas fa-clock text-muted"></i> ${startTime} - ${endTime} |
                                <i class="fas fa-bus text-muted"></i> ${busRoute.bus_name}
                            </div>
                            <div class="bus-details">Giá vé: ${formattedPrice}đ</div>
                        </div>
                        <div class="action-buttons">
                            <button class="btn btn-info btn-xs edit-btn" data-id="${busRoute.id}"><i class="fas fa-pencil-alt"></i></button>
                            <button class="btn btn-danger btn-xs delete-btn" data-id="${busRoute.id}"><i class="fas fa-trash"></i></button>
                        </div>
                    </li>`;
            }

            form.on('submit', function (e) {
                e.preventDefault();
                form.find('.is-invalid').removeClass('is-invalid').next('.invalid-feedback').remove();

                const id = $('#bus_route_id').val();
                const url = id ? `/company/bus-routes/${id}` : '{{ route("company.bus-routes.store") }}';
                const method = id ? 'PUT' : 'POST';
                let formData = $(this).serializeArray();

                // Replace formatted price with raw value for submission
                const priceIndex = formData.findIndex(item => item.name === 'price');
                if (priceIndex > -1) {
                    formData[priceIndex].value = cleavePrice.getRawValue();
                }

                $.ajax({
                    url: url, method: method, data: $.param(formData),
                    success: res => {
                        if (res.success) {
                            modal.modal('hide');
                            toastr.success(res.message);
                            if (id) { // Update
                                const cardToUpdate = $(`li[data-bus-route-id="${id}"]`);
                                const newListId = res.data.company_route_id;
                                const oldListId = cardToUpdate.parent().data('company-route-id');

                                cardToUpdate.replaceWith(renderBusRouteCard(res.data));

                            } else { // Create
                                const companyRouteId = res.data.company_route_id;
                                $(`.bus-route-list[data-company-route-id="${companyRouteId}"]`).append(renderBusRouteCard(res.data));
                            }
                        }
                    },
                    error: xhr => {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            $.each(errors, (key, value) => {
                                const field = $(`#${key}`);
                                field.addClass('is-invalid');
                                field.closest('.form-group').append(`<div class="invalid-feedback d-block">${value[0]}</div>`);
                            });
                            toastr.error("Vui lòng kiểm tra lại thông tin");
                        } else {
                            toastr.error("Đã xảy ra lỗi server.");
                        }
                    }
                });
            });

            $('body').on('click', '.edit-btn', function () {
                const id = $(this).data('id');
                $.get(`/company/bus-routes/${id}`, res => {
                    if (res.success) {
                        const data = res.data;
                        const card = $(`li[data-bus-route-id="${id}"]`);
                        form[0].reset();
                        $('#bus_route_id').val(data.id);
                        $('#start_time').val(data.start_time.substring(0, 5));
                        $('#end_time').val(data.end_time.substring(0, 5));
                        cleavePrice.setRawValue(data.price);
                        $('#modal_bus_name').val(card.find('.bus-route-bus-name').text().split('|')[1].trim());
                        $('#modal_company_route_name').val(card.closest('.company-route-container').find('.company-route-header').text());
                        $('#bus-route-modal-label').text('Cập nhật Chuyến xe');
                        modal.modal('show');
                    }
                });
            });

            $('body').on('click', '.delete-btn', function () {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Bạn chắc chắn muốn xóa chuyến xe này?',
                    icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33',
                    confirmButtonText: 'Vâng, xóa nó!', cancelButtonText: 'Hủy'
                }).then(result => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/company/bus-routes/${id}`, method: 'DELETE',
                            success: res => {
                                if (res.success) {
                                    toastr.success(res.message);
                                    $(`li[data-bus-route-id="${id}"]`).fadeOut(500, function () {
                                        $(this).remove();
                                    });
                                }
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush

