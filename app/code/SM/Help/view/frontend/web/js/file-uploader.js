define([
    'jquery',
    'Magento_Ui/js/form/element/file-uploader'
], function ($,Element) {
    'use strict';

    return Element.extend({
        /**
         * Adds provided file to the files list.
         *
         * @param {Object} file
         * @returns {FileUploader} Chainable.
         */
        addFile: function (file) {
            file = this.processFile(file);

            this.isMultipleFiles ?
                this.value.push(file) :
                this.value([file]);

            return this;
        },

        /**
         * Removes provided file from thes files list.
         *
         * @param {Object} file
         * @returns {FileUploader} Chainable.
         */
        removeFile: function (file) {
            $("#img-" + file.id).remove();
            this.value.remove(file);
            return this;
        },

        /**
         * May perform modifications on the provided
         * file object before adding it to the files list.
         *
         * @param {Object} file
         * @returns {Object} Modified file object.
         */
        processFile: function (file) {
            if (this.value().length >= 5) {
            } else {
                file.previewType = this.getFilePreviewType(file);

                if (!file.id && file.file) {
                    file.id = Base64.mageEncode(file.file);
                }
                file.id = file.id.replace(/,/g,"");

                $("#help-contact-us").append(
                    '<input id="img-' + file.id + '" type="hidden" name="images[]" value=\'' + JSON.stringify(file) + '\' />'
                ).trigger("contentUpdated");

                this.observe.call(file, true, [
                    'previewWidth',
                    'previewHeight'
                ]);

                return file;
            }

        },

        /**
         * Handler which is invoked prior to the start of a file upload.
         *
         * @param {Event} e - Event object.
         * @param {Object} data - File data that will be uploaded.
         */
        onBeforeFileUpload: function (e, data) {
            if (this.value().length < 5) {
                if ((data.files[0].size / 10240) < 10240) {
                    var file     = data.files[0],
                        allowed  = this.isFileAllowed(file),
                        target   = $(e.target);

                    if (this.disabled()) {
                        this.notifyError($t('The file upload field is disabled.'));

                        return;
                    }

                    if (allowed.passed) {
                        target.on('fileuploadsend', function (event, postData) {
                            postData.data.append('param_name', this.paramName);
                        }.bind(data));

                        target.fileupload('process', data).done(function () {
                            data.submit();
                        });
                    } else {
                        this.aggregateError(file.name, allowed.message);

                        // if all files in upload chain are invalid, stop callback is never called; this resolves promise
                        if (this.aggregatedErrors.length === data.originalFiles.length) {
                            this.uploaderConfig.stop();
                        }
                    }
                } else {
                    this.showError("You can only choose pictures under 10 MB.");
                }
            } else {
                this.showError("You can select only 5 images.");
            }

        },
    });
});
