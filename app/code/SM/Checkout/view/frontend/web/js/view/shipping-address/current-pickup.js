/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'ko'
], function (ko) {
    'use strict';
    /**
     * current pickup data
     * hasCurrentStore, currentStoreName, currentStoreAddress, currentPickupId
     */
    return {
        hasCurrentStore: ko.observable(false),
        currentStoreName: ko.observable(),
        currentStoreAddress: ko.observable(),
        currentPickupId: ko.observable(),
        storePickUpDate: ko.observable(''),
        storePickUpTime: ko.observable('')
    };
});
