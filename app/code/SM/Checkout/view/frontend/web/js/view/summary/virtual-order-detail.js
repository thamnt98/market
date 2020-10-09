/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiComponent',
    'SM_Checkout/js/view/summary/digital'
], function (Component, digital) {
    'use strict';

    return Component.extend({
        digitalData: digital.digitalData
    });
});
