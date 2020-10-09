/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiComponent',
    'SM_Checkout/js/view/cart-items/init-shipping-type'
], function (Component, initShippingType) {
    'use strict';

    return Component.extend({
        firstBuild: function () {
            initShippingType.getShippingMethod();
        }
    });
});
