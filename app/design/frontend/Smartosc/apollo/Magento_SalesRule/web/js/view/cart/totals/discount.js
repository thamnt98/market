/**
 * SMCommerce
 *
 * @category  Magento
 * @package   Magento_SalesRule
 *
 * Date: June, 19 2020
 * Time: 3:54 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */
define([
    'Magento_SalesRule/js/view/summary/discount',
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Amasty_Conditions/js/action/recollect-totals'
], function (Component, $, quote, recollect) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Magento_SalesRule/cart/totals/discount',
            rules: false,
            cartSelector: '.cart-summary tr.totals-discount .discount'
        },

        initObservable: function () {
            this._super();
            this.observe(['rules']);

            return this;
        },

        initialize: function () {
            this._super();
            this.initCollapseBreakdown();
            this.rules(this.getRules());
            quote.totals.subscribe(this.getDiscountDataFromTotals.bind(this));
            recollect(true);
        },

        /**
         * @override
         *
         * @returns {Boolean}
         */
        isDisplayed: function () {
            return this.getPureValue() != 0;
        },

        /**
         * getRules
         */
        getRules: function () {
            return this.amount.length ? this.amount : '';
        },

        /**
         * @override
         *
         * @returns {Boolean}
         */
        initCollapseBreakdown: function () {
            $(document).on('click', this.cartSelector, this.collapseBreakdown);
        },

        collapseBreakdown: function () {
            $('.total-rules').toggle();
            $(this).find('.title').toggleClass('-collapsed');
        },

        showDiscountArrow: function () {
            $('.totals .discount .title').addClass('-enabled');
        },

        /**
         * @param {Array} totals
         */
        getDiscountDataFromTotals: function (totals) {
            if (totals['extension_attributes'] && totals['extension_attributes']['amrule_discount_breakdown']) {
                this.rules(totals['extension_attributes']['amrule_discount_breakdown']);
            } else {
                this.rules(null);
            }
        }
    });
});