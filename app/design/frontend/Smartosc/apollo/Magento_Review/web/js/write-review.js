define(
    [
        'jquery',
        'Magento_Ui/js/modal/modal',
        'mage/translate'
    ],
    function ($, modal) {
        "use strict";

        $('.review-form').on('change', '#gallery-photo-add', function() {

            if (this.files) {
                var filesAmount = this.files.length;
                var filesSize = $('#fileSizeUsed').val() * 1;
                if(filesAmount > 3) {
                    alert($.mage.__('You can only upload maximum 3 photos'));
                    return;
                }
                for (var i = 0; i < filesAmount; i++) {
                    filesSize = filesSize + (this.files[i].size / 1000000);
                }
                if(filesSize > 3){
                    alert($.mage.__('You can only upload maximum 10MB'));
                    return;
                } else {
                    $('#fileSizeUsed').val(filesSize);
                }
                for (var i = 0; i < filesAmount; i++) {
                    var reader = new FileReader();

                    reader.onload = function(event) {
                        $($.parseHTML('<img>')).attr('src', event.target.result).appendTo('div.gallery');
                    };

                    reader.readAsDataURL(this.files[i]);
                }
            }
        });
    }
);

