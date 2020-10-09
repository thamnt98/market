define(
    [
        'jquery',
        'mage/url',
        'Magento_Checkout/js/model/error-processor',
        'mage/storage',
        'Magento_Checkout/js/model/full-screen-loader'
    ],
    function ($, urlManager, errorProcessor, storage, fullScreenLoader) {
        'use strict';

        return function (data) {
            var url = urlManager.build('rest/V1/trans-checkout/me/estimateShippingMethod');
            fullScreenLoader.startLoader();
            return storage.post(
                url,
                data,
                false
            ).done(
                function (response) {
                    fullScreenLoader.stopLoader();
                }
            ).fail(
                function (response) {
                    fullScreenLoader.stopLoader();
                }
            );
        };
    }
);
