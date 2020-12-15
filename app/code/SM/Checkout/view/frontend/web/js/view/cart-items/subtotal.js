/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Checkout/js/view/summary/item/details/subtotal',
    'SM_Checkout/js/view/cart-items/current-items'
], function ($, viewModel, currentItemsData) {
    'use strict';
    let imageData = window.checkoutConfig.imageData,
        basePrice = {};
    return viewModel.extend({
        defaults: {
            displayArea: 'after_details',
            template: 'SM_Checkout/view/cart-items/subtotal'
        },

        /**
         * @param {Object} quoteItem
         * @return {*|String}
         */
        getValue: function (quoteItem) {
            let currentItem = currentItemsData.getCurrentItemsData(quoteItem.item_id);
            if (typeof currentItem !== "undefined") {
                if (parseInt(currentItem().row_total) != parseInt(quoteItem.row_total)) {
                    return this.getFormattedPrice(currentItem().row_total);
                }
            }
            return this.getFormattedPrice(quoteItem.row_total);
        },

        /**
         * @param {Object} quoteItem
         * @returns {boolean}
         */
        isDiscount: function (quoteItem) {
            let basePriceByLocation = this.getBasePriceByLocation(quoteItem['item_id']);
            if (parseInt(quoteItem['price']) != parseInt(basePriceByLocation) && parseInt(basePriceByLocation) != 0) {
                basePrice[quoteItem['item_id']] = parseInt(basePriceByLocation);
                return true;
            }
            basePrice[quoteItem['item_id']] = 0;
            return false;
        },

        /**
         * @param {Object} quoteItem
         * @return {*|String}
         */
        getBaseSubTotal: function (quoteItem) {
            return this.getFormattedPrice(basePrice[quoteItem['item_id']] * quoteItem['qty']);
        },

        getBasePriceByLocation: function (itemId) {
            let price = 0,
                items  = window.checkoutConfig.quoteItemData;
            $.each(items, function (key, data) {
                if (data.item_id == itemId) {
                    price = data.regular_price;
                    return false;
                }
            });

            return price;
        },

        isFreshProduct: function (item_id) {
            if (imageData[item_id]) {
                if (imageData[item_id].is_fresh == 1) {
                    return true
                }
            }
            return false;
        },

        getFreshPromoPrice: function (item_id) {
            if (imageData[item_id]) {
                return imageData[item_id].promo_price_in_kg;
            }
            return 0;
        },

        getFreshBasePrice: function (item_id) {
            if (imageData[item_id]) {
                return imageData[item_id].base_price_in_kg;
            }
            return 0;
        },

        isShowPromoPrice: function (item_id) {
            if (this.isFreshProduct(item_id)) {
                if ((this.getFreshPromoPrice(item_id) < this.getFreshBasePrice(item_id))
                    && (this.getFreshPromoPrice(item_id) > 0)) {
                    return true;
                }
            }
            return false;
        }
    });
});
