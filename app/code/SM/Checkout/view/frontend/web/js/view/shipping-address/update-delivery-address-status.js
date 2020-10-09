/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'jquery',
    'ko',
], function ($, ko) {
    'use strict';

    var status = ko.observable(),
        preSelectAddress = window.checkoutConfig.pre_select_address,
        orderSelectAddressList = ko.observableArray([]);
    $.each(preSelectAddress, function(index, addressId) {
        orderSelectAddressList.push(addressId.toString());
    });

    let mod = {};

    mod.setStatus = function (value) {
        status(value);
    };

    mod.getStatus = function () {
        return status;
    };

    mod.setOrderSelectAddressList = function (oldId, newId) {
        if (oldId === -1 && orderSelectAddressList.indexOf(newId) === -1) {
            orderSelectAddressList.push(newId);
        } else {
            orderSelectAddressList.replace(oldId, newId);
        }
    };

    mod.getOrderSelectAddressList = function () {
        return orderSelectAddressList;
    };

    return mod;
});
