@php
    $isEdit = isset($profile);
@endphp

<x-admin.layout :title="$isEdit ? 'Chỉnh sửa Cấu hình' : 'Tạo Cấu hình mới'">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.web_profiles.index') }}">Quản lý Cấu hình</a></li>
        <li class="breadcrumb-item active">{{ $isEdit ? 'Chỉnh sửa' : 'Tạo mới' }}</li>
    </x-slot:breadcrumb>

    <form action="{{ $isEdit ? route('admin.web_profiles.update', $profile->id) : route('admin.web_profiles.store') }}"
          method="POST">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">{{ $isEdit ? 'Chỉnh sửa Cấu hình: ' . $profile->profile_name : 'Tạo Cấu hình Website mới' }}</h3>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="profile_name">Tên cấu hình <span class="text-danger">*</span></label>
                            <input type="text" id="profile_name" name="profile_name" class="form-control"
                                   value="{{ old('profile_name', $profile->profile_name ?? '') }}" required>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-center pt-3">
                        <div class="form-group clearfix">
                            <div class="icheck-primary d-inline">
                                <input type="checkbox" id="is_default"
                                       name="is_default" {{ old('is_default', $profile->is_default ?? false) ? 'checked' : '' }}>
                                <label for="is_default">Đặt làm mặc định</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="title">Tiêu đề Website (SEO)</label>
                            <input type="text" id="title" name="title" class="form-control"
                                   value="{{ old('title', $profile->title ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="description">Mô tả Website (SEO)</label>
                            <textarea id="description" name="description" class="form-control"
                                      rows="1">{{ old('description', $profile->description ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="logo_url">Logo URL</label>
                            <div class="input-group">
                                <input readonly type="text" class="form-control" id="logo_url" name="logo_url"
                                       value="{{ old('logo_url', $profile->logo_url ?? '') }}">
                                <span class="input-group-append">
                                    <button type="button" class="btn btn-secondary ckfinder-button">Duyệt ảnh</button>
                                </span>
                            </div>
                            <div style="margin-top: 10px;">
                                @php $logoUrl = old('logo_url', $profile->logo_url ?? ''); @endphp
                                <img src="{{ $logoUrl }}" alt="Logo Preview"
                                     class="img-thumbnail ckfinder-preview-image"
                                     style="max-width: 200px; max-height: 200px; {{ $logoUrl ? '' : 'display: none;' }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="favicon_url">Favicon URL</label>
                            <div class="input-group">
                                <input readonly type="text" class="form-control" id="favicon_url" name="favicon_url"
                                       value="{{ old('favicon_url', $profile->favicon_url ?? '') }}">
                                <span class="input-group-append">
                                    <button type="button" class="btn btn-secondary ckfinder-button">Duyệt ảnh</button>
                                </span>
                            </div>
                            <div style="margin-top: 10px;">
                                @php $faviconUrl = old('favicon_url', $profile->favicon_url ?? ''); @endphp
                                <img src="{{ $faviconUrl }}" alt="Favicon Preview"
                                     class="img-thumbnail ckfinder-preview-image"
                                     style="max-width: 50px; max-height: 50px; {{ $faviconUrl ? '' : 'display: none;' }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group"><label for="email">Email</label><input type="email" id="email"
                                                                                       name="email" class="form-control"
                                                                                       value="{{ old('email', $profile->email ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group"><label for="phone">Số điện thoại</label><input type="text" id="phone"
                                                                                               name="phone"
                                                                                               class="form-control"
                                                                                               value="{{ old('phone', $profile->phone ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group"><label for="hotline">Hotline</label><input type="text" id="hotline"
                                                                                           name="hotline"
                                                                                           class="form-control"
                                                                                           value="{{ old('hotline', $profile->hotline ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group"><label for="whatsapp">WhatsApp</label><input type="text" id="whatsapp"
                                                                                             name="whatsapp"
                                                                                             class="form-control"
                                                                                             value="{{ old('whatsapp', $profile->whatsapp ?? '') }}">
                        </div>
                    </div>
                </div>

                <div class="form-group"><label for="address">Địa chỉ</label><input type="text" id="address"
                                                                                   name="address" class="form-control"
                                                                                   value="{{ old('address', $profile->address ?? '') }}">
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group"><label for="facebook_url">Facebook URL</label><input type="url"
                                                                                                     id="facebook_url"
                                                                                                     name="facebook_url"
                                                                                                     class="form-control"
                                                                                                     value="{{ old('facebook_url', $profile->facebook_url ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group"><label for="zalo_url">Zalo URL</label><input type="url" id="zalo_url"
                                                                                             name="zalo_url"
                                                                                             class="form-control"
                                                                                             value="{{ old('zalo_url', $profile->zalo_url ?? '') }}">
                        </div>
                    </div>
                </div>

                <div class="form-group"><label for="map_embedded">Mã nhúng Google Maps</label><textarea
                            id="map_embedded" name="map_embedded" class="form-control"
                            rows="3">{{ old('map_embedded', $profile->map_embedded ?? '') }}</textarea></div>
                <div class="form-group"><label for="introduction_content">Nội dung giới thiệu</label><textarea
                            id="introduction_content" name="introduction_content"
                            class="form-control">{{ old('introduction_content', $profile->introduction_content ?? '') }}</textarea>
                </div>
                <div class="form-group"><label for="policy_content">Nội dung chính sách</label><textarea
                            id="policy_content" name="policy_content"
                            class="form-control">{{ old('policy_content', $profile->policy_content ?? '') }}</textarea>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Cập nhật' : 'Lưu lại' }}</button>
                <a href="{{ route('admin.web_profiles.index') }}" class="btn btn-secondary">Quay lại</a>
            </div>
        </div>
    </form>

    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                initCkEditor('#introduction_content');
                initCkEditor('#policy_content');

                initSingleCKFinder('#logo_url');
                initSingleCKFinder('#favicon_url');
            });
        </script>
    @endpush
</x-admin.layout>
