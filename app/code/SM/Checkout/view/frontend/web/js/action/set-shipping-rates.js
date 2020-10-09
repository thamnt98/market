define(
    [
        'jquery',
        'mage/url',
        'Magento_Checkout/js/model/error-processor',
        'mage/storage',
        'Magento_Checkout/js/model/full-screen-loader',
        'SM_Checkout/js/view/shipping-address/current-pickup',
        'Magento_Checkout/js/action/get-totals'
    ],
    function ($, urlManager, errorProcessor, storage, fullScreenLoader, pickup, getTotalsAction) {
        'use strict';

        var timer;
        let mod = {};

        mod.setShippingMethod = function (shippingMethodSelectList, disableDeliveryList, defaultBillingId, itemsData) {
            clearTimeout(timer);
            timer = setTimeout(function () {
                fullScreenLoader.startLoader();

                var rates = {};
                $.each(shippingMethodSelectList, function( id, rateSelected ) {
                    if (disableDeliveryList[id]()) {
                        rates[id] = {type: 'store_pickup_store_pickup', 'store': pickup.currentPickupId()};
                    } else {
                        rates[id] = {type: rateSelected()};
                    }

                });
                mod.setShippingMethodAction(JSON.stringify({'rates': rates, 'billing' : defaultBillingId(), 'items': itemsData}));

            }, 200);

        };

        mod.setShippingMethodAction = function (data) {
            fullScreenLoader.startLoader();
            return storage.post(
                urlManager.build('rest/V1/trans-checkout/me/shippingInformation'),
                data,
                false
            ).done(
                function (response) {
                    fullScreenLoader.stopLoader();
                    getTotalsAction([], $.Deferred());
                }
            ).fail(
                function (response) {
                    fullScreenLoader.stopLoader();
                }
            );
        };

        mod.refreshTotal = function () {
            getTotalsAction([], $.Deferred());
        };
        return mod;
    }
);
