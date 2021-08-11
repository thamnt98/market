/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils',
    'mage/translate',
    'SM_Checkout/js/view/global-observable',
    'SM_Checkout/js/view/split',
    'SM_Checkout/js/view/cart-items/init-shipping-type',
    'SM_Checkout/js/view/shipping-address/current-pickup',
    'SM_Checkout/js/view/cart-items/current-items'
], function ($, ko, Component, quote, priceUtils, $t, globalVar, split, shippingType, currentPickup, currentItemsData) {
    'use strict';

    var imageData = window.checkoutConfig.imageData,
        customerAddressData = shippingType.getCustomerAddressData(),
        addressNameList = shippingType.getAddressNameList(),
        itemsData = shippingType.getItemsDataList();

    return Component.extend({
        show: ko.observable(false),
        previewOrderData: split.getPreviewOrder(),
        initialize: function () {
            this._super();
            this.show = ko.computed(function() {
                return (globalVar.isStepPreviewOrder() && globalVar.showPaymentDetails()) ? true : false;
            }, this);
            return this;
        },

        getFormattedPrice: function (price, includeSymbol = false) {
            if (price == 0 && !includeSymbol) {
                return price;
            }
            return priceUtils.formatPrice(price, quote.getPriceFormat());
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

        getTitle: function(sort) {
            return $t("Order #%1").replace('%1', sort);
        },

        isScheduleMethod: function (shippingMethod) {
            return (shippingMethod == 'transshipping_transshipping3') ? true : false;
        },

        isDeliveryMethod: function (shippingMethod) {
            return (shippingMethod == 'store_pickup_store_pickup') ? false : true;
        },

        getProductUrl: function (itemId) {
            return imageData[itemId].url;
        },

        getItemData: function (itemId) {
            return itemsData[itemId]();
        },

        getCustomerName: function (addressId) {
            return customerAddressData[addressId].firstname;
        },

        getAddressName: function (addressId) {
            return addressNameList[addressId];
        },

        getAddress: function (addressId) {
            var address = '',
                addressData = customerAddressData[addressId];
            address += _.values(addressData.street).join(', ');
            address += ', ' + addressData.customAttributes.district.label;
            address += ', ' + addressData.customAttributes.city.value;
            address += ', ' + addressData.region + ' ' + addressData.postcode;
            return address;
        },

        getTelephone: function (addressId) {
            return customerAddressData[addressId].telephone;
        },

        getStoreName: function () {
            return currentPickup.currentStoreName();
        },

        getStoreAddress: function () {
            return currentPickup.currentStoreAddress();
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
            let price = 0;
            if (imageData[item_id]) {
                if (imageData[item_id].promo_price_in_kg) {
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

        getWeight: function (item_id) {
            if (imageData[item_id]) {
                let weight = imageData[item_id].weight;
                return '(' + weight + ' ' + 'gram)';
            }
            return '';
        },

        getFormattedFreshPrice: function (price) {
            return priceUtils.formatPrice(price, quote.getPriceFormat()) + "/kg";
        }
    });
});