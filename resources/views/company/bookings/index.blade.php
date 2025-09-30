@extends('layouts.shared.main')
@section('title', 'Quản lý Đặt vé')

@push('styles')
    <style>
        .modal-body dt {
            font-weight: 600;
        }

        .modal-body h6 {
            font-size: 1.1rem;
            color: #007bff;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
    </style>
@endpush

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Danh sách Đặt vé</h3>
        </div>
        <div class="card-body">
            <table id="bookings-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Mã vé</th>
                        <th>Khách hàng</th>
                        <th>Tuyến đường</th>
                        <th>Ngày đi</th>
                        <th>Tổng tiền</th>
                        <th class="text-center">Trạng thái</th>
                        <th style="width: 120px;" class="text-right">Hành động</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <!-- View Booking Modal -->
    <div class="modal fade" id="viewBookingModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chi tiết Đặt vé #<span id="view-booking-code" class="text-primary"></span></h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <h6><i class="fas fa-route text-muted mr-2"></i>Thông tin chuyến đi</h6>
                    <dl class="row mb-3">
                        <dt class="col-sm-4">Tuyến đường</dt>
                        <dd class="col-sm-8" id="view-company_route_name"></dd>
                        <dt class="col-sm-4">Xe</dt>
                        <dd class="col-sm-8" id="view-bus_name"></dd>
                        <dt class="col-sm-4">Ngày đi</dt>
                        <dd class="col-sm-8" id="view-booking_date"></dd>
                        <dt class="col-sm-4">Giờ khởi hành</dt>
                        <dd class="col-sm-8" id="view-start_time"></dd>
                        <dt class="col-sm-4">Số lượng vé</dt>
                        <dd class="col-sm-8" id="view-quantity"></dd>
                        <dt class="col-sm-4">Số ghế</dt>
                        <dd class="col-sm-8" id="view-quantity"></dd>
                    </dl>
                    <h6><i class="fas fa-user text-muted mr-2"></i>Thông tin khách hàng</h6>
                    <dl class="row mb-3">
                        <dt class="col-sm-4">Họ tên</dt>
                        <dd class="col-sm-8" id="view-customer_name"></dd>
                        <dt class="col-sm-4">Số điện thoại</dt>
                        <dd class="col-sm-8" id="view-customer_phone"></dd>
                        <dt class="col-sm-4">Email</dt>
                        <dd class="col-sm-8" id="view-customer_email"></dd>
                    </dl>
                    <h6><i class="fas fa-dollar-sign text-muted mr-2"></i>Thanh toán & Trạng thái</h6>
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Tổng tiền</dt>
                        <dd class="col-sm-8 font-weight-bold text-success" id="view-total_price"></dd>
                        <dt class="col-sm-4">Trạng thái vé</dt>
                        <dd class="col-sm-8" id="view-status"></dd>
                        <dt class="col-sm-4">Trạng thái thanh toán</dt>
                        <dd class="col-sm-8" id="view-payment_status"></dd>
                    </dl>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="updateStatusForm">
                    <div class="modal-header">
                        <h5 class="modal-title">Cập nhật vé #<span id="update-booking-code" class="text-primary"></span>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="update-booking-id">
                        <div class="form-group">
                            <label for="status">Trạng thái đặt vé</label>
                            <select class="form-control" id="status" name="status">
                                <option value="pending">Chờ xác nhận</option>
                                <option value="confirmed">Đã xác nhận</option>
                                <option value="completed">Hoàn thành</option>
                                <option value="cancelled">Đã hủy</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            const table = $('#bookings-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('company.bookings.list') }}',
                    error: function(xhr, e, dt) {
                        console.error("DataTables error:", xhr.responseText);
                    }
                },
                columns: [{
                        data: 'booking_code',
                        name: 'b.booking_code'
                    },
                    {
                        data: 'customer_info',
                        name: 'b.customer_name'
                    },
                    {
                        data: 'company_route_name',
                        name: 'cr.name'
                    },
                    {
                        data: 'booking_date',
                        name: 'b.booking_date'
                    },
                    {
                        data: 'total_price',
                        name: 'b.total_price'
                    },
                    {
                        data: 'status',
                        name: 'b.status',
                        className: 'text-center'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-right'
                    }
                ],
            });

            // View Modal Logic
            $('#bookings-table').on('click', '.view-btn', function() {
                const bookingId = $(this).data('id');
                $.get(`/company/bookings/${bookingId}`, function(response) {
                    if (response.success) {
                        const data = response.data;
                        $('#view-booking-code').text(data.booking_code);
                        $('#view-company_route_name').text(data.company_route_name);
                        $('#view-bus_name').text(data.bus_name);
                        $('#view-booking_date').text(new Date(data.booking_date).toLocaleDateString(
                            'vi-VN'));
                        $('#view-start_time').text(data.start_time.substring(0, 5));
                        $('#view-quantity').text(data.quantity);
                        $('#view-customer_name').text(data.customer_name);
                        $('#view-customer_phone').text(data.customer_phone);
                        $('#view-customer_email').text(data.customer_email || 'N/A');
                        $('#view-total_price').text(new Intl.NumberFormat('vi-VN').format(data
                            .total_price) + 'đ');
                        $('#view-status').html(table.cell($(`button[data-id=${data.id}]`).closest(
                            'tr'), 5).data());
                        const paymentStatusText = data.payment_status === 'paid' ? 'Đã thanh toán' :
                            'Chưa thanh toán';
                        const paymentBadge =
                            `<span class="badge ${data.payment_status === 'paid' ? 'badge-success' : 'badge-info'}">${paymentStatusText}</span>`;
                        $('#view-payment_status').html(paymentBadge);
                        $('#viewBookingModal').modal('show');
                    }
                });
            });

            // Update Status Modal Logic
            $('#bookings-table').on('click', '.update-btn', function() {
                const bookingId = $(this).data('id');
                $.get(`/company/bookings/${bookingId}`, function(response) {
                    if (response.success) {
                        $('#update-booking-id').val(response.data.id);
                        $('#update-booking-code').text(response.data.booking_code);
                        $('#status').val(response.data.status);
                        $('#updateStatusModal').modal('show');
                    }
                });
            });

            $('#updateStatusForm').on('submit', function(e) {
                e.preventDefault();
                const bookingId = $('#update-booking-id').val();
                const status = $('#status').val();
                $.ajax({
                    url: `/company/bookings/${bookingId}/update-status`,
                    method: 'PUT',
                    data: {
                        status: status
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#updateStatusModal').modal('hide');
                            table.ajax.reload();
                            toastr.success(response.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Đã xảy ra lỗi, vui lòng thử lại.');
                    }
                });
            });

        });
    </script>
@endpush
