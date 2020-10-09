/**
 * SMCommerce
 *
 * @category    SM
 * @package     SM_Checkout
 *
 * Date: June, 20 2020
 * Time: 2:59 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */
define([
    'Amasty_Rules/js/view/cart/totals/discount-breakdown',
    'jquery',
    'Magento_Checkout/js/model/quote'
], function (Component, $, quote) {
    'use strict';

    return Component.extend({
        initCollapseBreakdown: function () {},

        getDiscountDataFromTotals: function (totals) {
            this._super();

            let voucherTotal = 0,
                customRules = [];

            $.each(this.rules(), function (key, rule) {
                if (rule.code) {
                    voucherTotal += parseFloat(rule['rule_amount']);
                } else {
                    customRules.push(rule);
                }
            });

            if (voucherTotal !== 0) {
                customRules.push({rule_name: 'Voucher', rule_amount: voucherTotal});
            }

            this.rules(customRules);
        },

        getRules: function () {
            let config = window.checkoutConfig;

            if (config && config['currentUrl'] && config['currentUrl'] === 'transcheckout_digitalproduct_index') {
                let vouchers = [];

                if (config['quoteData'] && config['quoteData']['voucher_detail']) {
                    vouchers = JSON.parse(config['quoteData']['voucher_detail']);
                }

                return vouchers;
            } else {
                return this._super();
            }
        },
        isDigital: function (){
            let currentUrl = window.checkoutConfig.currentUrl;
            return currentUrl == "transcheckout_digitalproduct_index";
        },
        getAmountRule: function (amount){
            console.log(amount);
            console.log(this.isDigital());
            if (amount == 0 && this.isDigital()) {
                return '-';
            }
            return this.getFormattedPrice(amount);
        }
    });
});
