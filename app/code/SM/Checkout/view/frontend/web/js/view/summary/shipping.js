/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/quote',
    'Magento_SalesRule/js/view/summary/discount',
    'SM_Checkout/js/view/global-observable',
    'SM_Checkout/js/view/split',
    'mage/translate'
], function ($, ko, Component, quote, discountView, globalVar, split, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'SM_Checkout/summary/shipping'
        },
        show: ko.observable(false),
        previewOrderData: split.getPreviewOrder(),
        quoteIsVirtual: quote.isVirtual(),
        totals: quote.getTotals(),

        initialize: function () {
            this._super();
            this.show = ko.computed(function() {
                return (!globalVar.isStepShipping() && globalVar.showPaymentDetails()) ? true : false;
            }, this);
            return this;
        },

        /**
         * @return {*}
         */
        getShippingMethodTitle: function () {
            var shippingMethod = '',
                shippingMethodTitle = '';

            if (!this.isCalculated()) {
                return '';
            }
            shippingMethod = quote.shippingMethod();
            console.log(shippingMethod);
            if(shippingMethod == null ){
                return '';
            }
            if (typeof shippingMethod['method_title'] !== 'undefined') {
                shippingMethodTitle = ' - ' + shippingMethod['method_title'];
            }

            return shippingMethod ?
                shippingMethod['carrier_title'] + shippingMethodTitle :
                shippingMethod['carrier_title'];
        },

        /**
         * @return {*|Boolean}
         */
        isCalculated: function () {
            return true;
            //return this.totals() && this.isFullMode() && quote.shippingMethod() != null; //eslint-disable-line eqeqeq
        },

        /**
         * @return {*}
         */
        getValue: function () {
            var price;

            if (!this.isCalculated()) {
                return this.notCalculatedMessage;
            }
            price =  this.totals()['shipping_amount'];

            return this.getFormattedPrice(price);
        },

        getOldValue: function () {
            if (!this.isCalculated()) {
                return '';
            }

            let extData = this.totals()['extension_attributes'],
                price = 0;

            if (extData && extData['free_shipping_discount']) {
                price = parseFloat(extData['free_shipping_discount']) + parseFloat(this.totals()['shipping_amount']);

                return this.getFormattedPrice(price);
            }

            return '';
        },

        /**
         * If is set coupon code, but there wasn't displayed discount view.
         *
         * @return {Boolean}
         */
        haveToShowCoupon: function () {
            var couponCode = this.totals()['coupon_code'];

            if (typeof couponCode === 'undefined') {
                couponCode = false;
            }

            return couponCode && !discountView().isDisplayed();
        },

        /**
         * Returns coupon code description.
         *
         * @return {String}
         */
        getCouponDescription: function () {
            if (!this.haveToShowCoupon()) {
                return '';
            }

            return '(' + this.totals()['coupon_code'] + ')';
        },

        getTitle: function (sort) {
            if (globalVar.isStepPayment()) {
                return $t("Delivery Fee %1").replace('%1', sort);
            } else {
                return $t("Delivery Fee #%1").replace('%1', sort);
            }

        },

        isDeliveryMethod: function (type) {
            return (type == 1) ? true : false;
        },

        getDeliveryFee: function (price, includeSymbol = false) {
            if (price == 0 && !includeSymbol) {
                return price;
            }
            return this.getFormattedPrice(price);
        },

        getDeliverySubTotalTitle: function () {
            if (globalVar.isStepPreviewOrder() && globalVar.showPaymentDetails()) {
                return $t('Subtotal Delivery Fee');
            }

            return $t('Delivery Fee');
        },

        showSubtotalDeliveryFee: function () {
            return !globalVar.isStepPayment();
        }
    });
});
