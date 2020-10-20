/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'ko'
], function (ko) {
    'use strict';

    var shippingType = ko.observable(),
        addressNotCompleteNotify = ko.observable(false);

    return {

        setValue: function (value) {
            shippingType(value);
        },

        getValue: function () {
            return shippingType;
        },

        setAddressNotCompleteNotify: function (value) {
            addressNotCompleteNotify(true);
        },

        getAddressNotCompleteNotify: function () {
            return addressNotCompleteNotify;
        }
    };
});
