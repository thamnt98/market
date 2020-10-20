/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Customer/js/model/address-list',
    'Magento_Checkout/js/model/totals',
    'SM_Checkout/js/view/cart-items/set-shipping-type',
    'SM_Checkout/js/action/get-shipping-method',
    'SM_Checkout/js/view/shipping-address/update-delivery-address-status',
    'SM_Checkout/js/view/cart-items/init-shipping-type',
    'SM_Checkout/js/view/shipping-address/current-pickup',
    'SM_Checkout/js/view/default-shipping-method',
    'SM_Checkout/js/view/shipping-address/single-date-time-select'
], function ($, ko, Component, addressList, totals, setShippingType, getShippingMethod, updateStatus, initShippingType, pickup, defaultShipping, singleDateTime) {
    'use strict';

    return Component.extend({
        showShippingAddressPerItem: ko.observable(false),
        deliveryPerItem: ko.observable(false),
        storePickUpPerItem: ko.observable(false),
        isShowAddress: ko.observable(false),

        selectShippingType: initShippingType.getSelectShippingType(),
        disableDeliveryList: initShippingType.getDisableDeliveryList(),
        addressSelectedList: initShippingType.getAddressSelectedList(),
        storeSelectedList: initShippingType.getStoreSelectedList(),
        deliveryMethodList: defaultShipping.shippingMethodList,
        shippingMethodSelectListError: initShippingType.getDeliveryMethodListError(),
        storePickUpListError: initShippingType.getStorePickUpListError(),
        shippingMethodSelectList: initShippingType.getShippingMethodSelectList(),
        shippingMethodListValid: initShippingType.geShippingMethodListValid(),
        timeSlotListStatus: initShippingType.getTimeSlotListStatus(),

        storeList: ko.observableArray([]),
        itemsData: ko.observable({}),
        showAllShippingType: ko.observable(),
        storePickUp: pickup.currentStoreName,

        timeSlot: singleDateTime.timeSlot,
        addressListDate: initShippingType.getAddressListDate(),
        addressListTime: initShippingType.getAddressListTime(),

        initialize: function () {
            this._super();
            this.init();
            this.setStoreList();
        },

        init: function () {
            var self = this;

            self.deliveryPerItem = ko.computed(function() {
                if (updateStatus.getOrderSelectAddressList()().length == 1 && setShippingType.getValue()() != '2') {
                    return false;
                }
                var orderShippingType = setShippingType.getValue()();
                if (orderShippingType == '0') {
                    return true;
                } else if (orderShippingType == '1') {
                    return false;
                } else if (orderShippingType == '2') {
                    return true;
                }
                return false;
            }, this);

            self.storePickUpPerItem = ko.computed(function() {
                if (updateStatus.getOrderSelectAddressList()().length == 1 && setShippingType.getValue()() != '2') {
                    return false;
                }

                var orderShippingType = setShippingType.getValue()();
                if (orderShippingType == '0') {
                    return false;
                } else if (orderShippingType == '1') {
                    return true;
                } else if (orderShippingType == '2') {
                    return true;
                }
                return false;
            }, this);

            self.isShowAddress = ko.computed(function() {
                if (setShippingType.getValue()() == '2' && updateStatus.getOrderSelectAddressList()().length == 1) {
                    return false;
                }
                return true;
            }, this);

            /*set showShippingAddressPerItem dependent deliveryPerItem and storePickUpPerItem*/
            this.showShippingAddressPerItem = ko.computed(function() {
                return (this.deliveryPerItem() || this.storePickUpPerItem()) ? true : false;
            }, this);

            this.showAllShippingType = ko.computed(function() {
                return (this.deliveryPerItem() && this.storePickUpPerItem()) ? false : true;
            }, this);
        },

        getDeliveryMethodList: function (item_id) {
            return this.deliveryMethodList;
        },

        addressTagList: function () {
            return initShippingType.getAddressTagList();
        },

        setStoreList: function () {
            var self = this,
                MSI = window.checkoutConfig.msi;
            $.each(MSI, function( index, storeSource ) {
                self.storeList.push({label: storeSource.name, value: storeSource.source_code});
            });
        },

        isChecked: function (item_id) {
            return this.selectShippingType[item_id];
        },

        isActive: function (item_id, value) {
            if (this.selectShippingType[item_id]() == value) {
                return true;
            }
            return false;
        },

        disableDelivery: function (item_id) {
            if (this.deliveryPerItem() && this.storePickUpPerItem()) {
                return this.disableDeliveryList[item_id];
            } else if (this.deliveryPerItem()) {
                return false;
            } else {
                return true;
            }
        },

        deliveryTypeClick: function (item_id, value) {
            this.selectShippingType[item_id](value);
        },

        addressSelected: function (item_id) {
            return this.addressSelectedList[item_id];
        },

        storeSelected: function (item_id) {
            return this.storeSelectedList[item_id];
        },

        selectShippingMethod: function (item_id) {
            return this.shippingMethodSelectList[item_id];
        },

        isError: function (item_id) {
            var selectedAddressId = this.addressSelected(item_id)(),
                selectedAddress = initShippingType.getCustomerAddressData()[selectedAddressId];
            if (selectedAddress.customAttributes.support_shipping.value) {
                return this.shippingMethodSelectListError[item_id];
            }
            return false;
        },

        isFullFill: function (item_id) {
            return this.storePickUpListError[item_id];
        },

        isDisable: function (item_id, value) {
            return (this.shippingMethodListValid[item_id].indexOf(value) === -1) ? true : false;
        },

        isShowSelectDateTime: function (item_id) {
            return (this.shippingMethodSelectList[item_id]() == 'transshipping_transshipping3') ? true : false;
        },

        selectScheduleDate: function (item_id) {
            var addressId = this.addressSelectedList[item_id]();
            if (this.addressListDate[addressId]() == '') {
                var startDate = new Date();
                startDate.setDate(startDate.getDate() + 1);
                this.addressListDate[addressId]($.datepicker.formatDate('dd M yy', startDate));
            }
            return this.addressListDate[addressId];
        },

        selectScheduleTime: function (item_id) {
            var addressId = this.addressSelectedList[item_id]();
            if (this.addressListTime[addressId]() == '') {
                this.addressListTime[addressId](this.timeSlot()[0]);
            }
            return this.addressListTime[addressId];
        },

        renderDate: function (name, item_id) {
            var addressId = this.addressSelectedList[item_id](),
                startDate = new Date(),
                endDate = new Date();
            endDate.setDate(endDate.getDate() + 7);
            startDate.setDate(startDate.getDate() + 1);
            if (this.addressListDate[addressId]() != '') {
                var selectedDate = this.addressListDate[addressId]();
            } else {
                var selectedDate = startDate;
            }
            $("[name= " + name +"]").calendar({
                dateFormat: "dd MMM yy",
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                currentText: 'Go Today',
                closeText: 'Close',
                hideIfNoPrevNext: true,
                minDate: startDate,
                maxDate: endDate
            }).datepicker("setDate", selectedDate).trigger('change');
        },

        closeTimeSlotList: function (item_id) {
            this.timeSlotListStatus[item_id](false);
        },

        openTimeSlotList: function (item_id) {
            this.timeSlotListStatus[item_id](true);
        },

        preSelectTimeSlot: function (item_id) {
            var addressId = this.addressSelectedList[item_id]();
            if (this.addressListTime[addressId]() == '') {
                this.addressListTime[addressId](this.timeSlot()[0]);
            }
        },

        timeSlotListStatusPerItem: function (item_id) {
            return this.timeSlotListStatus[item_id];
        },

        timeSlotSelected: function (item_id, value) {
            var addressId = this.addressSelectedList[item_id]();
            return (this.addressListTime[addressId]() == value) ? true : false;
        },

        selectTimeSlot: function (item_id, value) {
            var addressId = this.addressSelectedList[item_id]();
            this.addressListTime[addressId](value);
            this.closeTimeSlotList(item_id);
        },
    });
});
