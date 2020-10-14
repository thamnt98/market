/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*':{
            slick: 'Magento_PageBuilder/js/resource/slick/slick',
            jqueryCountdown: 'SM_Theme/js/jquery.countdown',
        }
    },
    shim: {
        'slick': {
            deps: ['jquery']
        }
    },
    config: {
        mixins: {
            'Magento_Catalog/js/product/list/columns/final-price': {
                'SM_Theme/js/product/list/columns/special-price': true
            },
            'Magento_Catalog/js/product/list/columns/price-box': {
                'SM_Theme/js/product/list/columns/price-box': true
            }
        }
    }
};
