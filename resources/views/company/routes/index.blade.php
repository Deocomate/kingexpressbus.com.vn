@extends('layouts.shared.main')
@section('title', 'Tuyến đường của Nhà xe')

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

        /* New Styles for Stop Management */
        .stop-list-container {
            display: flex;
            gap: 20px;
        }

        .stop-list-wrapper {
            flex: 1;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px;
            height: 400px;
            overflow-y: auto;
        }

        .stop-list-wrapper h6 {
            font-weight: bold;
            color: #495057;
            padding-bottom: 10px;
            border-bottom: 1px solid #ced4da;
            margin-bottom: 10px;
        }

        .stop-list-ul {
            list-style: none;
            padding: 0;
            min-height: 320px;
        }

        .stop-card-sm {
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 8px 12px;
            margin-bottom: 8px;
            cursor: grab;
        }

        .stop-card-sm .stop-name {
            font-weight: 500;
            font-size: 0.9rem;
        }

        .stop-card-sm .stop-location {
            font-size: 0.8rem;
            color: #6c757d;
        }

        #selected-stops .stop-type-selector {
            margin-top: 8px;
        }

        #selected-stops .stop-type-selector label {
            font-weight: normal;
            font-size: 0.85rem;
            margin-right: 15px;
        }

        .filter-stops {
            width: 100%;
            margin-bottom: 10px;
        }
    </style>
