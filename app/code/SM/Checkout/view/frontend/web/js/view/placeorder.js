/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'uiComponent',
    'mage/storage',
    'mage/url',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/action/get-totals',
    'SM_Checkout/js/view/global-observable',
    'Magento_Customer/js/customer-data',
    'Magento_Checkout/js/action/place-order',
    'mage/translate',
    'Magento_Ui/js/model/messageList',
    'SM_Checkout/js/view/shipping-address/current-pickup',
    'SM_Checkout/js/view/split',
    'SM_Checkout/js/action/find-store',
    'SM_Checkout/js/view/cart-items/set-shipping-type',
    'SM_Checkout/js/view/cart-items/init-shipping-type'
], function ($, ko, Component, storage, urlManager, fullScreenLoader, getTotalsAction, globalVar, customerData, placeOrder,$t,messageList,pickup, split, findStoreAction, setShippingType, initShippingType) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'SM_Checkout/view/placeorder'
        },
        disableGoPaymentButton: ko.observable(false),
        isStepPayment: globalVar.isStepPayment,
        buttonTitle: ko.observable($t('Go To Payment')),
        initialize: function () {
            this._super();
            var self = this;
            this.isStepShipping = ko.computed(function() {
                var isStepShipping = globalVar.isStepPreviewOrder,
                    isStepPayment = globalVar.isStepShipping;
                return (isStepShipping() || isStepPayment()) ? true : false;
            }, this);
            this.disableGoPaymentButton = ko.computed(function () {
                return (globalVar.disableGoPaymentButton() || (!findStoreAction.storeFullFill() && setShippingType.getValue()() != '0')) ? true : false;
            }, this);
            this.buttonTitle = ko.computed(function() {
                if (globalVar.isStepShipping() && globalVar.splitOrder()) {
                    return $t('Continue');
                } else {
                    return $t('Go To Payment');
                }
            }, this);
            this.updateCartSection();
            return this;
        },

        updateCartSection: function () {
            if (window.checkoutConfig.is_virtual) {
                customerData.invalidate(['cart']);
            }
        },

        placeOrderAction: function () {
            fullScreenLoader.startLoader();
            storage.post(
                urlManager.build('rest/V1/trans-checkout/me/placeorder'),
                JSON.stringify({'customer_id': window.checkoutConfig.customerData.id})
            ).done(
                function (response) {
                    fullScreenLoader.stopLoader();
                    var response = $.parseJSON(response);
                    var sections = ['cart'];
                    customerData.invalidate(sections);
                    //customerData.reload(sections, true);

                    window.location.href = response.url;
                }
            ).fail(
                function (response) {
                    fullScreenLoader.stopLoader();
                    /**
                     * reload block totals
                     */
                    var deferred = $.Deferred();
                    var response = $.parseJSON(response);
                    window.location.href = response.url;
                    getTotalsAction([], deferred);
                }
            );
        },

        nextPayment: function(){
            var self = this;
            if (this.disableGoPaymentButton()) {
                return;
            }
            if (!window.checkoutConfig.address_complete && setShippingType.getValue()() == "0") {
                setShippingType.setAddressNotCompleteNotify(true);
                return;
            }
            if (window.checkoutConfig.sortSource.length == 0 && setShippingType.getValue()() == "1") {
                return;
            }
            if (globalVar.isStepPreviewOrder()) {
                /**
                 * chua trigger dc enable/disable button place order de tam nhu nay nhe :v
                 */
                globalVar.isStepPreviewOrder(false);
                globalVar.isStepPayment(true);
                $('.custom-placeorder-button').attr('disabled', true);
                $('.custom-placeorder-button').css('background', '#777777');
                return;
            }
            var rateData = initShippingType.getRatesData(),
                data = initShippingType.getDateTime();
            data['items'] = rateData['items'];
            fullScreenLoader.startLoader();
            storage.post(
                urlManager.build('rest/V1/trans-checkout/me/previewOrder'),
                JSON.stringify(data)
            ).done(
                function (response) {
                    fullScreenLoader.stopLoader();
                    if (response.reload) {
                        globalVar.disableGoPaymentButton(true);
                        window.location.href = urlManager.build("transcheckout");
                        return;
                    }
                    if (response.is_split_order == true) {
                        globalVar.isStepPreviewOrder(true);
                    } else {
                        globalVar.isStepPayment(true);
                    }
                    split.setPreviewOrder(response.order);
                    globalVar.isStepShipping(false);
                    globalVar.paymentSelected(false);
                    self.addBreadCrumb();
                }
            ).fail(
                function (response) {
                    fullScreenLoader.stopLoader();
                }
            );

        },
        placeOrder:function () {
            return $.when(placeOrder(this.getPaymentMethods(), '')).done(
                function () {

                    var redirectUrl = urlManager.build('sprint/payment/authorization');
                    $('[data-href="payment-error"]').text('').addClass('hidden');
                    $.ajax({
                               type: 'post',
                               showLoader: true,
                               url: redirectUrl,
                               cache: false,
                               success: function(data) {
                                   let sections = ['cart'];
                                   customerData.invalidate(sections);

                                   var status = data.insertStatus;

                                   var message = $t(data.insertMessage);

                                   if (data === false) {
                                       messageList.addErrorMessage({
                                                                       message: message
                                                                   });
                                   }

                                   if (status === '00') {
                                       var afterPlaceOrder = urlManager.build('transcheckout/index/success');
                                       if (data.redirectURL) {
                                            afterPlaceOrder = data.redirectURL;
                                       }
                                       window.location.replace(afterPlaceOrder);

                                   } else {
                                       $('[data-href="payment-error"]').text(message).removeClass('hidden');
                                       messageList.addErrorMessage({
                                                                       message: message
                                                                   });
                                       console.log({
                                                 title: 'Error',
                                                 content: message,
                                                 actions: {
                                                     always: function() {}
                                                 }
                                             });
                                       // window.location.replace(urlManager.build('transcheckout/checkout'));
                                   }
                               } //end of ajax success
                        ,error:function (error) {
                            console.log(error);
                        }
                           });
                }
            );
        },

        getPaymentMethods:function () {
            if (window.digitalProduct) {
                return {
                    'method': $('input[name="selected-method"]').val(),
                    'po_number': null,
                    'additional_data': ['digital']
                };
            } else {
                return {
                    'method': $('input[name="selected-method"]').val(),
                    'po_number': null,
                    'additional_data': null
                };
            }
        },

        addBreadCrumb: function () {
            var crumbs = $(".breadcrumbs ul.items");
            var last = crumbs.find("li").last();
            last.html($("<a/>", {
                "href": "/transcheckout",
                "title": $.mage.__("Check Out"),
                "text": $.mage.__("Check Out")
            }));

            crumbs.append($("<li/>", {}).append($("<strong/>", {
                "text" : $.mage.__("Payment"),
            })))

        }
    });
});
