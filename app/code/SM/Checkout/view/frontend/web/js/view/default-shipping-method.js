/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'mage/translate'
], function (
    $,
    ko
) {
    'use strict';
    return {
        shippingMethodList: ko.observableArray([
            {label: $.mage.__('Regular (2-7 days)'), value: 'transshipping_transshipping1'},
            {label: $.mage.__('Same day (3 hours)'), value: 'transshipping_transshipping2'},
            {label: $.mage.__('Scheduling'), value: 'transshipping_transshipping3'},
            {label: $.mage.__('Next day (1 day)'), value: 'transshipping_transshipping4'},
        ])
    };
});
