/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/quote'
], function (Component, quote) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'SM_Checkout/summary/discount'
        },
        totals: quote.getTotals(),

        /**
         * @return {*|Boolean}
         */
        isDisplayed: function () {
            return this.isFullMode() && this.getPureValue() != 0; //eslint-disable-line eqeqeq
        },

        /**
         * @return {*}
         */
        getCouponCode: function () {
            if (!this.totals()) {
                return null;
            }

            return this.totals()['coupon_code'];
        },

        /**
         * @return {*}
         */
        getCouponLabel: function () {
            if (!this.totals()) {
                return null;
            }

            return this.totals()['coupon_label'];
        },

        /**
         * Get discount title
         *
         * @returns {null|String}
         */
        getTitle: function () {
            var discountSegments;

            if (!this.totals()) {
                return null;
            }

            discountSegments = this.totals()['total_segments'].filter(function (segment) {
                return segment.code.indexOf('discount') !== -1;
            });

            return discountSegments.length ? discountSegments[0].title : null;
        },

        /**
         * @return {Number}
         */
        getPureValue: function () {
            if (!this.totals()) {
                return 0;
            }

            let price = 0,
                extData = this.totals()['extension_attributes'];

            if (this.totals()['discount_amount']) {
                price = Math.abs(parseFloat(this.totals()['discount_amount']));
            }

            if (extData && extData['free_shipping_discount']) {
                price += Math.abs(parseFloat(extData['free_shipping_discount']));
            }

            return Math.abs(price);
        },

        /**
         * @return {*|String}
         */
        getValue: function () {
            return this.getFormattedPrice(this.getPureValue());
        }
    });
});
