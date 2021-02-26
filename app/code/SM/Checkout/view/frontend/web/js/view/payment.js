/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'uiComponent',
    'ko',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/payment-service',
    'Magento_Checkout/js/model/payment/method-converter',
    'Magento_Checkout/js/action/get-payment-information',
    'Magento_Checkout/js/model/checkout-data-resolver',
    'mage/translate',
    'SM_Checkout/js/view/global-observable'
], function (
    $,
    _,
    Component,
    ko,
    quote,
    paymentService,
    methodConverter,
    getPaymentInformation,
    checkoutDataResolver,
    $t,
    globalVar
) {
    'use strict';

    /** Set payment methods to collection */
    var isPaymentMethodsAvailableGlobal = ko.observable(false);
    if (globalVar.isStepPayment()) {
        paymentService.setPaymentMethods(methodConverter(globalVar.paymentMethod()));
        if (paymentService.getAvailablePaymentMethods().length > 0) {
            isPaymentMethodsAvailableGlobal(true);
        }
    }
    globalVar.paymentMethod.subscribe(function (newValue) {;
        paymentService.setPaymentMethods(methodConverter(newValue));
        if (paymentService.getAvailablePaymentMethods().length > 0) {
            isPaymentMethodsAvailableGlobal(true);
        } else {
            isPaymentMethodsAvailableGlobal(false);
        }
    });
    return Component.extend({
        defaults: {
            template: 'SM_Checkout/payment',
            activeMethod: ''
        },
        isVisible: ko.observable(quote.isVirtual()),
        quoteIsVirtual: quote.isVirtual(),
        isPaymentMethodsAvailable: isPaymentMethodsAvailableGlobal,
        /** @inheritdoc */
        initialize: function () {
            this._super();
            checkoutDataResolver.resolvePaymentMethod();


            return this;
        },

        /**
         * Navigate method.
         */
        navigate: function () {
            var self = this;

            if (!self.hasShippingMethod()) {
                this.isVisible(false);
            } else {
                getPaymentInformation().done(function () {
                    self.isVisible(true);
                });
            }
        },

        /**
         * @return {Boolean}
         */
        hasShippingMethod: function () {
            return window.checkoutConfig.selectedShippingMethod !== null;
        },

        /**
         * @return {*}
         */
        getFormKey: function () {
            return window.checkoutConfig.formKey;
        },
        isStepPayment: globalVar.isStepPayment,
    });
});
