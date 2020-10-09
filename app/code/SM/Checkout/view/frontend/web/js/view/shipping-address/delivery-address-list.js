/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'mage/url',
    'uiComponent',
    'mage/translate',
    'underscore',
    'Magento_Customer/js/model/address-list',
    'Magento_Customer/js/customer-data',
    'Magento_Ui/js/modal/modal',
    'SM_Checkout/js/view/shipping-address/update-delivery-address-status',
    'SM_Checkout/js/view/shipping-address/location'
], function ($, ko, urlBuilder, Component, $t, _, addressList, customerData, modal, updateStatus, location) {
    'use strict';

    var countryData = customerData.get('directory-data'),
        limitAddress = window.checkoutConfig.limitAddress,
        preSelectPopup = window.checkoutConfig.pre_select_popup;

    return Component.extend({
        hiddenAddress: ko.observable(-1),
        selectedAddress: ko.observable(-1),
        action: ko.observable(),
        currentAddressId: ko.observable(),

        /** @inheritdoc */
        initialize: function () {
            this._super();
            var self = this;
            updateStatus.getStatus().subscribe(function(value) {
                if (value.status == 'change') {
                    self.hiddenAddress(value.otherSelectedId);
                    self.selectedAddress(value.addressId);
                    self.action('change');
                }
                if (value.status == 'add') {
                    self.hiddenAddress(value.addressId);
                    self.selectedAddress(-1);
                    self.action('add');
                }
                self.currentAddressId(value.addressId);
            }, this);
            return this;
        },

        onRenderComplete: function (id) {
            var self = this,
                selector = $('#' + id),
                options = {
                    type: 'popup',
                    responsive: true,
                    innerScroll: false,
                    title: $t('Delivery Address'),
                    buttons: [],
                    modalClass: 'pp-delivery-address',
                    clickableOverlay: false
                };
            modal(options, selector);
            if (preSelectPopup && typeof preSelectPopup !== "undefined") {
                if (preSelectPopup.current_action == 'change') {
                    self.changeAddress(preSelectPopup.current_address_id.toString());
                }
                if (preSelectPopup.current_action == 'add') {
                    self.addMoreSelectedAddress(preSelectPopup.current_address_id.toString());
                }
            }
        },

        getAddressList: function () {
            return addressList();
        },

        /**
         * @param {String} countryId
         * @return {String}
         */
        getCountryName: function (countryId) {
            return countryData()[countryId] != undefined ? countryData()[countryId].name : ''; //eslint-disable-line
        },

        getAddressTag: function (customAttributes) {
            if (customAttributes.address_tag) {
                return customAttributes.address_tag.value;
            }
            return '';
        },

        isShow: function (addressId) {
            if (this.hiddenAddress() != addressId) {
                return true;
            }
            return false;
        },

        isSelected: function (addressId) {
            if (this.selectedAddress() == addressId) {
                return true;
            }
            return false;
        },

        selectAddress: function (addressId) {
            $('#delivery-address-list').modal('closeModal');
            var self = this;
            if (this.action() == 'change') {
                updateStatus.setOrderSelectAddressList(this.selectedAddress(), addressId);
            } else {
                updateStatus.setOrderSelectAddressList(-1, addressId);
            }
        },

        addNewAddress: function () {
            $('#delivery-address-list').modal('closeModal');
            $('.action-show-popup').trigger('click');
            location.initMap('init');
        },

        addNewAddressUrl: function () {
            var createNewAddress = urlBuilder.build("customer/address/new") + '?checkout=true';
            createNewAddress += '&current=' + this.currentAddressId();
            createNewAddress += '&action=' + this.action();
            return createNewAddress;
        },

        isShowAddNewAddress: function () {
            if (addressList().length < parseInt(limitAddress)) {
                return true;
            }
            return false;
        },

        changeAddress: function (addressId) {
            var otherSelectedId = -1;
            $.each(updateStatus.getOrderSelectAddressList(), function( index, value ) {
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
    });
});
