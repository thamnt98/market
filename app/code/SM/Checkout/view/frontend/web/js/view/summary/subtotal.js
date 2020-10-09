/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/quote',
    'SM_Checkout/js/view/global-observable',
    'SM_Checkout/js/view/cart-items/current-items'
], function ($, Component, quote, globalVar, currentItemsData) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'SM_Checkout/summary/subtotal'
        },

        /**
         * Get pure value.
         *
         * @return {*}
         */
        getPureValue: function () {
            var totals = quote.getTotals()();

            if (totals) {
                return totals.subtotal;
            }
            return quote.subtotal;
        },

        /**
         * @return {*|String}
         */
        getValue: function () {
            return this.getFormattedPrice(this.getPureValue());
        },
        getCountItems: function(){
            let count = currentItemsData.getCountItems()();
            if (count <= 1) {
                return  ' (' + count + ' item)';
            } else {
                return  ' (' + count + ' items)';
            }
        },

        isShow: function () {
            let currentUrl = window.checkoutConfig.currentUrl;

            if (currentUrl == "transcheckout_digitalproduct_index") {
                return true;
            }
            if (!globalVar.isStepPayment()) {
                return true;
            } else {
                if (globalVar.showPaymentDetails()) {
                    return true;
                }
            }
            return false;
        },

        isShowItemsCount: function () {
            return (globalVar.isStepPayment()) ? false : true;
        }
    });
});
