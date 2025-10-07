{{-- resources/views/admin/bookings/index.blade.php --}}
<x-admin.layout title="Quản lý Đặt vé">

    <x-slot:breadcrumb>
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Quản lý Đặt vé</li>
    </x-slot>

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
                    <form method="GET" action="{{ route('admin.bookings.index') }}" class="form-inline">
                        <div class="form-group mr-2">
                            <label for="filter-status" class="mr-2">Trạng thái:</label>
                            <select name="status" id="filter-status" class="form-control">
                                <option value="">Tất cả</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                    Chờ xác nhận
                                </option>
                                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>
                                    Đã xác nhận
                                </option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                    Đã hủy
                                </option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                                    Hoàn thành
                                </option>
                            </select>
                        </div>

                        <div class="form-group mr-2">
                            <label for="filter-date" class="mr-2">Ngày đi:</label>
                            <input type="date" name="booking_date" id="filter-date" class="form-control"
                                   value="{{ request('booking_date') }}">
                        </div>

                        <div class="form-group mr-2">
                            <label for="filter-search" class="mr-2">Tìm kiếm:</label>
                            <input type="text" name="search" id="filter-search" class="form-control"
                                   placeholder="Mã vé, tên khách hàng, SĐT..." value="{{ request('search') }}">
                        </div>

                        <button type="submit" class="btn btn-primary mr-2">
                            <i class="fas fa-search"></i> Lọc
                        </button>
                        <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Đặt lại
                        </a>
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
                        <th>Trạng thái</th>
                        <th style="width: 150px">Hành động</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($bookings as $booking)
                        <tr>
                            <td>{{ $loop->iteration + ($bookings->currentPage() - 1) * $bookings->perPage() }}</td>
                            <td>
                                <strong>#{{ $booking->booking_code }}</strong>
                            </td>
                            <td>
                                <div>{{ $booking->customer_name }}</div>
                                <small class="text-muted">{{ $booking->customer_phone }}</small>
                            </td>
                            <td>{{ $booking->route_name }}</td>
                            <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</td>
                            <td>
                                <strong>{{ number_format($booking->total_price, 0, ',', '.') }}đ</strong>
                            </td>
                            <td>
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
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.bookings.show', $booking->id) }}"
                                       class="btn btn-sm btn-info"
                                       title="Chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if($booking->status === 'pending')
                                        <button type="button"
                                                class="btn btn-sm btn-success btn-confirm"
                                                data-id="{{ $booking->id }}"
                                                title="Xác nhận">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif

                                    @if(in_array($booking->status, ['pending', 'confirmed']))
                                        <button type="button"
                                                class="btn btn-sm btn-danger btn-cancel"
                                                data-id="{{ $booking->id }}"
                                                title="Hủy vé">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
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
                    <small class="text-muted">
                        Hiển thị {{ $bookings->firstItem() }} - {{ $bookings->lastItem() }}
                        trong tổng số {{ $bookings->total() }} kết quả
                    </small>
                </div>
                <div class="float-right">
                    {{ $bookings->links() }}
                </div>
            </div>
        @endif
    </div>

    {{-- Custom Scripts --}}
    @push('scripts')
        <script>
            $(document).ready(function () {
                // Xác nhận đặt vé
                $('.btn-confirm').on('click', function () {
                    const bookingId = $(this).data('id');

                    Swal.fire({
                        title: 'Xác nhận đặt vé?',
                        text: "Bạn có chắc chắn muốn xác nhận đặt vé này?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Xác nhận',
                        cancelButtonText: 'Hủy'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `/admin/bookings/${bookingId}/confirm`,
                                type: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function (response) {
                                    Swal.fire({
                                        title: 'Thành công!',
                                        text: response.message || 'Đã xác nhận đặt vé thành công.',
                                        icon: 'success',
                                        timer: 2000
                                    }).then(() => {
                                        location.reload();
                                    });
                                },
                                error: function (xhr) {
                                    Swal.fire({
                                        title: 'Lỗi!',
                                        text: xhr.responseJSON?.message || 'Có lỗi xảy ra khi xác nhận.',
                                        icon: 'error'
                                    });
                                }
                            });
                        }
                    });
                });

                // Hủy đặt vé
                $('.btn-cancel').on('click', function () {
                    const bookingId = $(this).data('id');

                    Swal.fire({
                        title: 'Hủy đặt vé?',
                        text: "Bạn có chắc chắn muốn hủy đặt vé này?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Hủy vé',
                        cancelButtonText: 'Đóng',
                        input: 'textarea',
                        inputPlaceholder: 'Nhập lý do hủy (không bắt buộc)...',
                        inputAttributes: {
                            'aria-label': 'Nhập lý do hủy'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `/admin/bookings/${bookingId}/cancel`,
                                type: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    reason: result.value || ''
                                },
                                success: function (response) {
                                    Swal.fire({
                                        title: 'Thành công!',
                                        text: response.message || 'Đã hủy đặt vé thành công.',
                                        icon: 'success',
                                        timer: 2000
                                    }).then(() => {
                                        location.reload();
                                    });
                                },
                                error: function (xhr) {
                                    Swal.fire({
                                        title: 'Lỗi!',
                                        text: xhr.responseJSON?.message || 'Có lỗi xảy ra khi hủy vé.',
                                        icon: 'error'
                                    });
                                }
                            });
                        }
                    });
                });

                // Auto refresh mỗi 30 giây (tùy chọn)
                // setInterval(function() {
                //     location.reload();
                // }, 30000);
            });
        </script>
    @endpush
</x-admin.layout>
