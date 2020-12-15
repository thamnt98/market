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
    'SM_Checkout/js/view/shipping-address/current-pickup'
], function ($, ko, Component, quote, priceUtils, $t, globalVar, split, shippingType, currentPickup) {
    'use strict';

    var imageData = window.checkoutConfig.imageData,
        weightUnit = window.checkoutConfig.weightUnit,
        customerAddressData = shippingType.getCustomerAddressData(),
        addressNameList = shippingType.getAddressNameList(),
        itemsData = shippingType.getItemsDataList();

    return Component.extend({
        isStepPreviewOrder: globalVar.isStepPreviewOrder,
        previewOrderData: split.getPreviewOrder(),
        initialize: function () {
            this._super();
            return this;
        },

        getFormattedPrice: function (price) {
            return priceUtils.formatPrice(price, quote.getPriceFormat());
        },

        getTitle: function(sort, totalItems) {
            if (totalItems > 1) {
                return $t("Order #%1 (%2 items)").replace('%1', sort).replace('%2', totalItems);
            }
            return $t("Order #%1 (%2 item)").replace('%1', sort).replace('%2', totalItems);
        },

        isDeliveryMethod: function (shippingMethod) {
            return (shippingMethod == 'store_pickup_store_pickup') ? false : true;
        },

        isScheduleMethod: function (shippingMethod) {
            return (shippingMethod == 'transshipping_transshipping3') ? true : false;
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

        getItemData: function (itemId) {
            return itemsData[itemId]();
        },

        getProductUrl: function (itemId) {
            return imageData[itemId].url;
        },

        getProductImage: function (itemId) {
            return imageData[itemId].src;
        },

        getSubtotal: function (subtotal) {
            return subtotal;
        },

        /**
         * @param int item
         * @return string
         */
        getWeight: function (itemId) {
            if (imageData[itemId]) {
                return '(' + imageData[itemId].weight + ' ' + weightUnit +')';
            }

            return '';
        },

        isDiscount: function (baseSubtotal, subtotal) {
            if (baseSubtotal != subtotal) {
                return true;
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

        getFormatPrice: function (price) {
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
