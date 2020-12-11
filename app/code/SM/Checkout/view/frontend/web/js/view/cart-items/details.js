/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'Magento_Checkout/js/view/summary/item/details',
    'SM_Checkout/js/view/cart-items/current-items',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils',
    'Magento_Customer/js/customer-data'
], function ($, ko, Component, currentItemsData, quote, priceUtils, customerData) {
    'use strict';

    var imageData = window.checkoutConfig.imageData,
        weightUnit = window.checkoutConfig.weightUnit,
        updateMiniCart = ko.observable(false);
    return Component.extend({

        /**
         * @param int item
         * @return string
         */
        getProductUrl: function (item_id) {
            if (imageData[item_id]) {
                return imageData[item_id].url;
            }

            return '#';
        },

        /**
         * @param int item
         * @return string
         */
        getWeight: function (item_id, qty) {
            if (imageData[item_id]) {
                let currentItem = currentItemsData.getCurrentItemsData(item_id),
                    weight = imageData[item_id].weight;
                if (typeof currentItem !== "undefined") {
                    if (parseInt(currentItem().qty) != parseInt(qty)) {
                        weight = (parseInt(imageData[item_id].weight)/parseInt(qty)) * parseInt(currentItem().qty);
                    }
                }
                return '(' + weight + ' ' + weightUnit +')';
            }

            return '';
        },

        getType: function (item_id) {
            if (imageData[item_id]) {
                return imageData[item_id].product_type;
            }

            return '';
        },

        getQty: function (item_id, qty) {
            let currentItem = currentItemsData.getCurrentItemsData(item_id);
            if (typeof currentItem !== "undefined") {
                if (parseInt(currentItem().qty) != parseInt(qty)) {
                    return currentItem().qty;
                }
            }
            return qty;
        },

        isDisable: function (item_id) {
            let currentItems = currentItemsData.getCurrentItems();
            if (currentItems.indexOf(item_id.toString()) === -1) {
                if (!updateMiniCart()) {
                    customerData.invalidate(['cart']);
                    updateMiniCart(true);
                }
                return true;
            }
            return false;
        },

        isDownStock: function (item_id, qty) {
            let currentItem = currentItemsData.getCurrentItemsData(item_id);
            if (typeof currentItem !== "undefined") {
                if (parseInt(currentItem().qty) != parseInt(qty)) {
                    if (!updateMiniCart()) {
                        customerData.invalidate(['cart']);
                        updateMiniCart(true);
                    }
                    updateMiniCart(true);
                    return true;
                }
            }
            return false;
        },

        isFreshProduct: function (item_id) {
            if (imageData[item_id]) {
                if (imageData[item_id].is_fresh == 1) {
                    return true
                }
            }
            return false;
        },

        isPriceInKg: function (item_id) {
            if (imageData[item_id]) {
                if (imageData[item_id].price_in_kg == 1) {
                    return true
                }
            }
            return false;
        },

        getTotalWeight:  function (qty, weight) {
            return '(' + (qty * weight) + ' ' + 'gram)';
        },

        getFreshWeight: function (item_id) {
            if (imageData[item_id]) {
                return imageData[item_id].fresh_weight;
            }
            return 0;
        },

        getLabelFreshWeight: function (item_id) {
            if (imageData[item_id]) {
                let weight = imageData[item_id].fresh_weight;
                return weight + ' ' + 'gram';
            }
            return '';
        },

        getSoldIn: function (item_id) {
            if (imageData[item_id]) {
                if (imageData[item_id].sold_in) {
                    return imageData[item_id].sold_in;
                }
            }
            return '';
        },

        getPrice: function (item_id) {
            let price = '';
            if (imageData[item_id]) {
                    if (this.getFreshPromoPrice(item_id) && this.getFreshPromoPrice(item_id) > 0) {
                        price = this.getFreshPromoPrice(item_id);
                    } else {
                        price = this.getFreshBasePrice(item_id);
                    }
            }
            return price;
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
            return (this.getFreshPromoPrice(item_id) < this.getFreshBasePrice(item_id))
                && (this.getFreshPromoPrice(item_id) > 0);

        },

        getFormattedPrice: function (price) {
            return priceUtils.formatPrice(price, quote.getPriceFormat()) + "/kg";
        },

        getItemWeight: function (item_id) {
            if (imageData[item_id]) {
                let weight = imageData[item_id].weight;
                return '(' + weight + ' ' + 'gram)';
            }
            return '';
        }

    });
});
