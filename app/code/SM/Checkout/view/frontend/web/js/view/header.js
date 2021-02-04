/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'uiComponent',
    'SM_Checkout/js/view/global-observable',
    'mage/translate'
], function ($, Component, globalVar, $t) {
    'use strict';

    return Component.extend({
        isStepPreviewOrder: globalVar.isStepPreviewOrder,
        getPageTitle: function () {
            var message = $t('Check Out');
            if (globalVar.isStepShipping()) {
                message = $t('Check Out');
            } else if (globalVar.isStepPreviewOrder()) {
                message = $t('Order Summary');
            } else {
                message = $t('Payment');
            }
            return message;
        },

        backToCheckout: function () {
            globalVar.isStepPreviewOrder(false);
            globalVar.isStepShipping(true);
            globalVar.showPaymentDetails(false);
            $(".breadcrumbs ul.items li").last().remove();
            $(".breadcrumbs ul.items li.Out").html($("<strong/>", {
                "text": $.mage.__("Check Out")
            }));
        }
    });
});
