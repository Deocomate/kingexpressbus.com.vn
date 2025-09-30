{{-- ===== resources/views/examples/example-dropzone-ckfinder.blade.php ===== --}}
<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ví dụ: Tích hợp Dropzone & CKFinder</title>
    @include("layouts.shared.partials.styles")
</head>
<body>
<div class="container mt-5 mb-5">
    <h3>Ví dụ Tích hợp Dropzone.js và CKFinder</h3>
    <p class="text-muted">Bản hoàn chỉnh dùng để tham khảo và tái sử dụng.</p>
    <hr>

    {{-- ===== VÍ DỤ 1: UPLOAD MỘT ẢNH ===== --}}
    <h4>1. Upload một ảnh (Single Image)</h4>
    <div class="form-group">
        <label for="avatar_url">Ảnh đại diện</label>
        <div class="dropzone-wrapper">
            {{-- Input ẩn để lưu trữ URL cuối cùng của ảnh --}}
            <input type="hidden" name="avatar_url" id="avatar_url" value="">

            {{-- Container cho Dropzone --}}
            <div id="dropzone-single" class="dropzone"
                 data-upload-url="{{ route('ckfinder_upload') }}">
                <div class="dz-message" data-dz-message>
                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i><br>
                    <span>Kéo thả ảnh hoặc <button type="button" class="dz-button">tải lên</button></span>
                </div>
            </div>

            {{-- Nút để mở CKFinder --}}
            <button type="button" class="btn btn-sm btn-outline-primary btn-ckfinder-browse">
                <i class="far fa-folder-open"></i> Chọn từ thư viện
            </button>
        </div>
        <small class="form-text text-muted">Chỉ cho phép 1 ảnh. Tải lên ảnh mới sẽ thay thế ảnh cũ.</small>
    </div>

    {{-- ===== VÍ DỤ 2: UPLOAD NHIỀU ẢNH ===== --}}
    <h4>2. Upload nhiều ảnh (Multiple Images)</h4>
    <div class="form-group">
        <label for="album_urls">Album ảnh</label>
        <div class="dropzone-wrapper">
            {{-- Input ẩn để lưu trữ JSON array các URL ảnh --}}
            <input type="hidden" name="album_urls" id="album_urls" value="[]">

            {{-- Container cho Dropzone --}}
            <div id="dropzone-multiple" class="dropzone"
                 data-upload-url="{{ route('ckfinder_upload') }}">
                <div class="dz-message" data-dz-message>
                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i><br>
                    <span>Kéo thả nhiều ảnh hoặc <button type="button" class="dz-button">tải lên</button></span>
                </div>
            </div>

            {{-- Nút để mở CKFinder --}}
            <button type="button" class="btn btn-sm btn-outline-primary btn-ckfinder-browse">
                <i class="far fa-folder-open"></i> Chọn từ thư viện
            </button>
        </div>
        <small class="form-text text-muted">Có thể kéo thả, tải lên hoặc chọn nhiều ảnh từ thư viện.</small>
    </div>
</div>

{{-- Tải các thư viện JS chung (jQuery, Bootstrap, Dropzone, CKFinder...) --}}
@include("layouts.shared.partials.scripts")

<script>
    // Chạy các hàm khởi tạo sau khi trang đã tải xong
    document.addEventListener("DOMContentLoaded", function () {
        initDropzoneDefault('dropzone-single');
        initDropzoneMultipleImages('dropzone-multiple');
    });
</script>
</body>
</html>
