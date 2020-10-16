/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'SM_Checkout/js/view/global-observable'
], function (globalVar) {
    'use strict';

    var mixin = {
        defaults: {
            template: 'SM_Checkout/summary/service-fee'
        },
        isShow: function () {
            return globalVar.isStepPayment() && globalVar.showPaymentDetails();
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});
