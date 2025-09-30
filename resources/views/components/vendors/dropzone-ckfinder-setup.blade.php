{{-- ===== resources/views/components/vendors/dropzone-ckfinder-setup.blade.php ===== --}}
<style>
    .form-section-title {
        border-left: 4px solid #17a2b8;
        padding-left: 10px;
        margin-bottom: 1.5rem;
        margin-top: 1.5rem;
        font-size: 1.2rem;
    }
    .dropzone-wrapper {
        position: relative;
    }
    .dropzone {
        border: 2px dashed #007bff;
        border-radius: 8px;
        background: #f8f9fa;
        transition: background-color 0.2s ease-in-out;
        padding: 1rem;
    }
    .dropzone .dz-message {
        font-size: 1.1rem;
        color: #6c757d;
    }
    .dropzone .dz-message .dz-button {
        background: none;
        border: none;
        color: #007bff;
        font-weight: bold;
        padding: 0;
        text-decoration: underline;
    }
    .dropzone .dz-preview {
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .dropzone .dz-preview .dz-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .btn-ckfinder-browse {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 10;
    }
</style>

<script>
    function initDropzoneDefault(dropzone_element_id) {
        const dropzoneElement = document.getElementById(dropzone_element_id);
        if (!dropzoneElement) return;

        const wrapper = dropzoneElement.closest('.dropzone-wrapper');
        const hiddenInput = wrapper.querySelector('input[type="hidden"]');
        const uploadUrl = dropzoneElement.dataset.uploadUrl;

        const myDropzone = new Dropzone(dropzoneElement, {
            url: uploadUrl,
            paramName: 'upload',
            maxFiles: 1,
            acceptedFiles: 'image/*',
            addRemoveLinks: true,
            dictRemoveFile: 'Xóa ảnh',
            dictMaxFilesExceeded: 'Bạn chỉ có thể tải lên 1 ảnh.',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
            init: function() {
                const existingFileUrl = hiddenInput.value;
                if (existingFileUrl) {
                    const mockFile = { name: existingFileUrl.split('/').pop(), size: 12345, accepted: true, serverUrl: existingFileUrl };
                    this.emit("addedfile", mockFile);
                    this.emit("thumbnail", mockFile, existingFileUrl);
                    this.emit("complete", mockFile);
                    this.files.push(mockFile);
                }
                this.on("maxfilesexceeded", (file) => {
                    this.removeAllFiles();
                    this.addFile(file);
                });
            }
        });

        myDropzone.on("success", (file, response) => {
            if (response && response.url) {
                hiddenInput.value = response.url;
                file.serverUrl = response.url;
                toastr.success('Tải ảnh lên thành công!');
            } else {
                 toastr.error(response.error.message || 'Lỗi: Server không trả về URL ảnh hợp lệ.');
                 myDropzone.removeFile(file);
            }
        });

        myDropzone.on("removedfile", function(file) {
            const fileUrlToDelete = file.serverUrl || hiddenInput.value;
            if(hiddenInput.value === fileUrlToDelete) {
               hiddenInput.value = '';
            }
            toastr.info('Đã xóa ảnh khỏi giao diện.');
        });

        wrapper.querySelector('.btn-ckfinder-browse').addEventListener('click', function() {
             openCkfinderForDropzone(myDropzone, hiddenInput, false);
        });
    }

    function initDropzoneMultipleImages(dropzone_element_id) {
        const dropzoneElement = document.getElementById(dropzone_element_id);
        if (!dropzoneElement) return;

        const wrapper = dropzoneElement.closest('.dropzone-wrapper');
        const hiddenInput = wrapper.querySelector('input[type="hidden"]');
        const uploadUrl = dropzoneElement.dataset.uploadUrl;

        const updateHiddenInput = (action, url) => {
            let urls = [];
            try { urls = hiddenInput.value ? JSON.parse(hiddenInput.value) : []; } catch (e) { urls = []; }

            if (action === 'add' && !urls.includes(url)) {
                urls.push(url);
            } else if (action === 'remove') {
                const index = urls.indexOf(url);
                if (index > -1) urls.splice(index, 1);
            }
            hiddenInput.value = JSON.stringify(urls);
        };

        const myDropzone = new Dropzone(dropzoneElement, {
            url: uploadUrl,
            paramName: 'upload',
            acceptedFiles: 'image/*',
            addRemoveLinks: true,
            dictRemoveFile: 'Xóa ảnh',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
            init: function() {
                try {
                    const existingFiles = hiddenInput.value ? JSON.parse(hiddenInput.value) : [];
                    if (Array.isArray(existingFiles)) {
                        existingFiles.forEach(url => {
                            if (url) {
                                const mockFile = { name: url.split('/').pop(), size: 12345, accepted: true, serverUrl: url };
                                this.emit("addedfile", mockFile);
                                this.emit("thumbnail", mockFile, url);
                                this.emit("complete", mockFile);
                                this.files.push(mockFile);
                            }
                        });
                    }
                } catch (e) {}
            }
        });

        myDropzone.on("success", (file, response) => {
            if (response && response.url) {
                file.serverUrl = response.url;
                updateHiddenInput('add', response.url);
                toastr.success(`Đã tải lên: ${file.name}`);
            } else {
                toastr.error(response.error.message || 'Lỗi: Server không trả về URL ảnh hợp lệ.');
                myDropzone.removeFile(file);
            }
        });

        myDropzone.on("removedfile", function(file) {
            const fileUrlToDelete = file.serverUrl;
            if (fileUrlToDelete) {
                updateHiddenInput('remove', fileUrlToDelete);
            }
             toastr.info(`Đã xóa ảnh ${file.name} khỏi giao diện.`);
        });

        wrapper.querySelector('.btn-ckfinder-browse').addEventListener('click', function() {
             openCkfinderForDropzone(myDropzone, hiddenInput, true);
        });
    }

    function openCkfinderForDropzone(dropzoneInstance, hiddenInput, isMultiple) {
        CKFinder.popup({
            chooseFiles: true,
            width: 800,
            height: 600,
            onInit: function (finder) {
                finder.on('files:choose', function (evt) {
                    const files = evt.data.files.toArray();
                    if (!isMultiple) {
                        dropzoneInstance.removeAllFiles();
                    }
                    files.forEach(file => {
                        const fileUrl = file.getUrl();
                        const mockFile = { name: file.get('name'), size: file.get('size'), accepted: true, serverUrl: fileUrl };

                        dropzoneInstance.emit("addedfile", mockFile);
                        dropzoneInstance.emit("thumbnail", mockFile, fileUrl);
                        dropzoneInstance.emit("complete", mockFile);
                        dropzoneInstance.files.push(mockFile);

                        if (isMultiple) {
                            let urls = [];
                            try { urls = hiddenInput.value ? JSON.parse(hiddenInput.value) : []; } catch(e) { urls = []; }
                            if (!urls.includes(fileUrl)) urls.push(fileUrl);
                            hiddenInput.value = JSON.stringify(urls);
                        } else {
                            hiddenInput.value = fileUrl;
                        }
                    });
                });
            }
        });
    }
</script>
