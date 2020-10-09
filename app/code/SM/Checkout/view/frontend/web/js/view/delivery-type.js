/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/totals',
    'SM_Checkout/js/view/cart-items/set-shipping-type',
    'mage/translate'
], function ($, ko, Component, totals, orderShippingType, $t) {
    'use strict';

    var deliveryTypeList = window.checkoutConfig.delivery_type,
        addressComplete = window.checkoutConfig.address_complete,
        sortSourceDefault = window.checkoutConfig.sortSource,
        preOrderShippingType = window.checkoutConfig.pre_select_order_shipping_type;

    return Component.extend({
        isActiveList: {},
        orderDeliveryType: ko.observableArray([]),

        defaults: {
            template: 'SM_Checkout/delivery-type'
        },
        /** @inheritdoc */
        initialize: function () {
            this._super();
            this.init();
            return this;
        },

        init: function () {
            var self = this;
            $.each(deliveryTypeList, function( index, item ) {
                var status = false;
                if (preOrderShippingType != '2') {
                    if (preOrderShippingType == item.value) {
                        status = true;
                        self.orderDeliveryType.push(item.value);
                        orderShippingType.setValue(item.value);
                    }
                } else {
                    status = true;
                    self.orderDeliveryType.push(item.value);
                    orderShippingType.setValue('2');
                }
                self.isActiveList[item.value] = ko.observable(status);
            });
            self.orderDeliveryType.subscribe(function(changes) {
                changes.forEach(function(change) {
                    if (change.status === 'deleted') {
                        self.isActiveList[change.value](false);
                    } else if (change.status === 'added') {
                        self.isActiveList[change.value](true);
                    }
                });
                if (self.orderDeliveryType().length == deliveryTypeList.length) {
                    orderShippingType.setValue('2');
                } else {
                    $.each(self.orderDeliveryType(), function( index, item ) {
                        orderShippingType.setValue(item);
                    });
                }
            }, null, "arrayChange");
        },

        /**
         * get img of mobile number
         * @returns {*}
         */
        getImageMobileNumber: function (){
            return window.checkoutConfig.viewFileUrl;
        },

        /**
         * return all attributes callable
         * @returns {*}
         */
        getDeliveryType: function(){
            return deliveryTypeList;
        },

        /**
         *
         * @returns {boolean}
         */
        isShowBoth: function(){
            return (deliveryTypeList.length >= 2 && totals.getItems()().length >= 2) ? true :false;
        },

        /**
         * capture delivery click
         * @param code
         */
        deliveryTypeClick: function(value){
            var self = this;
            if (value == 1 && sortSourceDefault.length == 0) {
                if ($('.pickup-not-avaliable-message').length == 0) {
                    var message = window.checkoutConfig.notFulFillMessage;
                    $('body').append('<div class="pickup-not-avaliable-message" style="display:none">' + message + '</div>');
                }
                $('.pickup-not-avaliable-message').fadeIn('slow');
                setTimeout(function(){
                    $('.pickup-not-avaliable-message').fadeOut('slow');
                },2000);
                return;
            }
            if (self.orderDeliveryType().length != deliveryTypeList.length && self.orderDeliveryType.indexOf(value) == -1) {
                self.orderDeliveryType.pop();
                self.orderDeliveryType.push(value);
            }
        },

        /**
         * capture both click
         */
        setBothOptions: function(){
            var self = this;
            if (!addressComplete) {
                orderShippingType.setAddressNotCompleteNotify(true);
                return;
            } else if (sortSourceDefault.length == 0) {
                if ($('.showboth-not-avaliable-message').length == 0) {
                    var message = window.checkoutConfig.notFulFillMessage;
                    $('body').append('<div class="pickup-not-avaliable-message showboth-not-avaliable-message" style="display:none">' + message + '</div>');
                }
                $('.showboth-not-avaliable-message').fadeIn('slow');
                setTimeout(function(){
                    $('.showboth-not-avaliable-message').fadeOut('slow');
                },2000);
                return;
            }
            if (this.orderDeliveryType().length == deliveryTypeList.length) {
                self.orderDeliveryType.pop();
            } else {
                $.each(deliveryTypeList, function( index, item ) {
                    if (self.orderDeliveryType.indexOf(item.value) == -1) {
                        self.orderDeliveryType.push(item.value);
                    }
                });
            }
        },

        addActive: function (value) {
            return this.isActiveList[value];
        },

        addActiveBoth: function () {
            return this.orderDeliveryType().length == deliveryTypeList.length
        },

        hideSelectShippingType: function () {
            if (deliveryTypeList.length >= 2) {
                return false;
            }
            return true;
        }
    });
});
