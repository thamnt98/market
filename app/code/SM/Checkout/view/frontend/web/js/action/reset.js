define(
    [
        'jquery',
        'mage/url',
        'Magento_Checkout/js/model/error-processor',
        'mage/storage',
        'Magento_Checkout/js/model/full-screen-loader',
        'SM_Checkout/js/view/global-observable',
        'SM_Checkout/js/view/split'
    ],
    function ($, urlManager, errorProcessor, storage, fullScreenLoader, globalVar, split) {
        'use strict';

        return function () {
            var url = urlManager.build('rest/V1/trans-checkout/resetPaymentFail');
            fullScreenLoader.startLoader();
            return storage.post(
                url,
                {},
                false
            ).done(
                function (response) {
                    fullScreenLoader.stopLoader();
                    split.setPreviewOrder(response.order);
                }
            ).fail(
                function (response) {
                    fullScreenLoader.stopLoader();
                }
            );
        };
    }
);
