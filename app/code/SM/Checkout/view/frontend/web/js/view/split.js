/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'jquery',
    'ko'
], function ($, ko) {
    'use strict';

    var splitOrder = ko.observableArray([]);

    return {

        setPreviewOrder: function (data) {
            splitOrder.removeAll();
            $.each(data, function( index, value ) {
                splitOrder.push(value);
            });
        },

        getPreviewOrder: function () {
            return splitOrder;
        }
    };
});
