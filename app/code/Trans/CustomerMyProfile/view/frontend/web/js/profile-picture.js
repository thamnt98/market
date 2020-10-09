/**
 * @category Trans
 * @package  Trans_CustomerMyProfile
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Dwi Septha Kurniawan <septha.kurniawan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

require([
        'jquery',
        'jquery/ui',
        'jquery/validate',
        'mage/translate'
    ], function ($) {

    $('#form-validate-picture input[name=profile_picture]').on('change', function() {
        $('#form-validate-picture').submit();
    });

    //validate picture type
    $.validator.addMethod(
        'validate-picture-type', function (v, elm) {
        var extensions = ['jpeg', 'jpg', 'png', 'gif', 'JPG', 'JPEG', 'PNG', 'GIF'];
            if (!v) {
                return true;
            }
            with (elm) {
                var ext = value.substring(value.lastIndexOf('.') + 1);
                for (i = 0; i < extensions.length; i++) {
                    if (ext == extensions[i]) {
                        return true;
                    }
                }
            }
            return false;
        }, $.mage.__('Picture invalid (Please use format .jpeg .jpg .png .gif).')
    );

    //validate picture size
    $.validator.addMethod(
        'validate-picture-size', function (v, elm) {
            var maxSize = 1 * 1048576;

            if (window.getMaxsizePicture){
                maxSize = window.getMaxsizePicture * 1048576;
            }

            if (navigator.appName == "Microsoft Internet Explorer") {
                if (elm.value) {
                    var oas = new ActiveXObject("Scripting.FileSystemObject");
                    var e = oas.getFile(elm.value);
                    var size = e.size;
                }
            } else {
                if (elm.files[0] != undefined) {
                    size = elm.files[0].size;
                }
            }
            if (size != undefined && size > maxSize) {
                return false;
            }
            return true;
        }, $.mage.__('Picture size too large (max. '+window.getMaxsizePicture+'MB).')
    );
});
