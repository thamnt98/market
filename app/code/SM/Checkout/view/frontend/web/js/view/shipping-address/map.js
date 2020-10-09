/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'ko',
    'uiComponent',
    'jquery',
    'SM_Checkout/js/view/shipping-address/location',
    'SM_Checkout/js/view/shipping-address/fillout-city-district',
], function (ko, Component, $, location, fill) {
    'use strict';

    return Component.extend({
        /** @inheritdoc */
        initialize: function () {
            this._super();
            return this;
        },

        locationImg: function () {
            return window.checkoutConfig.locationImg;
        },

        renderMap: function () {
            location.loadAPIMap();
            fill.init();
        }
    });
});
