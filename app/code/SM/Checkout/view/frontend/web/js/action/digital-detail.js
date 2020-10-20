define(
    [
        'jquery',
        'mage/url',
        'Magento_Checkout/js/model/error-processor',
        'mage/storage',
        'Magento_Checkout/js/model/full-screen-loader',
        'SM_Checkout/js/view/summary/digital'
    ],
    function ($, urlManager, errorProcessor, storage, fullScreenLoader, digital) {
        'use strict';

        return function () {
            var url = urlManager.build('rest/V1/trans-checkout/digitalDetail');
            fullScreenLoader.startLoader();
            return storage.post(
                url,
                {},
                false
            ).done(
                function (response) {
                    fullScreenLoader.stopLoader();
                    $.each(response, function( index, value ) {
                        digital.digitalData.push(value);
                    });
                }
            ).fail(
                function (response) {
                    fullScreenLoader.stopLoader();
                }
            );
        };
    }
);
