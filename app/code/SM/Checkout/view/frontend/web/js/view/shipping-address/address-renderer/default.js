/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'mage/url',
    'uiComponent',
    'underscore',
    'Magento_Checkout/js/action/select-shipping-address',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-address/form-popup-state',
    'Magento_Checkout/js/checkout-data',
    'Magento_Customer/js/customer-data',
    'Magento_Checkout/js/model/shipping-service',
    'Magento_Customer/js/model/address-list',
    'Magento_Ui/js/modal/modal',
    'SM_Checkout/js/view/shipping-address/update-delivery-address-status',
    'SM_Checkout/js/view/cart-items/init-shipping-type',
    'SM_Checkout/js/view/cart-items/set-shipping-type',
    'SM_Checkout/js/view/default-shipping-method',
    'SM_Checkout/js/view/shipping-address/single-date-time-select',
    'mage/calendar'
], function ($, ko, urlBuilder, Component, _, selectShippingAddressAction, quote, formPopUpState, checkoutData, customerData, shippingService, addressList, modal, updateStatus, initShippingType, setShippingType, defaultShipping, singleDateTime) {
    'use strict';

    var countryData = customerData.get('directory-data'),
        addressComplete = window.checkoutConfig.address_complete,
        preSelectAddress = window.checkoutConfig.pre_select_address;

    return Component.extend({
        isError: initShippingType.getSingleDeliveryMethodError(),
        isSelectedList: updateStatus.getOrderSelectAddressList(),
        deliveryMethodListSingle: defaultShipping.shippingMethodList,
        selectSingleShippingMethod: initShippingType.setSelectSingleShippingMethod(),
        singleDeliveryMethodValid: initShippingType.getSingleDeliveryMethodValid(),
        timeSlot: singleDateTime.timeSlot,
        timeSlotListStatus: singleDateTime.timeSlotListStatus,
        singleScheduleDate: singleDateTime.singleScheduleDate,
        singleScheduleTime: singleDateTime.singleScheduleTime,

        defaults: {
            template: 'SM_Checkout/shipping-address/address-renderer/default'
        },
        /** @inheritdoc */
        initialize: function () {
            this._super();
            return this;
        },

        /**
         * @param {String} countryId
         * @return {String}
         */
        getCountryName: function (countryId) {
            return countryData()[countryId] != undefined ? countryData()[countryId].name : ''; //eslint-disable-line
        },
        rates: shippingService.getShippingRates(),

        /**
         * Get customer attribute label
         *
         * @param {*} attribute
         * @returns {*}
         */
        getCustomAttributeLabel: function (attribute) {
            var resultAttribute;

            if (typeof attribute === 'string') {
                return attribute;
            }

            if (attribute.label) {
                return attribute.label;
            }

            if (typeof this.source.get('customAttributes') !== 'undefined') {
                resultAttribute = _.findWhere(this.source.get('customAttributes')[attribute['attribute_code']], {
                    value: attribute.value
                });
            }

            return resultAttribute && resultAttribute.label || attribute.value;
        },

        /** Set selected customer shipping address  */
        selectAddress: function () {
            selectShippingAddressAction(this.address());
            checkoutData.setSelectedShippingAddress(this.address().getKey());
        },

        /**
         * Edit address.
         */
        editAddress: function () {
            formPopUpState.isVisible(true);
            this.showPopup();

        },

        /**
         * Show popup.
         */
        showPopup: function () {
            $('[data-open-modal="opc-new-shipping-address"]').trigger('click');
        },
        /**
         * @returns {boolean}
         */
        isMaxAddress: function () {
            return this.isSelectedList().length > 1;
        },

        /**
         * Show address form popup
         */
        showFormPopUp: function () {
            $('.action-show-popup').trigger('click');
        },

        isSelected: function (addressId) {
            if (this.isSelectedList.indexOf(addressId.toString()) !== -1) {
                return true;
            }
            return false;
        },

        getAddressTag: function () {
            var customAttributes = this.address().customAttributes;
            if (customAttributes.address_tag) {
                return customAttributes.address_tag.value;
            }
            return '';
        },

        changeAddress: function (addressId) {
            var otherSelectedId = -1;
            $.each(this.isSelectedList(), function( index, value ) {
                if (addressId != value) {
                    otherSelectedId = value;
                    return false;
                }
            });
            updateStatus.setStatus({status: 'change', addressId: addressId, otherSelectedId: otherSelectedId});
            $('#delivery-address-list').modal('openModal');
        },

        addMoreSelectedAddress: function (addressId) {
            $('#delivery-address-list').modal('openModal');
            updateStatus.setStatus({status: 'add', addressId: addressId});
        },

        deleteSelectedAddress: function (addressId) {
            if (this.isSelectedList().length > 1) {
                this.isSelectedList.remove(addressId);
            }
        },

        isShow: function () {
            if (setShippingType.getValue()() == '2') {
                return false;
            }
            return true;
        },

        isDisable: function (value) {
            return (this.singleDeliveryMethodValid.indexOf(value) === -1) ? true : false;
        },

        isShowSelectDateTime: function () {
            return (initShippingType.getSelectSingleShippingMethod()() == 'transshipping_transshipping3') ? true : false;
        },

        renderDate: function(name) {
            var startDate = new Date(),
                endDate = new Date();
            endDate.setDate(endDate.getDate() + 7);
            startDate.setDate(startDate.getDate() + 1);

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
            }).datepicker("setDate", startDate).trigger('change');
        },

        preSelectTimeSlot: function () {
            this.singleScheduleTime(this.timeSlot()[0]);
        },

        closeTimeSlotList: function () {
            this.timeSlotListStatus(false);
        },

        openTimeSlotList: function () {
            this.timeSlotListStatus(true);
        },

        timeSlotSelected: function (value) {
            return (value == this.singleScheduleTime()) ? true : false;
        },

        selectTimeSlot: function (value) {
            this.singleScheduleTime(value);
            this.timeSlotListStatus(false);
        },

        isAddressComplete: function () {
            return addressComplete;
        },

        getEditAddressUrl: function (addressId) {
            return urlBuilder.build("customer/address/edit") + '/id/' + addressId + '?checkout=true';
        },

        notify: function (address) {
            if (setShippingType.getAddressNotCompleteNotify()() || this.showNotify(address)) {
                return true;
            }
            return false;
        },

        showNotify: function (address) {
            return !address.customAttributes.support_shipping.value
        }
    });
});
