/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiComponent',
    'Magento_Checkout/js/model/totals',
    'SM_Checkout/js/view/global-observable',
    'mage/url',
    'mage/translate'
], function (Component, totals, globalVar, urlManager, $t) {
    'use strict';

    return Component.extend({
        isStepShipping: globalVar.isStepShipping,
        splitOrder: globalVar.splitOrder,
        isStepPreviewOrder: globalVar.isStepPreviewOrder,
        isLoading: totals.isLoading,
        getCartUrl: function () {
            return urlManager.build('checkout/cart/index');
        },
        seeDetails: function () {
            if (globalVar.showPaymentDetails()) {
                return $t('Less details');
            }
            return $t('See details');
        },

        showPaymentDetails: function () {
            globalVar.showPaymentDetails(!globalVar.showPaymentDetails());
        },

        isVirtual: function () {
            return window.checkoutConfig.is_virtual;
        }
    });
});
