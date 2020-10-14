/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

define([
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/totals'
], function (Component, quote, totals) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Trans_Sprint/summary/service-fee'
        },
        totals: quote.getTotals(),

        /**
         * @return {*|Boolean}
         */
        isDisplayed: function () {
            return this.getPureValue() !== 0; //eslint-disable-line eqeqeq
        },

        /**
         * Get discount title
         *
         * @returns {null|String}
         */
        getTitle: function () {
            return 'Service Fee';
        },

        /**
         * @return {Number}
         */
        getPureValue: function () {
            var price = 0;
            
            if (this.totals() && totals.getSegment('service_fee').value) {
                price = totals.getSegment('service_fee').value;
            }

            return price;
        },

        /**
         * @return {*|String}
         */
        getValue: function () {
            return this.getFormattedPrice(this.getPureValue());
        }
    });
});
