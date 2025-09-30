{{-- ===== resources/views/company/profile/index.blade.php ===== --}}
@extends('layouts.shared.main')
@section('title', 'Thông tin nhà xe')

@section('content')
    <div class="card card-info card-outline">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-id-card mr-2"></i>Thông tin Nhà xe</h3>
        </div>
        <div class="card-body">
            <form id="profile-form" novalidate>
                <h4 class="form-section-title">Thông tin cơ bản</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Tên nhà xe <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name"
                                   value="{{ $company->name ?? '' }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="slug">Tên slug (URL) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="slug" name="slug"
                                   value="{{ $company->slug ?? '' }}" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="phone">Số điện thoại</label>
                            <input type="text" class="form-control" id="phone" name="phone"
                                   value="{{ $company->phone ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="hotline">Hotline</label>
                            <input type="text" class="form-control" id="hotline" name="hotline"
                                   value="{{ $company->hotline ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="{{ $company->email ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="address">Địa chỉ</label>
                            <input type="text" class="form-control" id="address" name="address"
                                   value="{{ $company->address ?? '' }}">
                        </div>
                    </div>
                </div>

                <h4 class="form-section-title">Thông tin hiển thị & SEO</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="title">Tiêu đề (SEO)</label>
                            <input type="text" class="form-control" id="title" name="title"
                                   value="{{ $company->title ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="description">Mô tả ngắn (SEO)</label>
                            <textarea class="form-control" id="description" name="description"
                                      rows="1">{{ $company->description ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="content">Nội dung giới thiệu chi tiết</label>
                    <textarea name="content" id="content" class="form-control">{{ $company->content ?? '' }}</textarea>
                </div>

                <h4 class="form-section-title">Hình ảnh</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="thumbnail_url">Logo nhà xe</label>
                            <div class="dropzone-wrapper">
                                <input type="hidden" name="thumbnail_url" id="thumbnail_url"
                                       value="{{ $company->thumbnail_url ?? '' }}">
                                <div id="dropzone-logo" class="dropzone"
                                     data-upload-url="{{ route('ckfinder_upload') }}">
                                    <div class="dz-message" data-dz-message>
                                        <span>Kéo thả ảnh hoặc <button type="button" class="dz-button">chọn ảnh</button></span>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary btn-ckfinder-browse"
                                        data-target-dz="dropzone-logo">
                                    <i class="far fa-folder-open"></i> Duyệt thư viện
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="image_list_url">Album ảnh (xe, văn phòng...)</label>
                            <div class="dropzone-wrapper">
                                <input type="hidden" name="image_list_url" id="image_list_url"
                                       value="{{ $company->image_list_url ?? '[]' }}">
                                <div id="dropzone-album" class="dropzone"
                                     data-upload-url="{{ route('ckfinder_upload') }}">
                                    <div class="dz-message" data-dz-message>
                                        <span>Kéo thả nhiều ảnh hoặc <button type="button"
                                                                             class="dz-button">chọn ảnh</button></span>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary btn-ckfinder-browse"
                                        data-target-dz="dropzone-album">
                                    <i class="far fa-folder-open"></i> Duyệt thư viện
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-footer text-right">
            <button type="submit" class="btn btn-primary" id="save-profile-btn"><i class="fas fa-save mr-2"></i>Lưu thay
                đổi
            </button>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let contentEditor;
            initCkEditor('#content').then(editor => contentEditor = editor).catch(e => console.error(e));

            const nameInput = document.getElementById('name');
            const slugInput = document.getElementById('slug');

            function generateSlug(text) {
                return text.toString().toLowerCase()
                    .replace(/\s+/g, '-')
                    .replace(/[^\w\-]+/g, '')
                    .replace(/\-\-+/g, '-')
                    .replace(/^-+/, '')
                    .replace(/-+$/, '');
            }

            let isSlugManuallyEdited = slugInput.value.length > 0;
            slugInput.addEventListener('change', () => {
                isSlugManuallyEdited = true;
            });

            nameInput.addEventListener('keyup', () => {
                if (!isSlugManuallyEdited) {
                    slugInput.value = generateSlug(nameInput.value);
                }
            });

            initDropzoneDefault('dropzone-logo');
            initDropzoneMultipleImages('dropzone-album');

            const saveButton = document.getElementById('save-profile-btn');
            saveButton.addEventListener('click', function (e) {
                e.preventDefault();
                const form = document.getElementById('profile-form');
                const formData = new FormData(form);

                if (contentEditor) {
                    formData.set('content', contentEditor.getData());
                }

                saveButton.disabled = true;
                saveButton.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i>Đang lưu...`;

                $.ajax({
                    url: '{{ route("company.profile.update") }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(() => location.reload(), 1500);
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            let errorMessages = '';
                            $('.is-invalid').removeClass('is-invalid');

                            $.each(errors, function (key, value) {
                                errorMessages += `<li>${value[0]}</li>`;
                                $(`#${key}`).addClass('is-invalid');
                            });
                            toastr.error(`<ul>${errorMessages}</ul>`, 'Lỗi xác thực dữ liệu!');
                        } else {
                            toastr.error('Đã xảy ra lỗi không mong muốn. Vui lòng thử lại.');
                        }
                    },
                    complete: function () {
                        saveButton.disabled = false;
                        saveButton.innerHTML = `<i class="fas fa-save mr-2"></i>Lưu thay đổi`;
                    }
                });
            });
            $('input, textarea').on('input', function () {
                $(this).removeClass('is-invalid');
            });
        });
    </script>
@endpush
