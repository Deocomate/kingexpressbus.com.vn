<x-admin.layout title="Quản lý Cấu hình Website">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Quản lý Cấu hình Website</li>
    </x-slot:breadcrumb>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Danh sách Cấu hình</h3>
            <a href="{{ route('admin.web_profiles.create') }}" class="btn btn-success btn-sm">
                <i class="fas fa-plus"></i> Thêm Cấu hình mới
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped projects">
                    <thead>
                    <tr>
                        <th style="width: 1%">#</th>
                        <th>Tên Cấu hình</th>
                        <th class="text-center">Trạng thái</th>
                        <th style="width: 30%" class="text-right">Hành động</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($profiles as $profile)
                        <tr id="profile-row-{{ $profile->id }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $profile->profile_name }}</strong>
                                @if ($profile->is_default)
                                    <i class="fas fa-check-circle text-success ml-2" title="Cấu hình mặc định"></i>
                                @endif
                            </td>
                            <td class="project-state text-center">
                                @if ($profile->is_default)
                                    <span class="badge badge-success">Mặc định</span>
                                @else
                                    <span class="badge badge-secondary">Không áp dụng</span>
                                @endif
                            </td>
                            <td class="project-actions text-right">
                                <form action="{{ route('admin.web_profiles.setDefault', $profile->id) }}" method="POST"
                                      class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="btn btn-primary btn-sm" {{ $profile->is_default ? 'disabled' : '' }}>
                                        <i class="fas fa-check"></i> Đặt làm mặc định
                                    </button>
                                </form>
                                <a class="btn btn-info btn-sm"
                                   href="{{ route('admin.web_profiles.edit', $profile->id) }}">
                                    <i class="fas fa-pencil-alt"></i> Sửa
                                </a>
                                <form action="{{ route('admin.web_profiles.destroy', $profile->id) }}" method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa cấu hình này không?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-danger btn-sm" {{ $profile->is_default || $profiles->count() <= 1 ? 'disabled' : '' }}>
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Chưa có cấu hình nào.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin.layout>
