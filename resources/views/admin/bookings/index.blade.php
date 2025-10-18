{{-- resources/views/admin/bookings/index.blade.php --}}
<x-admin.layout title="Quản lý Đặt vé">

    <x-slot:breadcrumb>
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Quản lý Đặt vé</li>
    </x-slot:breadcrumb>

    @push('styles')
        <style>
            /* Style cho modal */
            #bookingDetailModal .modal-body dt {
                font-weight: 600;
                color: #495057;
            }

            #bookingDetailModal .modal-body h6 {
                font-size: 1.1rem;
                color: #17a2b8;
                border-bottom: 1px solid #dee2e6;
                padding-bottom: 8px;
                margin-top: 1.5rem;
                margin-bottom: 1rem;
            }

            #bookingDetailModal .modal-body h6:first-child {
                margin-top: 0;
            }

            #bookingDetailModal .modal-body dd {
                margin-bottom: 0.5rem;
            }

            /* Style cho trạng thái */
            .status-badge {
                font-size: 0.85em;
                padding: 0.4em 0.7em;
            }

            /* Style cho nút hành động */
            .action-buttons .btn {
                margin: 0 2px;
            }

            /* Pre tag for notes */
            #modal-notes {
                white-space: pre-wrap;
                word-break: break-word;
                font-family: inherit;
                font-size: inherit;
                background-color: #f8f9fa;
                padding: 10px;
                border-radius: 4px;
                border: 1px solid #dee2e6;
                max-height: 150px;
                overflow-y: auto;
            }
        </style>
    @endpush

    {{-- Main Content Card --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Danh sách đặt vé</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>

        <div class="card-body">
            {{-- Filters Section --}}
            <div class="row mb-3">
                <div class="col-md-12">
                    <form method="GET" action="{{ route('admin.bookings.index') }}" class="form-inline flex-wrap">
                        {{-- Status Filter --}}
                        <div class="form-group mr-sm-2 mb-2">
                            <label for="filter-status" class="mr-2">Trạng thái:</label>
                            <select name="status" id="filter-status" class="form-control form-control-sm">
                                <option value="">Tất cả</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xác
                                    nhận
                                </option>
                                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Đã
                                    xác nhận
                                </option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã
                                    hủy
                                </option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn
                                    thành
                                </option>
                            </select>
                        </div>
                        {{-- Date Filters --}}
                        <div class="form-group mr-sm-2 mb-2">
                            <label for="filter-start-date" class="mr-2">Từ ngày:</label>
                            <input type="date" name="start_date" id="filter-start-date"
                                   class="form-control form-control-sm" value="{{ request('start_date') }}">
                        </div>
                        <div class="form-group mr-sm-2 mb-2">
                            <label for="filter-end-date" class="mr-2">Đến ngày:</label>
                            <input type="date" name="end_date" id="filter-end-date" class="form-control form-control-sm"
                                   value="{{ request('end_date') }}">
                        </div>
                        {{-- Search Filter --}}
                        <div class="form-group mr-sm-2 mb-2 flex-grow-1">
                            <label for="filter-search" class="mr-2 sr-only">Tìm kiếm:</label>
                            <input type="text" name="search" id="filter-search"
                                   class="form-control form-control-sm w-100" placeholder="Mã vé, tên, SĐT..."
                                   value="{{ request('search') }}">
                        </div>
                        {{-- Action Buttons --}}
                        <button type="submit" class="btn btn-primary btn-sm mr-2 mb-2"><i class="fas fa-search"></i> Lọc
                        </button>
                        <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary btn-sm mb-2"><i
                                class="fas fa-redo"></i> Đặt lại</a>
                    </form>
                </div>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                    <tr>
                        <th style="width: 50px">#</th>
                        <th>Mã vé</th>
                        <th>Khách hàng</th>
                        <th>Tuyến đường</th>
                        <th>Ngày đi</th>
                        <th>Tổng tiền</th>
                        <th class="text-center">Trạng thái</th>
                        <th style="width: 150px" class="text-center">Hành động</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        // Helper function to generate status badge HTML
                        function getStatusBadge($status) {
                            $statusClasses = [
                                'pending' => 'badge-warning',
                                'confirmed' => 'badge-success',
                                'cancelled' => 'badge-danger',
                                'completed' => 'badge-primary',
                            ];
                            $statusTexts = [
                                'pending' => 'Chờ xác nhận',
                                'confirmed' => 'Đã xác nhận',
                                'cancelled' => 'Đã hủy',
                                'completed' => 'Hoàn thành',
                            ];
                            $class = $statusClasses[$status] ?? 'badge-secondary';
                            $text = $statusTexts[$status] ?? ucfirst($status);
                            return '<span class="badge status-badge ' . $class . '">' . $text . '</span>';
                        }
                    @endphp
                    @forelse($bookings as $booking)
                        <tr id="booking-row-{{ $booking->id }}">
                            <td>{{ $loop->iteration + ($bookings->currentPage() - 1) * $bookings->perPage() }}</td>
                            <td><strong>#{{ $booking->booking_code }}</strong></td>
                            <td>
                                <div>{{ $booking->customer_name }}</div>
                                <small class="text-muted">{{ $booking->customer_phone }}</small>
                            </td>
                            <td>{{ $booking->route_name }}</td>
                            <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</td>
                            <td class="text-right"><strong>{{ number_format($booking->total_price, 0, ',', '.') }}
                                    đ</strong></td>
                            <td class="text-center status-cell">{!! getStatusBadge($booking->status) !!}</td>
                            <td class="text-center action-buttons">
                                <div class="btn-group">
                                    {{-- Nút xem chi tiết --}}
                                    <button type="button" class="btn btn-sm btn-info btn-show-details"
                                            data-id="{{ $booking->id }}" title="Chi tiết"><i class="fas fa-eye"></i>
                                    </button>

                                    {{-- Nút xác nhận --}}
                                    @if($booking->status === 'pending')
                                        <button type="button" class="btn btn-sm btn-success btn-update-status"
                                                data-id="{{ $booking->id }}" data-status="confirmed" title="Xác nhận"><i
                                                class="fas fa-check"></i></button>
                                    @endif

                                    {{-- Nút hủy --}}
                                    @if(in_array($booking->status, ['pending', 'confirmed']))
                                        <button type="button" class="btn btn-sm btn-danger btn-update-status"
                                                data-id="{{ $booking->id }}" data-status="cancelled" title="Hủy vé"><i
                                                class="fas fa-times"></i></button>
                                    @endif

                                    {{-- Nút hoàn thành --}}
                                    @if($booking->status === 'confirmed')
                                        <button type="button" class="btn btn-sm btn-primary btn-update-status"
                                                data-id="{{ $booking->id }}" data-status="completed" title="Hoàn thành">
                                            <i class="fas fa-check-double"></i></button>
                                    @endif

                                    {{-- Nút Xóa (thêm nếu cần) --}}
                                    {{--
                                    <button type="button" class="btn btn-sm btn-danger btn-delete-booking" data-id="{{ $booking->id }}" title="Xóa đặt vé"><i class="fas fa-trash"></i></button>
                                    --}}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Không có dữ liệu đặt vé nào.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($bookings->hasPages())
            <div class="card-footer clearfix">
                <div class="float-left">
                    <small class="text-muted">Hiển thị {{ $bookings->firstItem() }} - {{ $bookings->lastItem() }}
                        / {{ $bookings->total() }}</small>
                </div>
                <div class="float-right">{{ $bookings->withQueryString()->links() }}</div>
            </div>
        @endif
    </div>

    {{-- Booking Detail Modal --}}
    <div class="modal fade" id="bookingDetailModal" tabindex="-1" role="dialog"
         aria-labelledby="bookingDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookingDetailModalLabel">Chi tiết Đặt vé #<span
                            id="modal-booking-code"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div id="modal-loading" class="text-center py-5"><i
                            class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                        <p class="mt-2">Đang tải...</p></div>
                    <div id="modal-content-details" style="display: none;">
                        <h6><i class="fas fa-user text-muted mr-2"></i>Thông tin Khách hàng</h6>
                        <dl class="row mb-3">
                            <dt class="col-sm-4">Họ tên</dt>
                            <dd class="col-sm-8" id="modal-customer-name"></dd>
                            <dt class="col-sm-4">Số điện thoại</dt>
                            <dd class="col-sm-8" id="modal-customer-phone"></dd>
                            <dt class="col-sm-4">Email</dt>
                            <dd class="col-sm-8" id="modal-customer-email"></dd>
                        </dl>
                        <h6><i class="fas fa-route text-muted mr-2"></i>Thông tin Chuyến đi</h6>
                        <dl class="row mb-3">
                            <dt class="col-sm-4">Tuyến đường</dt>
                            <dd class="col-sm-8" id="modal-route-name"></dd>
                            <dt class="col-sm-4">Nhà xe</dt>
                            <dd class="col-sm-8" id="modal-company-name"></dd>
                            <dt class="col-sm-4">Xe</dt>
                            <dd class="col-sm-8" id="modal-bus-name"></dd>
                            <dt class="col-sm-4">Ngày đi</dt>
                            <dd class="col-sm-8" id="modal-booking-date"></dd>
                            <dt class="col-sm-4">Giờ khởi hành</dt>
                            <dd class="col-sm-8" id="modal-start-time"></dd>
                            <dt class="col-sm-4">Giờ đến (dự kiến)</dt>
                            <dd class="col-sm-8" id="modal-end-time"></dd>
                            <dt class="col-sm-4">Điểm đón</dt>
                            <dd class="col-sm-8" id="modal-pickup-stop"></dd>
                            <dt class="col-sm-4">Điểm trả</dt>
                            <dd class="col-sm-8" id="modal-dropoff-stop"></dd>
                            <dt class="col-sm-4">Số lượng vé</dt>
                            <dd class="col-sm-8" id="modal-quantity"></dd>
                        </dl>
                        <h6><i class="fas fa-dollar-sign text-muted mr-2"></i>Thanh toán & Trạng thái</h6>
                        <dl class="row mb-0">
                            <dt class="col-sm-4">Tổng tiền</dt>
                            <dd class="col-sm-8 font-weight-bold text-success" id="modal-total-price"></dd>
                            <dt class="col-sm-4">Phương thức</dt>
                            <dd class="col-sm-8" id="modal-payment-method"></dd>
                            <dt class="col-sm-4">Thanh toán</dt>
                            <dd class="col-sm-8" id="modal-payment-status"></dd>
                            <dt class="col-sm-4">Trạng thái vé</dt>
                            <dd class="col-sm-8" id="modal-status"></dd>
                            <dt class="col-sm-4">Ghi chú</dt>
                            <dd class="col-sm-8">
                                <pre id="modal-notes"></pre>
                            </dd>
                        </dl>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Custom Scripts --}}
    @push('scripts')
        <script>
            // Helper function to get status badge HTML
            function getStatusBadgeHtml(status) {
                const statusClasses = {
                    'pending': 'badge-warning', 'confirmed': 'badge-success',
                    'cancelled': 'badge-danger', 'completed': 'badge-primary'
                };
                const statusTexts = {
                    'pending': 'Chờ xác nhận', 'confirmed': 'Đã xác nhận',
                    'cancelled': 'Đã hủy', 'completed': 'Hoàn thành'
                };
                const cssClass = statusClasses[status] || 'badge-secondary';
                const text = statusTexts[status] || status;
                return `<span class="badge status-badge ${cssClass}">${text}</span>`;
            }

            // Helper function to generate action buttons HTML based on status
            function getActionButtonsHtml(bookingId, status) {
                let buttons = `<button type="button" class="btn btn-sm btn-info btn-show-details" data-id="${bookingId}" title="Chi tiết"><i class="fas fa-eye"></i></button>`;
                if (status === 'pending') {
                    buttons += ` <button type="button" class="btn btn-sm btn-success btn-update-status" data-id="${bookingId}" data-status="confirmed" title="Xác nhận"><i class="fas fa-check"></i></button>`;
                }
                if (['pending', 'confirmed'].includes(status)) {
                    buttons += ` <button type="button" class="btn btn-sm btn-danger btn-update-status" data-id="${bookingId}" data-status="cancelled" title="Hủy vé"><i class="fas fa-times"></i></button>`;
                }
                if (status === 'confirmed') {
                    buttons += ` <button type="button" class="btn btn-sm btn-primary btn-update-status" data-id="${bookingId}" data-status="completed" title="Hoàn thành"><i class="fas fa-check-double"></i></button>`;
                }
                // Add delete button if needed:
                // buttons += ` <button type="button" class="btn btn-sm btn-danger btn-delete-booking" data-id="${bookingId}" title="Xóa đặt vé"><i class="fas fa-trash"></i></button>`;
                return `<div class="btn-group">${buttons}</div>`;
            }


            $(document).ready(function () {
                const showUrlTemplate = `{{ route('admin.bookings.show', ['booking' => ':id']) }}`;
                const updateUrlTemplate = `{{ route('admin.bookings.update', ['booking' => ':id']) }}`;
                // Optional: const deleteUrlTemplate = `{{ route('admin.bookings.destroy', ['booking' => ':id']) }}`;

                // --- Show Booking Details Modal ---
                $('.table').on('click', '.btn-show-details', function () {
                    const bookingId = $(this).data('id');
                    const modal = $('#bookingDetailModal');
                    const loading = $('#modal-loading');
                    const content = $('#modal-content-details');
                    const codeSpan = $('#modal-booking-code');

                    // Reset
                    codeSpan.text('');
                    content.find('dd').text('N/A');
                    $('#modal-payment-status, #modal-status').html(getStatusBadgeHtml('unknown'));
                    $('#modal-notes').text('Không có');
                    content.hide();
                    loading.show();
                    modal.modal('show');

                    $.ajax({
                        url: showUrlTemplate.replace(':id', bookingId),
                        type: 'GET',
                        dataType: 'json',
                        success: function (response) {
                            if (response.success && response.data) {
                                const data = response.data;
                                codeSpan.text(data.booking_code);
                                $('#modal-customer-name').text(data.customer_name || 'N/A');
                                $('#modal-customer-phone').text(data.customer_phone || 'N/A');
                                $('#modal-customer-email').text(data.customer_email || 'N/A');
                                $('#modal-route-name').text(data.route_name || 'N/A');
                                $('#modal-company-name').text(data.company_name || 'N/A');
                                $('#modal-bus-name').text(data.bus_name || 'N/A');
                                $('#modal-booking-date').text(data.booking_date ? new Date(data.booking_date).toLocaleDateString('vi-VN') : 'N/A');
                                $('#modal-start-time').text(data.start_time ? data.start_time.substring(0, 5) : 'N/A');
                                $('#modal-end-time').text(data.end_time ? data.end_time.substring(0, 5) : 'N/A');

                                // Use pre-processed pickup_display
                                $('#modal-pickup-stop').text(data.pickup_display || 'N/A');

                                let dropoffStop = data.dropoff_stop_name || 'N/A';
                                if (data.dropoff_stop_address) dropoffStop += ` - ${data.dropoff_stop_address}`;
                                $('#modal-dropoff-stop').text(dropoffStop);

                                $('#modal-quantity').text(data.quantity || 'N/A');
                                $('#modal-total-price').text(data.total_price ? new Intl.NumberFormat('vi-VN').format(data.total_price) + 'đ' : 'N/A');

                                let paymentMethod = data.payment_method === 'online_banking' ? 'Chuyển khoản' : (data.payment_method === 'cash_on_pickup' ? 'Tiền mặt khi lên xe' : 'N/A');
                                $('#modal-payment-method').text(paymentMethod);

                                let paymentStatus = data.payment_status === 'paid' ? '<span class="badge status-badge badge-success">Đã thanh toán</span>' : '<span class="badge status-badge badge-warning">Chưa thanh toán</span>';
                                $('#modal-payment-status').html(paymentStatus);

                                $('#modal-status').html(getStatusBadgeHtml(data.status));

                                // Use pre-processed notes_display
                                $('#modal-notes').text(data.notes_display || 'Không có');

                                content.show();
                            } else {
                                toastr.error(response.message || 'Không thể tải chi tiết đặt vé.');
                                modal.modal('hide');
                            }
                        },
                        error: (xhr) => {
                            toastr.error('Lỗi khi tải dữ liệu: ' + (xhr.responseJSON?.message || 'Lỗi không xác định'));
                            modal.modal('hide');
                        },
                        complete: () => loading.hide()
                    });
                });

                // --- Update Booking Status ---
                $('.table').on('click', '.btn-update-status', function () {
                    const button = $(this); // Store button reference
                    const bookingId = button.data('id');
                    const newStatus = button.data('status');
                    const url = updateUrlTemplate.replace(':id', bookingId);

                    let swalConfig = { /* ... Swal config remains the same ... */
                        title: 'Bạn chắc chắn?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        cancelButtonText: 'Hủy'
                    };
                    let ajaxData = {status: newStatus};

                    if (newStatus === 'confirmed') {
                        swalConfig.text = "Xác nhận đặt vé này?";
                        swalConfig.confirmButtonColor = '#28a745';
                        swalConfig.confirmButtonText = 'Xác nhận';
                    } else if (newStatus === 'cancelled') {
                        swalConfig.text = "Hủy đặt vé này?";
                        swalConfig.icon = 'warning';
                        swalConfig.confirmButtonColor = '#dc3545';
                        swalConfig.confirmButtonText = 'Hủy vé';
                        swalConfig.input = 'textarea';
                        swalConfig.inputPlaceholder = 'Nhập lý do hủy (không bắt buộc)...';
                        swalConfig.inputAttributes = {'aria-label': 'Nhập lý do hủy'};
                    } else if (newStatus === 'completed') {
                        swalConfig.text = "Đánh dấu đặt vé này là hoàn thành?";
                        swalConfig.confirmButtonColor = '#007bff';
                        swalConfig.confirmButtonText = 'Hoàn thành';
                    }


                    Swal.fire(swalConfig).then((result) => {
                        if (result.isConfirmed) {
                            if (newStatus === 'cancelled' && result.value !== undefined) {
                                ajaxData.notes = result.value || '';
                            }

                            // Add loading state to button
                            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

                            $.ajax({
                                url: url,
                                type: 'PUT',
                                data: ajaxData,
                                success: function (response) {
                                    if (response.success && response.updated_data) {
                                        Swal.fire({
                                            title: 'Thành công!',
                                            text: response.message,
                                            icon: 'success',
                                            timer: 1000,
                                            showConfirmButton: false
                                        });

                                        // Update UI directly
                                        const row = $(`#booking-row-${bookingId}`);
                                        if (row.length) {
                                            row.find('.status-cell').html(getStatusBadgeHtml(response.updated_data.status));
                                            row.find('.action-buttons').html(getActionButtonsHtml(bookingId, response.updated_data.status));
                                        }
                                        // Optional: Update modal if open
                                        if ($('#bookingDetailModal').hasClass('show') && $('#modal-booking-code').text() === $(`#booking-row-${bookingId} strong`).text().replace('#', '')) {
                                            $('#modal-status').html(getStatusBadgeHtml(response.updated_data.status));
                                            // Update notes in modal if necessary
                                            // $('#modal-notes').text(response.updated_data.notes || 'Không có');
                                        }
                                    } else {
                                        Swal.fire('Lỗi!', response.message || 'Cập nhật thất bại.', 'error');
                                    }
                                },
                                error: (xhr) => Swal.fire('Lỗi!', xhr.responseJSON?.message || 'Có lỗi xảy ra.', 'error'),
                                complete: () => button.prop('disabled', false).html(button.attr('title') === 'Xác nhận' ? '<i class="fas fa-check"></i>' : (button.attr('title') === 'Hủy vé' ? '<i class="fas fa-times"></i>' : '<i class="fas fa-check-double"></i>')) // Restore original icon based on title
                            });
                        }
                    });
                });

                // Optional: Delete Booking Logic (if needed)
                /*
                $('.table').on('click', '.btn-delete-booking', function () {
                    const bookingId = $(this).data('id');
                    const url = deleteUrlTemplate.replace(':id', bookingId);

                    Swal.fire({
                        title: 'Bạn chắc chắn muốn xóa?',
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
                                url: url,
                                type: 'DELETE',
                                success: function (response) {
                                    if (response.success) {
                                        $(`#booking-row-${bookingId}`).fadeOut(500, function() { $(this).remove(); });
                                        Swal.fire('Đã xóa!', response.message, 'success');
                                    } else {
                                        Swal.fire('Lỗi!', response.message || 'Không thể xóa.', 'error');
                                    }
                                },
                                error: (xhr) => Swal.fire('Lỗi!', xhr.responseJSON?.message || 'Có lỗi xảy ra.', 'error')
                            });
                        }
                    });
                });
                */

            });
        </script>
    @endpush
</x-admin.layout>
