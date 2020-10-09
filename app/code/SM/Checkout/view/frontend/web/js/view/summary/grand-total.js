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
            template: 'SM_Checkout/summary/grand-total'
        },

        /**
         * @return {*}
         */
        isDisplayed: function () {
            return this.isFullMode();
        },

        /**
         * Get pure value.
         */
        getPureValue: function () {
            var totals = quote.getTotals()();

            if (totals) {
                return totals['grand_total'];
            }

            return quote['grand_total'];
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
        isShowItemsCount: function () {
            return (globalVar.isStepPayment()) ? false : true;
        }
    });
});
