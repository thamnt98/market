define([
    'jquery',
    'Magento_Ui/js/form/element/file-uploader'
], function ($,Element) {
    'use strict';

    return Element.extend({
        /**
         * Defines initial value of the instance.
         *
         * @returns {FileUploader} Chainable.
         */
        setInitialValue: function () {
            var value = this.getInitialValue();
            var that = this;
            if (this.is_edit == 1) {
                $.ajax({
                    url: that.getImageUrl,
                    type: 'get',
                    dataType: 'json',
                    showLoader: true,
                    success: function (data) {
                        if (data.length > 0) {
                            data.forEach(myFunction);

                            function myFunction(item, index)
                            {
                                that.addFile(JSON.parse(item));
                            }
                            $('div[data-role=loader]').hide();
                        }
                    }
                });
            }

            value = value.map(this.processFile, this);

            this.initialValue = value.slice();

            this.value(value);
            this.on('value', this.onUpdate.bind(this));
            this.isUseDefault(this.disabled());

            return this;
        },


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
            if (this.value().length >= 3) {
            } else {
                file.previewType = this.getFilePreviewType(file);

                if (!file.id && file.file) {
                    file.id = Base64.mageEncode(file.file);
                }
                file.id = file.id.replace(/,/g,"");

                $("#review-form").append(
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
            if (this.value().length < 3) {
                var file     = data.files[0],
                    allowed  = this.isFileAllowed(file),
                    target   = $(e.target);
                if (!allowed.passed) {
                    this.attention();

                    // if all files in upload chain are invalid, stop callback is never called; this resolves promise
                    if (this.aggregatedErrors.length === data.originalFiles.length) {
                        this.uploaderConfig.stop();
                    }
                }
                else if ((data.files[0].size / 1024) < 1024) {
                    target.on('fileuploadsend', function (event, postData) {
                        postData.data.append('param_name', this.paramName);
                    }.bind(data));

                    target.fileupload('process', data).done(function () {
                        data.submit();
                    });
                } else {
                    this.showError("You can only choose pictures under 1 MB.");
                }
            } else {
                this.showError("You can select only 3 images.");
            }

        },

        showError : function (error_log) {
            var options = {
                type: 'popup',
                responsive: true,
                modalClass: 'pp-error',
                title: 'Error',
                buttons: [{
                    text: $.mage.__('OK'),
                    class: 'action primary',
                    click: function () {
                        $('#error-alert-content').empty();
                        this.closeModal();
                    }
                }]
            };
            $('#error-alert-content').html($.mage.__(error_log));
            $('#error-alert-modal').modal(options).modal('openModal');
        },

        attention : function () {
            var options = {
                type: 'popup',
                responsive: true,
                modalClass: 'pp-shopping-list',
                title: 'This format is not supported',
                buttons: [{
                    text: $.mage.__('Got it!'),
                    class: 'action primary',
                    click: function () {
                        $('#attention-content').empty();
                        this.closeModal();
                    }
                }]
            };
            $("#attention-content").html($.mage.__("Sorry, we can't upload this file format. Please make sure to upload JPEG, JPG, or PNG files."))
            $('#attention-modal').modal(options).modal('openModal');
        }
    });
});
