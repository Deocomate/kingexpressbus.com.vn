@extends('layouts.shared.main')
@section('title', 'Quản lý Đặt vé')
@push('styles')
    <style>
        .modal-body .row {
            margin-bottom: 1rem;
        }

        .modal-body dt {
            font-weight: 600;
            color: #555;
        }

        .action-buttons .btn {
            margin-right: 5px;
        }

        .modal-body h6 {
            font-size: 1.1rem;
            color: #007bff;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        .itinerary-point {
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border: 1px solid #e9ecef;
        }
    </style>
@endpush
@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Bộ lọc và Tìm kiếm</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.bookings.index') }}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="search">Tìm kiếm</label>
                            <input type="text" name="search" id="search" class="form-control"
                                   value="{{ request('search') }}" placeholder="Mã vé, Tên, SĐT...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="status">Trạng thái</label>
                            <select name="status" id="status" class="form-control">
                                <option value="all" @selected(request('status') == 'all')>Tất cả</option>
                                <option value="pending" @selected(request('status') == 'pending')>Chờ xác nhận</option>
                                <option value="confirmed" @selected(request('status') == 'confirmed')>Đã xác nhận
                                </option>
                                <option value="completed" @selected(request('status') == 'completed')>Hoàn thành
                                </option>
                                <option value="cancelled" @selected(request('status') == 'cancelled')>Đã hủy</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="start_date">Từ ngày</label>
                            <input type="date" name="start_date" id="start_date" class="form-control"
                                   value="{{ request('start_date') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="end_date">Đến ngày</label>
                            <input type="date" name="end_date" id="end_date" class="form-control"
                                   value="{{ request('end_date') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Lọc</button>
                        <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary"><i
                                class="fas fa-sync-alt"></i> Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Danh sách Đặt vé</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped projects">
                    <thead>
                    <tr>
                        <th style="width: 1%">#</th>
                        <th>Mã vé</th>
                        <th>Khách hàng</th>
                        <th>Tuyến đường</th>
                        <th>Ngày đi</th>
                        <th>Tổng tiền</th>
                        <th class="text-center">Trạng thái</th>
                        <th style="width: 20%" class="text-right">Hành động</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($bookings as $booking)
                        <tr id="booking-row-{{ $booking->id }}">
                            <td>{{ $loop->iteration + ($bookings->currentPage() - 1) * $bookings->perPage() }}</td>
                            <td><strong>#{{ $booking->booking_code }}</strong></td>
                            <td>
                                {{ $booking->customer_name }}<br/>
                                <small>{{ $booking->customer_phone }}</small>
                            </td>
                            <td>{{ $booking->route_name }}</td>
                            <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</td>
                            <td>{{ number_format($booking->total_price, 0, ',', '.') }}đ</td>
                            <td class="project-state text-center">
                                @php
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
                                @endphp
                                <span class="badge {{ $statusClasses[$booking->status] ?? 'badge-secondary' }}">
                                        {{ $statusTexts[$booking->status] ?? ucfirst($booking->status) }}
                                    </span>
                            </td>
                            <td class="project-actions text-right action-buttons">
                                <button class="btn btn-primary btn-sm btn-view" data-id="{{ $booking->id }}">
                                    <i class="fas fa-folder"></i> Xem
                                </button>
                                <button class="btn btn-info btn-sm btn-edit" data-id="{{ $booking->id }}">
                                    <i class="fas fa-pencil-alt"></i> Sửa
                                </button>
                                <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $booking->id }}">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
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
        <div class="card-footer clearfix">
            {{ $bookings->links("pagination::bootstrap-4") }}
        </div>
    </div>
    <div class="modal fade" id="viewBookingModal" tabindex="-1" role="dialog" aria-labelledby="viewBookingModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewBookingModalLabel">Chi tiết Đặt vé</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3 align-items-center">
                        <div class="col-md-6">
                            <h4>Mã vé: #<span id="view-booking-code" class="text-primary font-weight-bold"></span></h4>
                        </div>
                        <div class="col-md-6 text-md-right">
                            <span id="view-status" class="mr-2"></span>
                            <span id="view-payment-status"></span>
                        </div>
                    </div>

                    <div class="card card-outline card-primary">
                        <div class="card-body">
                            <h6><i class="fas fa-route text-muted mr-2"></i><strong>Thông tin chuyến đi</strong></h6>
                            <dl class="row mb-0">
                                <dt class="col-sm-4"><i class="fas fa-building fa-fw mr-1"></i>Nhà xe</dt>
                                <dd class="col-sm-8" id="view-company-name"></dd>

                                <dt class="col-sm-4"><i class="fas fa-road fa-fw mr-1"></i>Tuyến đường</dt>
                                <dd class="col-sm-8" id="view-route-name"></dd>

                                <dt class="col-sm-4"><i class="fas fa-bus fa-fw mr-1"></i>Xe</dt>
                                <dd class="col-sm-8" id="view-bus-name"></dd>

                                <dt class="col-sm-4"><i class="fas fa-calendar-alt fa-fw mr-1"></i>Ngày đi</dt>
                                <dd class="col-sm-8" id="view-booking-date"></dd>

                                <dt class="col-sm-4"><i class="fas fa-clock fa-fw mr-1"></i>Giờ khởi hành</dt>
                                <dd class="col-sm-8" id="view-start-time"></dd>

                                <dt class="col-sm-4"><i class="fas fa-ticket-alt fa-fw mr-1"></i>Số lượng vé</dt>
                                <dd class="col-sm-8" id="view-quantity"></dd>

                                <dt class="col-sm-4"><i class="fas fa-chair fa-fw mr-1"></i>Số ghế</dt>
                                <dd class="col-sm-8" id="view-quantity"></dd>
                            </dl>
                            <hr>

                            <h6><i class="fas fa-user text-muted mr-2"></i><strong>Thông tin khách hàng</strong></h6>
                            <dl class="row mb-0">
                                <dt class="col-sm-4"><i class="fas fa-user fa-fw mr-1"></i>Họ tên</dt>
                                <dd class="col-sm-8" id="view-customer-name"></dd>

                                <dt class="col-sm-4"><i class="fas fa-phone fa-fw mr-1"></i>Số điện thoại</dt>
                                <dd class="col-sm-8" id="view-customer-phone"></dd>

                                <dt class="col-sm-4"><i class="fas fa-envelope fa-fw mr-1"></i>Email</dt>
                                <dd class="col-sm-8" id="view-customer-email"></dd>
                            </dl>
                            <hr>

                            <h6><i class="fas fa-map-marker-alt text-muted mr-2"></i><strong>Hành trình</strong></h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="itinerary-point">
                                        <strong>Điểm đón:</strong>
                                        <div id="view-pickup-point"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="itinerary-point">
                                        <strong>Điểm trả:</strong>
                                        <div id="view-dropoff-point"></div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <h6><i class="fas fa-sticky-note text-muted mr-2"></i><strong>Ghi chú của khách
                                    hàng</strong></h6>
                            <p id="view-notes" class="mb-0"></p>
                        </div>
                    </div>
                    <div class="text-right mt-3">
                        <h5>TỔNG CỘNG: <span id="view-total-price" class="text-success font-weight-bold"></span></h5>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editBookingModal" tabindex="-1" role="dialog" aria-labelledby="editBookingModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="editBookingForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editBookingModalLabel">Cập nhật Đặt vé #<span
                                id="edit-booking-code"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit-booking-id" name="id">
                        <div class="form-group">
                            <label for="edit-status">Trạng thái vé</label>
                            <select name="status" id="edit-status" class="form-control">
                                <option value="pending">Chờ xác nhận</option>
                                <option value="confirmed">Đã xác nhận</option>
                                <option value="completed">Hoàn thành</option>
                                <option value="cancelled">Đã hủy</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit-payment-status">Trạng thái thanh toán</label>
                            <select name="payment_status" id="edit-payment-status" class="form-control">
                                <option value="unpaid">Chưa thanh toán</option>
                                <option value="paid">Đã thanh toán</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit-notes">Ghi chú</label>
                            <textarea name="notes" id="edit-notes" class="form-control" rows="3"></textarea>
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
        $(document).ready(function () {
            const statusClasses = {
                'pending': 'badge-warning', 'confirmed': 'badge-success',
                'cancelled': 'badge-danger', 'completed': 'badge-primary'
            };
            const statusTexts = {
                'pending': 'Chờ xác nhận', 'confirmed': 'Đã xác nhận',
                'cancelled': 'Đã hủy', 'completed': 'Hoàn thành'
            };

            let triggerButton = null;
            const viewModal = $('#viewBookingModal');
            const editModal = $('#editBookingModal');

            viewModal.on('hidden.bs.modal', function () {
                if (triggerButton) {
                    $(triggerButton).focus();
                    triggerButton = null;
                }
            });
            editModal.on('hidden.bs.modal', function () {
                if (triggerButton) {
                    $(triggerButton).focus();
                    triggerButton = null;
                }
            });

            function showLoading() {
                $('body').append('<div class="loading-overlay" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;"><i class="fas fa-spinner fa-spin fa-3x fa-fw" style="color:white;"></i></div>');
            }

            function hideLoading() {
                $('.loading-overlay').remove();
            }

            function fetchBookingData(id, callback) {
                showLoading();
                $.ajax({
                    url: `/admin/bookings/${id}`,
                    type: 'GET',
                    success: function (response) {
                        hideLoading();
                        if (response.success) {
                            callback(response.data);
                        } else {
                            toastr.error(response.message || 'Không thể tải dữ liệu.');
                        }
                    },
                    error: function () {
                        hideLoading();
                        toastr.error('Đã xảy ra lỗi server. Vui lòng thử lại.');
                    }
                });
            }

            function formatDate(dateString) {
                if (!dateString) return 'N/A';
                const date = new Date(dateString);
                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const year = date.getFullYear();
                return `${day}/${month}/${year}`;
            }

            function formatTime(timeString) {
                if (!timeString) return 'N/A';
                return timeString.substring(0, 5);
            }

            function formatCurrency(number) {
                return new Intl.NumberFormat('vi-VN', {style: 'currency', currency: 'VND'}).format(number);
            }

            // View Button Click
            $('.btn-view').on('click', function () {
                triggerButton = this;
                const bookingId = $(this).data('id');
                fetchBookingData(bookingId, function (data) {
                    $('#view-booking-code').text(data.booking_code);
                    $('#view-customer-name').text(data.customer_name);
                    $('#view-customer-phone').text(data.customer_phone);
                    $('#view-customer-email').text(data.customer_email || 'N/A');
                    $('#view-company-name').text(data.company_name);
                    $('#view-route-name').text(data.route_name);
                    $('#view-bus-name').text(data.bus_name);
                    $('#view-booking-date').text(formatDate(data.booking_date));
                    $('#view-start-time').text(formatTime(data.start_time));
                    $('#view-quantity').text(data.quantity);
                    
                    $('#view-total-price').text(formatCurrency(data.total_price));

                    const statusBadge = `<span class="badge ${statusClasses[data.status] || 'badge-secondary'}">${statusTexts[data.status] || data.status}</span>`;
                    $('#view-status').html(statusBadge);

                    const paymentStatusText = data.payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán';
                    const paymentStatusBadge = `<span class="badge ${data.payment_status === 'paid' ? 'badge-success' : 'badge-warning'}">${paymentStatusText}</span>`;
                    $('#view-payment-status').html(paymentStatusBadge);

                    $('#view-pickup-point').html(`<strong>${data.pickup_stop_name}</strong><br><small>${data.pickup_stop_address}</small>`);
                    $('#view-dropoff-point').html(`<strong>${data.dropoff_stop_name}</strong><br><small>${data.dropoff_stop_address}</small>`);
                    $('#view-notes').text(data.notes || 'Không có.');

                    viewModal.modal('show');
                });
            });
            // Edit Button Click
            $('.btn-edit').on('click', function () {
                triggerButton = this;
                const bookingId = $(this).data('id');
                fetchBookingData(bookingId, function (data) {
                    $('#edit-booking-id').val(data.id);
                    $('#edit-booking-code').text(data.booking_code);
                    $('#edit-status').val(data.status);
                    $('#edit-payment-status').val(data.payment_status);
                    $('#edit-notes').val(data.notes);
                    editModal.modal('show');
                });
            });
            // Edit Form Submission
            $('#editBookingForm').on('submit', function (e) {
                e.preventDefault();
                const bookingId = $('#edit-booking-id').val();
                const formData = $(this).serialize();
                showLoading();
                $.ajax({
                    url: `/admin/bookings/${bookingId}`,
                    type: 'PUT',
                    data: formData,
                    success: function (response) {
                        hideLoading();
                        if (response.success) {
                            editModal.modal('hide');
                            toastr.success(response.message);
                            const updatedStatus = $('#edit-status').val();
                            const statusBadge = `<span class="badge ${statusClasses[updatedStatus] || 'badge-secondary'}">${statusTexts[updatedStatus] || updatedStatus}</span>`;
                            $(`#booking-row-${bookingId}`).find('.project-state').html(statusBadge);
                        } else {
                            toastr.error(response.message || 'Cập nhật thất bại.');
                        }
                    },
                    error: function (xhr) {
                        hideLoading();
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorMsg = '<ul>';
                            $.each(errors, function (key, value) {
                                errorMsg += '<li>' + value[0] + '</li>';
                            });
                            errorMsg += '</ul>';
                            toastr.error(errorMsg, 'Lỗi validation!');
                        } else {
                            toastr.error('Đã xảy ra lỗi server. Vui lòng thử lại.');
                        }
                    }
                });
            });
            // Delete Button Click
            $('.btn-delete').on('click', function () {
                const bookingId = $(this).data('id');
                Swal.fire({
                    title: 'Bạn có chắc chắn?',
                    text: "Bạn sẽ không thể hoàn tác hành động này!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Vâng, xóa nó!',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        showLoading();
                        $.ajax({
                            url: `/admin/bookings/${bookingId}`,
                            type: 'DELETE',
                            success: function (response) {
                                hideLoading();
                                if (response.success) {
                                    $(`#booking-row-${bookingId}`).fadeOut(500, function () {
                                        $(this).remove();
                                    });
                                    Swal.fire('Đã xóa!', response.message, 'success');
                                } else {
                                    Swal.fire('Lỗi!', response.message || 'Không thể xóa.', 'error');
                                }
                            },
                            error: function () {
                                hideLoading();
                                Swal.fire('Lỗi!', 'Đã xảy ra lỗi server.', 'error');
                            }
                        });
                    }
                })
            });
        });
    </script>
@endpush
