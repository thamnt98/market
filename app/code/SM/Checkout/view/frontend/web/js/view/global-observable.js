/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'ko'
], function (ko) {
    'use strict';
    /**
     * current step
     */
    var paymentFail = window.checkoutConfig.payment_fail,
        isVirtual = window.checkoutConfig.is_virtual;

    if (paymentFail || isVirtual) {
        var isStepShipping = ko.observable(false),
            isStepPreviewOrder = ko.observable(false),
            isStepPayment = ko.observable(true),
            paymentSelected = ko.observable(false),
            showPaymentDetails = ko.observable(false),
            disableGoPaymentButton = ko.observable(false),
            splitOrder = ko.observable(false);
    } else {
        var isStepShipping = ko.observable(true),
            isStepPreviewOrder = ko.observable(false),
            isStepPayment = ko.observable(false),
            paymentSelected = ko.observable(false),
            showPaymentDetails = ko.observable(false),
            disableGoPaymentButton = ko.observable(false),
            splitOrder = ko.observable(false);
    }
    return {
        isStepShipping: isStepShipping,
        isStepPreviewOrder: isStepPreviewOrder,
        isStepPayment: isStepPayment,
        paymentSelected: paymentSelected,
        showPaymentDetails: showPaymentDetails,
        disableGoPaymentButton: disableGoPaymentButton,
        splitOrder: splitOrder
    };
});

