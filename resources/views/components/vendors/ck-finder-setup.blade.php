<script type="text/javascript" src="{{ asset('/js/ckfinder/ckfinder.js') }}"></script>
<script>CKFinder.config({connectorPath: '/ckfinder/connector'});</script>
<script>
    function initSingleCKFinder(inputIdSelector) {
        const inputElement = document.querySelector(inputIdSelector);
        if (!inputElement) {
            console.error(`CKFinder: Input element with selector "${inputIdSelector}" not found.`);
            return;
        }

        const formGroup = inputElement.closest('.form-group');
        if (!formGroup) {
            console.error('CKFinder: Parent .form-group not found.');
            return;
        }

        const buttonElement = formGroup.querySelector('.ckfinder-button');
        const previewElement = formGroup.querySelector('.ckfinder-preview-image');

        if (!buttonElement || !previewElement) {
            console.error('CKFinder: Button or Preview element not found within the form group.');
            return;
        }

        buttonElement.addEventListener('click', function () {
            CKFinder.popup({
                chooseFiles: true,
                width: 800,
                height: 600,
                onInit: function (finder) {
                    finder.on('files:choose', function (evt) {
                        const file = evt.data.files.first();
                        const fullUrl = file.getUrl();
                        let path;
                        try {
                            const urlObj = new URL(fullUrl);
                            path = urlObj.pathname;
                        } catch (e) {
                            path = fullUrl;
                        }

                        inputElement.value = path;
                        previewElement.src = path;
                        previewElement.style.display = 'block';
                    });
                }
            });
        });
    }
</script>
