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
    'gtmCheckout',
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
    'SM_Checkout/js/view/cart-items/init-shipping-type',
    'Trans_Mepay/js/view/payment/method-renderer/trans_mepay'
], function (
    $,
    ko,
    Component,
    storage,
    urlManager,
    gtmCheckout,
    fullScreenLoader,
    getTotalsAction,
    globalVar,
    customerData,
    placeOrder,
    $t,
    messageList,
    pickup,
    split,
    findStoreAction,
    setShippingType,
    initShippingType,
    transMepay
) {
    'use strict';

    var updatePaymentMethodFromPreviewOrder = false;
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
            this.isStepShipping = ko.computed(function () {
                var isStepShipping = globalVar.isStepPreviewOrder,
                    isStepPayment = globalVar.isStepShipping;
                return (isStepShipping() || isStepPayment()) ? true : false;
            }, this);
            this.disableGoPaymentButton = ko.computed(function () {
                return (globalVar.disableGoPaymentButton() || (!findStoreAction.storeFullFill() && setShippingType.getValue()() != '0')) ? true : false;
            }, this);
            this.buttonTitle = ko.computed(function () {
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
                JSON.stringify({ 'customer_id': window.checkoutConfig.customerData.id })
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

        nextPayment: function () {
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
                this.pushGtmStep2();
                globalVar.isStepPreviewOrder(false);
                globalVar.isStepPayment(true);
                $('.custom-placeorder-button').attr('disabled', true);
                $('.custom-placeorder-button').css('background', '#777777');
                return;
            }
            var rateData = initShippingType.getRatesData(),
                data = initShippingType.getDateTime();
            data['items'] = rateData['items'];
            data['store'] = pickup.currentPickupId();
            data['update_payment_method'] = updatePaymentMethodFromPreviewOrder;
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
                        self.pushGtmStep2();
                    }
                    split.setPreviewOrder(response.order);
                    globalVar.isStepShipping(false);
                    globalVar.paymentSelected(false);
                    if (!updatePaymentMethodFromPreviewOrder) {
                        globalVar.paymentMethod(response.payment_method);
                        updatePaymentMethodFromPreviewOrder = true;
                    }
                    self.addBreadCrumb();
                }
            ).fail(
                function (response) {
                    fullScreenLoader.stopLoader();
                }
            );

        },
        placeOrder: function () {
            // **** BANK MEGA payment ****
            var obj = this.getPaymentMethods();
            var redirectUrl = urlManager.build('transcheckout');
            if (obj['method'] == 'trans_mepay_allbankccdebit' || obj['method'] == 'trans_mepay_debit' || obj['method'] == 'trans_mepay_cc' || obj['method'] == 'trans_mepay_va' || obj['method'] == 'trans_mepay_qris' || obj['method'] == 'trans_mepay_allbank_cc' || obj['method'] == 'trans_mepay_allbank_debit') {
                return $.when(placeOrder(this.getPaymentMethods(), '')).done(
                    function () {
                        $('[data-href="payment-error"]').text('').addClass('hidden');
                        window.location.replace(urlManager.build('transmepay/payment/redirect'));
                    }
                ).fail(
                    function () {
                        window.location.replace(redirectUrl);
                    }
                );
            }
            // **** end BANK MEGA payment ****

            return placeOrder(this.getPaymentMethods(), '').success(
                function () {
                    var redirectUrl = urlManager.build('sprint/payment/authorization');
                    $('[data-href="payment-error"]').text('').addClass('hidden');
                    $.ajax({
                        type: 'post',
                        showLoader: true,
                        url: redirectUrl,
                        cache: false,
                        success: function (data) {
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
                                        always: function () { }
                                    }
                                });
                                // window.location.replace(urlManager.build('transcheckout/checkout'));
                            }
                        } //end of ajax success
                        , error: function (error) {
                            window.location.replace(redirectUrl);
                        }
                    });
                }
            ).fail(
                function () {
                    window.location.replace(redirectUrl);
                }
            );
        },

        getPaymentMethods: function () {
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
                "text": $.mage.__("Payment"),
            })))

        },

        pushGtmStep2: function () {
            let items          = window.checkoutConfig.quoteItemData,
                deliveryOption = window.itemsCheckoutGTM,
                itemsData      = [];

            if (!dataLayerSourceObjects || !items || !items.length) {
                return;
            }

            $.each(items, function (key, value) {
                let delivery = "Delivery";

                if (deliveryOption[key].shipping_method_selected === 'store_pickup_store_pickup') {
                    delivery = $('h4[data-bind="html: currentStoreName"]').text();
                }

                itemsData.push({
                    'productId'      : value.product_id,
                    'productQty'     : value.qty,
                    'delivery_option': delivery
                });
            });

            $.ajax({
                type: 'POST',
                url: urlManager.build('sm_gtm/gtm/product'),
                data: {'productsInfo': itemsData},
                dataType: "json",
                async: true,
                success: function (result) {
                    if (result) {
                        let dataProducts = [],
                            quantity     = 0,
                            total        = 0;
                        $.each(result, function (key, value) {
                            try {
                                let product = JSON.parse(value);

                                dataProducts.push(product);
                                quantity += (product.quantity * 1);
                                total += (product.quantity * product.price);
                            } catch (e) {
                            }
                        });

                        dataProducts['step'] = 2;
                        dataProducts['option'] = 'Delivery Option';
                        dataProducts['basket_value'] = total;
                        dataProducts['basket_quantity'] = quantity;
                        dataProducts['eventCallback'] = function () {};
                        dataProducts['eventTimeout'] = 2000;
                        gtmCheckout.push('checkout', dataProducts);
                        $.localStorage.set(
                            'product-checkout-gtm',
                            {
                                'step'           : 3,
                                'option'         : 'Payment Method',
                                'basket_value'   : total,
                                'basket_quantity': quantity,
                                'eventTimeout'   : 2000,
                                'product'        : dataProducts
                            }
                        );
                    }
                },
                error: function () {
                }
            });
        }
    });
});
