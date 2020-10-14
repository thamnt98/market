/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

/**
 * Customer store credit(balance) application
 */
define([
    'ko',
    'jquery',
    'Magento_Checkout/js/model/quote',
    'mage/url',
    'Magento_Checkout/js/model/error-processor',
    'Magento_SalesRule/js/model/payment/discount-messages',
    'mage/storage',
    'mage/translate',
    'Magento_Checkout/js/action/get-payment-information',
    'Magento_Checkout/js/model/totals',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/action/recollect-shipping-rates'
], function (ko, $, quote, urlBuilder, errorProcessor, messageContainer, storage, $t, getPaymentInformationAction,
    totals, fullScreenLoader, recollectShippingRates
) {
    'use strict';

    var dataModifiers = [],
        successCallbacks = [],
        failCallbacks = [],
        action;

    /**
     * Apply provided coupon.
     *
     * @param {String} couponCode
     * @param {Boolean}isApplied
     * @returns {Deferred}
     */
    action = function (serviceFeeValue) {
        var quoteId = quote.getQuoteId(),
            url = urlBuilder.build('rest/V1/payment/apply/service-fee', {}),
            data = {cartId: quoteId, serviceFeeValue : serviceFeeValue};
            
        fullScreenLoader.startLoader();

        return storage.post(
            url,
            JSON.stringify(data),
            false,
            null
        ).done(function (response) {
            var deferred;

            if (response) {
                deferred = $.Deferred();

                totals.isLoading(true);
                // recollectShippingRates();
                getPaymentInformationAction(deferred);
                $.when(deferred).done(function () {
                    fullScreenLoader.stopLoader();
                    totals.isLoading(false);
                });
                
                //Allowing to tap into apply-coupon process.
                successCallbacks.forEach(function (callback) {
                    callback(response);
                });
            }
        }).fail(function (response) {
            fullScreenLoader.stopLoader();
            totals.isLoading(false);
            errorProcessor.process(response, messageContainer);
            //Allowing to tap into apply-coupon process.
            failCallbacks.forEach(function (callback) {
                callback(response);
            });
        });
    };

    /**
     * When successfully added a coupon.
     *
     * @param {Function} callback
     */
    action.registerSuccessCallback = function (callback) {
        successCallbacks.push(callback);
    };

    /**
     * When failed to add a coupon.
     *
     * @param {Function} callback
     */
    action.registerFailCallback = function (callback) {
        failCallbacks.push(callback);
    };

    return action;
});
