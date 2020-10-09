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
            if (!globalVar.isStepPayment()) {
                return true;
            } else {
                if (globalVar.showPaymentDetails()) {
                    return true;
                }
            }
            return false;
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});