@endpush

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
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
                    <div class="card shadow-none">
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
                                    @if (isset($companyRoutesByProvince[$province->id]))
                                        @foreach ($companyRoutesByProvince[$province->id] as $route)
                                            <li class="route-card" data-route-id="{{ $route->id }}">
                                                <div>
                                                    <span class="route-name">{{ $route->company_route_name }}</span><br>
                                                    <small class="route-details">
                                                        <i class="fas fa-arrow-right"></i> Đích
                                                        đến: {{ $route->end_province_name }}
                                                    </small>
                                                </div>
                                                <div class="action-buttons">
                                                    <button class="btn btn-info btn-sm edit-btn"
                                                            data-id="{{ $route->id }}"><i class="fas fa-pencil-alt"></i>
                                                        Sửa
                                                    </button>
                                                    <button class="btn btn-danger btn-sm delete-btn"
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
                    <p class="text-center">Nhà xe của bạn chưa có tuyến đường nào.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="modal fade" id="route-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <form id="route-form" novalidate>
                    <div class="modal-header">
                        <h5 class="modal-title" id="route-modal-label">Thêm Tuyến đường</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="company_route_id" name="id">
                        <input type="hidden" id="stops_json" name="stops_json">

                        <div class="form-group">
                            <label for="route_id">Chọn tuyến đường gốc <span class="text-danger">*</span></label>
                            <select class="form-control select2" id="route_id" name="route_id" style="width: 100%;"
                                    required>
                                <option value="">-- Vui lòng chọn --</option>
                                @foreach($all_global_routes as $route)
                                    <option value="{{ $route->id }}">{{ $route->text }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="name">Tên tuyến đường (Tên riêng của nhà xe) <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                    <input type="hidden" id="slug" name="slug">
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

                        <hr>
                        <label>Quản lý các điểm dừng</label>
                        <div class="stop-list-container">
                            <div class="stop-list-wrapper">
                                <h6><i class="fas fa-list-ul"></i> Điểm dừng có sẵn</h6>
                                <input type="text" class="form-control form-control-sm filter-stops"
                                       placeholder="Lọc điểm dừng...">
                                <ul id="available-stops" class="stop-list-ul">
                                    @foreach($all_stops as $stop)
                                        <li class="stop-card-sm" data-stop-id="{{ $stop->id }}">
                                            <div class="stop-name">{{ $stop->name }}</div>
                                            <div class="stop-location">{{ $stop->address }} ({{$stop->location}})</div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="stop-list-wrapper">
                                <h6><i class="fas fa-check-square"></i> Điểm dừng đã chọn (kéo thả để sắp xếp)</h6>
                                <ul id="selected-stops" class="stop-list-ul"></ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary" id="save-route-btn">Lưu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function () {
            const modal = $('#route-modal');
            const form = $('#route-form');
            $('#route_id').select2({theme: 'bootstrap4', dropdownParent: modal});

            // Initialize SortableJS for route list
            document.querySelectorAll('.route-list').forEach(listEl => {
                new Sortable(listEl, {
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    onEnd: evt => {
                        const order = Array.from(evt.target.children).map(item => item.dataset.routeId);
                        $.post('{{ route("company.company-routes.updateOrder") }}', {order})
                            .done(res => toastr.success(res.message))
                            .fail(() => toastr.error('Lỗi server, không thể cập nhật thứ tự.'));
                    }
                });
            });

            // --- New Stop Management Logic ---
            const availableStopsEl = document.getElementById('available-stops');
            const selectedStopsEl = document.getElementById('selected-stops');

            const sharedSortableConfig = {
                group: 'stops',
                animation: 150,
                ghostClass: 'sortable-ghost'
            };

            new Sortable(availableStopsEl, {...sharedSortableConfig, sort: false});
            new Sortable(selectedStopsEl, {
                ...sharedSortableConfig,
                onAdd: function (evt) {
                    const itemEl = evt.item;
                    const stopTypeSelector = `
                        <div class="stop-type-selector">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="stop_type_${itemEl.dataset.stopId}" value="pickup" checked>
                                <label class="form-check-label">Điểm đón</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="stop_type_${itemEl.dataset.stopId}" value="dropoff">
                                <label class="form-check-label">Điểm trả</label>
                            </div>
                             <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="stop_type_${itemEl.dataset.stopId}" value="both">
                                <label class="form-check-label">Cả hai</label>
                            </div>
                        </div>`;
                    $(itemEl).append(stopTypeSelector);
                },
                onRemove: function (evt) {
                    $(evt.item).find('.stop-type-selector').remove();
                }
            });

            $('.filter-stops').on('keyup', function () {
                const filterText = $(this).val().toLowerCase();
                $('#available-stops .stop-card-sm').each(function () {
                    const stopText = $(this).text().toLowerCase();
                    $(this).toggle(stopText.indexOf(filterText) > -1);
                });
            });

            // === HELPERS ===
            function generateSlug(text) {
                return text.toString().toLowerCase()
                    .replace(/\s+/g, '-').replace(/[^\w\-]+/g, '')
                    .replace(/\-\-+/g, '-').replace(/^-+/, '').replace(/-+$/, '');
            }

            function resetForm() {
                form[0].reset();
                $('#company_route_id').val('');
                $('#route_id').val(null).trigger('change');
                // Clear and reset stop lists
                $('#selected-stops').empty();
                $('#available-stops').empty();
                const allStopsHtml = @json($all_stops->map(fn($s) => '<li class="stop-card-sm" data-stop-id="'.$s->id.'"><div class="stop-name">'.$s->name.'</div><div class="stop-location">'.$s->address.' ('.$s->location.')</div></li>')->implode(''));
                $('#available-stops').html(allStopsHtml);
                form.find('.is-invalid').removeClass('is-invalid').next('.invalid-feedback').remove();
            }

            // === EVENT LISTENERS ===
            $('#name').on('input', function () {
                $('#slug').val(generateSlug($(this).val()));
            });

            $('#route_id').on('change', function () {
                const selectedText = $(this).find('option:selected').text();
                if (selectedText && selectedText !== '-- Vui lòng chọn --') {
                    $('#name').val(selectedText.split(' (')[0]).trigger('input');
                }
            });

            $('#btn-add').on('click', () => {
                resetForm();
                $('#route-modal-label').text('Thêm Tuyến đường');
                modal.modal('show');
            });

            $('body').on('click', '.edit-btn', function () {
                const id = $(this).data('id');
                $.get(`/company/company-routes/${id}`, function (response) {
                    if (response.success) {
                        resetForm();
                        const data = response.data;
                        $('#route-modal-label').text('Chỉnh sửa Tuyến đường: ' + data.name);
                        $('#company_route_id').val(data.id);
                        $('#route_id').val(data.route_id).trigger('change');
                        $('#name').val(data.name);
                        $('#slug').val(data.slug);
                        $('#priority').val(data.priority);

                        // Populate selected stops with details
                        const selectedStops = data.stops || [];
                        selectedStops.forEach(stop => {
                            // Find and move the stop from available to selected
                            const availableStopEl = $(`#available-stops .stop-card-sm[data-stop-id="${stop.stop_id}"]`);
                            if (availableStopEl.length) {
                                availableStopEl.appendTo('#selected-stops');
                                // Add and check the correct radio button
                                const stopTypeSelectorHtml = `
                                <div class="stop-type-selector">
                                    <div class="form-check form-check-inline"> <input class="form-check-input" type="radio" name="stop_type_${stop.stop_id}" value="pickup"> <label class="form-check-label">Điểm đón</label> </div>
                                    <div class="form-check form-check-inline"> <input class="form-check-input" type="radio" name="stop_type_${stop.stop_id}" value="dropoff"> <label class="form-check-label">Điểm trả</label> </div>
                                    <div class="form-check form-check-inline"> <input class="form-check-input" type="radio" name="stop_type_${stop.stop_id}" value="both"> <label class="form-check-label">Cả hai</label> </div>
                                </div>`;
                                const newEl = $('#selected-stops').find(`.stop-card-sm[data-stop-id="${stop.stop_id}"]`);
                                newEl.append(stopTypeSelectorHtml);
                                newEl.find(`input[name="stop_type_${stop.stop_id}"][value="${stop.stop_type}"]`).prop('checked', true);
                            }
                        });

                        modal.modal('show');
                    }
                });
            });

            form.on('submit', function (e) {
                e.preventDefault();

                // Serialize stops data into JSON
                const stopsData = [];
                $('#selected-stops .stop-card-sm').each(function () {
                    const stopId = $(this).data('stop-id');
                    const stopType = $(this).find(`input[name="stop_type_${stopId}"]:checked`).val();
                    stopsData.push({stop_id: stopId, stop_type: stopType});
                });
                $('#stops_json').val(JSON.stringify(stopsData));

                const id = $('#company_route_id').val();
                const url = id ? `/company/company-routes/${id}` : '{{ route("company.company-routes.store") }}';
                let formData = new FormData(this);
                if (id) formData.append('_method', 'PUT');

                $.ajax({
                    url: url, method: 'POST', data: formData, processData: false, contentType: false,
                    success: res => {
                        if (res.success) {
                            modal.modal('hide');
                            toastr.success(res.message);
                            setTimeout(() => location.reload(), 1500);
                        }
                    },
                    error: xhr => {
                        form.find('.is-invalid, .is-invalid ~ .select2-container .select2-selection').removeClass('is-invalid');
                        form.find('.invalid-feedback').remove();
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            $.each(errors, (key, value) => {
                                const field = $('#' + key.replace('_json', ''));
                                const container = field.next('.select2-container');
                                if (container.length) {
                                    container.find('.select2-selection').addClass('is-invalid');
                                    field.parent().append(`<div class="invalid-feedback d-block">${value[0]}</div>`);
                                } else {
                                    field.addClass('is-invalid').after(`<div class="invalid-feedback">${value[0]}</div>`);
                                }
                            });
                            toastr.error('Vui lòng kiểm tra lại thông tin.');
                        } else {
                            toastr.error('Đã xảy ra lỗi server.');
                        }
                    }
                });
            });

            $('body').on('click', '.delete-btn', function () {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Bạn có chắc chắn?', text: "Các chuyến xe thuộc tuyến này cũng sẽ bị ảnh hưởng!",
                    icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6', confirmButtonText: 'Vâng, xóa nó!', cancelButtonText: 'Hủy'
                }).then(result => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/company/company-routes/${id}`, method: 'DELETE',
                            success: res => {
                                if (res.success) {
                                    toastr.success(res.message);
                                    $(`.route-card[data-route-id="${id}"]`).fadeOut(500, function () {
                                        $(this).remove();
                                    });
                                }
                            },
                            error: xhr => toastr.error(xhr.responseJSON.message || 'Đã xảy ra lỗi khi xóa.')
                        });
                    }
                });
            });
        });
    </script>
@endpush
