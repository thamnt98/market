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

    let currentItems = ko.observableArray([]),
        countItems = ko.observable(0),
        currentItemsListId = ko.observable(window.checkoutConfig.currentItemsListId),
        currentItemsData = {};

    return {

        setCurrentItemsListId: function (listId) {
            currentItemsListId(listId);
        },

        getCurrentItemsListId: function () {
            return currentItemsListId;
        },

        setCurrentItems: function (itemId) {
            currentItems(itemId);
        },

        getCurrentItems: function () {
            return currentItems;
        },

        setCountItems: function (count) {
            countItems(count);
        },

        getCountItems: function () {
            return countItems;
        },

        setCurrentItemsData: function (itemId, qty, rowTotal) {
            if (typeof currentItemsData[itemId] === "undefined") {
                currentItemsData[itemId] = ko.observable({'qty': qty, 'row_total': rowTotal});
            } else {
                currentItemsData[itemId]({'qty': qty, 'row_total': rowTotal});
            }
        },

        getCurrentItemsData: function (itemId) {
            return currentItemsData[itemId];
        }
    };
});
