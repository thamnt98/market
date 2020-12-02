define(
    [
        'jquery',
        'Magento_Ui/js/modal/modal',
        'mage/url'
    ], function ($, modal, urlBuilder) {
        'use strict';

        let mod = {};

        mod.init = function () {
            let urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('recoverytoken')) {
                mod.callAjax('lock-reset-form');
            }
        };

        mod.callAjax = function (type) {
            $.ajax({
                url: urlBuilder.build("customer/popup/index"),
                type: 'POST',
                dataType: 'json',
                data: {'type': type, 'otp': false},
                showLoader: true,
                async: true,
                success: function(response) {
                    $('body').append(response.html).trigger('contentUpdated');
                }
            });
        };

        return mod.init();
    }
);
