/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define(['Magento_Checkout/js/view/summary/item/details/thumbnail'], function (Component) {
    'use strict';

    var imageData = window.checkoutConfig.imageData;

    return Component.extend({
        imageData: imageData,

        /**
         * @param {Object} item
         * @return string
         */
        getProductUrl: function (item) {
            if (this.imageData[item['item_id']]) {
                return this.imageData[item['item_id']].url;
            }

            return '#';
        }
    });
});
