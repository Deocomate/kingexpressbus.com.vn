<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Example CKFinder - Single Init</title>
    @include("layouts.shared.partials.styles")
</head>
<body>

<div class="container mt-5">
    <h4>Chọn ảnh đại diện</h4>
    <div class="form-group">
        <label for="input-avatar">Avatar URL</label>
        <div class="input-group">
            <input readonly type="text" class="form-control" name="avatar" id="input-avatar"
                   required value="">
            <span class="input-group-append">
                <button type="button" class="btn btn-secondary ckfinder-button">
                    Duyệt Ảnh
                </button>
            </span>
        </div>
        <div style="margin-top: 10px;">
            <img src="" alt="Image Preview" class="ckfinder-preview-image"
                 style="max-width: 200px; max-height: 200px; display: none;">
        </div>
    </div>

    <hr>

    <h4>Chọn ảnh bìa</h4>
    <div class="form-group">
        <label for="input-banner">Banner URL</label>
        <div class="input-group">
            <input readonly type="text" class="form-control" name="banner" id="input-banner"
                   required value="">
            <span class="input-group-append">
                <button type="button" class="btn btn-secondary ckfinder-button">
                    Duyệt Ảnh
                </button>
            </span>
        </div>
        <div style="margin-top: 10px;">
            <img src="" alt="Image Preview" class="ckfinder-preview-image"
                 style="max-width: 300px; max-height: 200px; display: none;">
        </div>
    </div>
</div>

@include("layouts.shared.partials.scripts")

<script>
    document.addEventListener("DOMContentLoaded", function () {
        initSingleCKFinder('#input-avatar');
        initSingleCKFinder('#input-banner');
    });
</script>

</body>
</html>
