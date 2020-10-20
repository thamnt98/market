/**
 * SMCommerce
 *
 * @category    Magento
 * @package     Magento_Tax
 *
 * Date: June, 23 2020
 * Time: 2:19 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

define([
    'Magento_Tax/js/view/checkout/summary/grand-total',
    'Magento_Checkout/js/model/totals'
], function (Component, totals) {
    'use strict';

    return Component.extend({
        /**
         * @override
         */
        isDisplayed: function () {
            if (this.totals()) {
                if (totals.getSegment('discount')) {
                    return true;
                }
            }
            return false;
        },

        /**
         * @override
         *
         * Update total = subtotal - discount
         */
        getValue: function () {
            let price = 0;

            if (this.totals()) {
                if (totals.getSegment('subtotal')) {
                    price += totals.getSegment('subtotal').value;
                }

                if (totals.getSegment('discount')) {
                    price += totals.getSegment('discount').value;
                }
            }

            return this.getFormattedPrice(price);
        },

        /**
         * @override
         *
         * Update total = subtotal - discount
         */
        getGrandTotalExclTax: function () {
            let total = this.totals();

            if (!total) {
                return 0;
            }

            return this.getFormattedPrice(total['subtotal'] + total['discount_amount']);
        }
    });
});
