/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'underscore',
    'ko',
    'mageUtils',
    'uiComponent',
    'uiLayout'
], function (_, ko, utils, Component, layout) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'SM_Checkout/shipping-address/attributes-address-form'
        },
        /** @inheritdoc */
        initialize: function () {
            this._super();

            return this;
        },
        /**
         * return all attributes callable
         * @returns {*}
         */
        attributes: function(){
            return window.checkoutConfig.attribute_address_form;
        },
        /**
         * return number streets address
         * @param number
         * @returns {Array}
         */
        getNumberStreets: function(number){
            var result = [];
            for(var i =0; i< number; i++)
                result.push(i);
            return result;
        },
    });
});
