define(
    [
        'jquery',
        'mage/url',
        'Magento_Checkout/js/model/error-processor',
        'mage/storage',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Customer/js/model/address-list',
        'Magento_Customer/js/model/customer/address'
    ],
    function ($, urlManager, errorProcessor, storage, fullScreenLoader, addressList, Address) {
        'use strict';

        return function (data) {
            var url = urlManager.build('rest/V1/customers/me/createAddress');
            fullScreenLoader.startLoader();
            return storage.post(
                url,
                data,
                false
            ).done(
                function (response) {
                    fullScreenLoader.stopLoader();
                    if (response.status) {
                        addressList.push(new Address($.parseJSON(response.result)));
                    }
                }
            ).fail(
                function (response) {
                    fullScreenLoader.stopLoader();
                }
            );
        };
    }
);
