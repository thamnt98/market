/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/quote',
    'SM_Checkout/js/view/voucher'
], function ($, ko, Component, quote, voucherView) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'SM_Checkout/summary/voucher'
        },
        isDisplayed: ko.observable(),
        amount: ko.observable(0),
        initialize: function(){
            this._super();
            quote.totals.subscribe(this.getTotalsVoucher.bind(this));
            this.isDisplayed(this.isShow());
            return this;
        },
        getSymbol: function(){
            return window.checkoutConfig.symbol;
        },
        /**
         * @return {*|Boolean}
         */
        isShow: function () {
            return this.getCouponCode() != null && this.getCouponCode() != "";
        },

        /**
         * @return {*}
         */
        getCouponCode: function () {
            if (!quote.getTotals()) {
                return null;
            }
            console.log(quote.getTotals()['coupon_code']);
            return quote.getTotals()['coupon_code'];
        },

        /**
         * total
         * @param totals
         * @returns {number}
         */
        getTotalsVoucher: function (totals) {
            var self = this;
            var sum = 0;
            if (voucherView.prototype.applyList().length > 0) {
                var rules = voucherView.prototype.applyList();
                $.each(rules, function(key, item){
                    if(item.amount !== undefined){
                        var ruleAmount = item.amount;
                        if(isNaN(parseFloat(ruleAmount))){
                            ruleAmount = (ruleAmount.split(" ")).slice(-1)[0];
                            ruleAmount = parseFloat(ruleAmount);
                            sum += ruleAmount;
                        }
                    }
                });
                this.isDisplayed(true);
            }else{
                this.isDisplayed(false);
            }
            this.amount(sum.toFixed(2));
        },
    });
});
