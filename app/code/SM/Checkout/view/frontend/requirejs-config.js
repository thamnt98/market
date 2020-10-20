/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    config: {
        mixins: {
            'Trans_Sprint/js/view/summary/service-fee': {
                'SM_Checkout/js/view/summary/service-fee-mixin': true
            }
        }
    },
    map: {
        '*': {
            dateTimePicker: 'SM_Checkout/js/lib/daterangepicker',
            updateCartForm: 'SM_Checkout/js/cart/update',
            'Magento_Checkout/js/view/summary/cart-items': 'SM_Checkout/js/view/cart-items/cart-items-mixin',
            'Magento_Checkout/js/model/totals': 'SM_Checkout/js/model/totals'
        }
    },
    shim: {
        dateTimePicker: {
            deps: ['jquery']
        }
    }
};
