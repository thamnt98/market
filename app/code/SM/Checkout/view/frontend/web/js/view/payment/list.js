/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'uiComponent',
    'mage/url',
    'mage/storage',
    'Magento_Checkout/js/model/full-screen-loader',
    'SM_Checkout/js/view/global-observable',
    'Trans_Sprint/js/action/set-service-fee',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/action/select-payment-method',
    'Magento_Ui/js/model/messageList',
    'Magento_Checkout/js/action/get-payment-information',
    'Magento_Checkout/js/model/totals',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils'
], function (
    $,
    ko,
    Component,
    urlManager,
    storage,
    fullScreenLoader,
    globalVar,
    setServiceFee,
    checkoutData,
    selectPaymentMethodAction,
    messageList,
    getPaymentInformation,
    totals,
    quote,
    priceUtils
) {
    'use strict';
    return Component.extend({
        ccFullPaymentCode: 'sprint_allbankfull_cc',
        selectedMethod: ko.observable(),
        instalmentTerms: ko.observable(),
        paymentDescription: ko.observable(),
        paymentTooltipDescription: ko.observable(),
        grandTotalAmount: ko.observable(0),
        canPay: true,
        defaults: {
            template: 'SM_Checkout/payment-methods/list',
            templateCC: 'SM_Checkout/payment-methods/method/cc',
            templateDB: 'SM_Checkout/payment-methods/method/db',
            templateOVO: 'SM_Checkout/payment-methods/method/db',
            templateVA: 'SM_Checkout/payment-methods/method/va',
            templateQRIS: 'SM_Checkout/payment-methods/method/qris'
        },
        /**
         * Initialize view.
         *
         * @returns {Component} Chainable.
         */
        initialize: function () {
            this._super();
            let self = this;
            self.grandTotalAmount(window.checkoutConfig.totalsData.grand_total);
            quote.totals.subscribe(function (newValue) {
                self.grandTotalAmount(newValue.grand_total);
            });
        },
        invalidMinimumAmount: function (payment_type) {
            if (typeof window.checkoutConfig.payment.sm_config[payment_type] !== 'undefined') {
                var minimum = window.checkoutConfig.payment.sm_config[payment_type].minimum_amount;
                var grand_total = this.grandTotalAmount();
                if (typeof minimum !== 'undefined' && grand_total < minimum) {
                    return minimum;
                }
                return false;
            }
        },
        formatPrice: function (price) {
            return priceUtils.formatPrice(price, quote.getPriceFormat(), false);
        },
        paymentLists: function () {
            return window.checkoutConfig.paymentMethods;
        },
        getListPaymentVirtualAccount: function () {
            var methods = ["sprint_bca_va", "trans_mepay_va"];
            return this.getPaymentInList(methods);
        },
        getListPaymentCreditCard: function () {
            var methods = ["sprint_allbankfull_cc", 'sprint_mega_cc', 'trans_mepay_cc', 'trans_mepay_debit'];
            return this.getPaymentInList(methods);
        },
        getListPaymentQris: function () {
            var methods = ["trans_mepay_qris"];
            return this.getPaymentInList(methods);
        },
        getListPaymentInstallment: function () {
            var methods = ['sprint_bca_cc'];
            return this.getPaymentInList(methods);
        },
        getPaymentInList: function (methods) {
            if (!this.hasMethod()) {
                return false;
            } else {
                var payments = this.paymentLists();
                var listPaymentVA = [];
                payments.forEach(function (v) {
                    methods.forEach(function (i) {
                        if (v.method.indexOf(i) >= 0) {
                            listPaymentVA.push(v);
                        }
                    })
                });
                if (listPaymentVA.length > 0) {
                    return listPaymentVA;
                }
                return false;
            }
        },
        getInstalmentOption: function (method) {
            var sprintInstalmentTerms = window.checkoutConfig.payment.sprint.installmentTerms;
            var self = this;
            if (sprintInstalmentTerms.length > 0) {
                sprintInstalmentTerms.forEach(function (value) {
                    if (value[method]) {
                        if (value[method].length > 0) {
                            var grandTotal = window.checkoutConfig.totalsData.grand_total;
                            value[method].forEach(function (v) {
                                v.totalFee = (String(grandTotal * (100 + parseInt(v.serviceFeeValue)) / 100)).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
                            });
                            self.instalmentTerms(value[method]);
                            return value[method];
                        }
                    }
                });
            }
            return false;
        },
        showOptions: function (data, event) {
            var element = event.target;
            var idElm = element.getAttribute('id');
            $('.ck-payment .bx-payment').removeClass('active');
            $('.ck-payment .bx-payment #' + idElm).parents('.bx-payment').addClass('active');
            if (idElm == 'cc') {
                if (this.invalidMinimumAmount('credit_card')) {
                    $('.bx-payment .payment-valid').addClass('hidden');
                    $('[data-href="valid-min-amount-cc"]').removeClass('hidden');
                }
                $('[data-id="option-selected-cc"]').text('Select Bank');
                $('[data-href="cc-method"]').removeClass('active');
                $('[data-id="full-payment"]').addClass('active');
                $('[data-href="credit-options"]').removeClass('hidden');
                this.disabledBtnPay();
            } else if (idElm == 'va') {
                let optSelected = $(element).attr('data-option');
                $('[data-href="credit-options"]').addClass('hidden');

                if (this.invalidMinimumAmount('virtual_account')) {
                    $('.bx-payment .payment-valid').addClass('hidden');
                    $('[data-href="valid-min-amount-va"]').removeClass('hidden');
                }

                if (optSelected) {
                    this.setPaymentMethod(optSelected);
                } else {
                    this.disabledBtnPay();
                }
            } else if (idElm == 'qris') {
                $('[data-href="credit-options"]').addClass('hidden');
                let optSelected = $(element).attr('data-option');
                if (optSelected) {
                    this.setPaymentMethod(optSelected);
                } else {
                    this.disabledBtnPay();
                }
            } else {
                setServiceFee(0);
            }

            $('[data-href="instalment-terms"]').addClass('hidden');
            $('[data-id="option-selected-instalment"]').text('Select Bank');
            $('[data-href="instalment-options"]').addClass('hidden');
            $('[data-href="payment-method-options"]').addClass('hidden');
            $('[data-id="payment-method-' + idElm + '"]').removeClass('hidden');
            return true;
        },
        showInstalmentOptions: function (data, event) {
            var element = event.target;
            $('[data-href="instalment-options"]').removeClass('hidden');

        },
        showInstallmentTerms: function (data, event) {
            var ele = event.target;
            var method = ele.getAttribute('data-id');
            this.getInstalmentTerm(method);
            this.selectedMethod(method);
            $('[data-href="instalment-terms"]').removeClass('hidden');
        },
        disabledBtnPay: function () {
            $('.custom-placeorder-button').addClass('disabled').attr('disabled', true).css('background', '#777777');
            $(".accept-terms").addClass("hidden");
            return true;
        },
        enableBtnPay: function () {
            $('.custom-placeorder-button').removeClass('disabled').attr('disabled', false).css('background', '#f7b500');
            $(".accept-terms").removeClass("hidden");
            return true;
        },
        selectOption: function (data, event) {
            var ele = event.target;
            var dataHref = ele.getAttribute('data-href');
            $('[data-id="' + dataHref + '"]').text($(ele).text());
            $(ele).parents('[data-href="group-methods"]').find('input[type=radio]').attr('data-option', $(ele).attr('data-id'))
            return true;
        },
        activeMethod: function (data, event) {
            var ele = event.target;
            var dataHref = ele.getAttribute('data-href');
            $('[data-href="' + dataHref + '"]').removeClass('active');
            $(ele).addClass('active');
            if ($(ele).attr('data-id') == 'installment') {
                $('[data-id="option-selected-instalment"]').text('Select Bank');
                $('[data-href="instalment-terms"]').addClass('hidden');
                $('[data-href="credit-options"]').addClass('hidden');
                this.disabledBtnPay();
            } else if ($(ele).attr('data-id') == 'full-payment') {
                $('[data-id="option-selected-cc"]').text('Select Bank');
                $('[data-href="credit-options"]').removeClass('hidden');
                $('[data-href="instalment-terms"]').addClass('hidden');
                $('[data-href="instalment-options"]').addClass('hidden');
                this.disabledBtnPay();
            }
            return true;
        },
        showMore: function () {
            $('[data-href="order-item"]').removeClass('hidden');
            $('[data-id="show-more"]').addClass('hidden')
        },
        hasMethod: function () {
            return (window.checkoutConfig.paymentMethods.length > 0) ? true : false;
        },
        selectedInstalmentTerm: function (data, event) {
            var elm = event.target;
            var method = $(elm).attr('data-href');
            var serviceFeeValue = $(elm).attr('data-count');
            var value = $(elm).val();
            var dataPayment = {
                'method': method,
                'additional_data': {
                    'tenor': value,
                    'serviceFeeValue': serviceFeeValue
                }
            };
            var self = this;
            checkoutData.setSelectedPaymentMethod(method);
            selectPaymentMethodAction(dataPayment);
            self.setPaymentMethod(method, serviceFeeValue, function () {
                self.enableBtnPay();
            });
            return true;
        },
        setPaymentMethod: function (method, serviceFee, cb) {
            let self = this,
                customerId = window.checkoutConfig.customerData.id,
                data = JSON.stringify({
                    customer_id: customerId,
                    paymentMethod: method,
                    serviceFee: serviceFee
                });
            console.log(data);
            this.selectedMethod(method);
            fullScreenLoader.startLoader()
            storage.post(
                urlManager.build('rest/V1/trans-checkout/me/savepayment'),
                data,
                false
            ).done(
                function (response) {
                    let deferred = $.Deferred();

                    totals.isLoading(true);
                    // recollectShippingRates();
                    getPaymentInformation(deferred);
                    $.when(deferred).done(function () {
                        fullScreenLoader.stopLoader();
                        totals.isLoading(false);
                    });

                    fullScreenLoader.stopLoader();
                    globalVar.paymentSelected(true);

                    if (globalVar.paymentSelected() && self.canPay) {
                        $('.custom-placeorder-button').removeClass('disabled');
                        $('.custom-placeorder-button').attr('disabled', false);
                        $('.custom-placeorder-button').css('background', '#f7b500');
                        $(".accept-terms").removeClass("hidden");
                    }

                    if (typeof cb == 'function') {
                        cb();
                    }
                    let paymentList = window.checkoutConfig.paymentMethods;
                    paymentList.forEach(function (item) {
                        if (item.method == method) {
                            self.paymentDescription(item.description);
                            self.paymentTooltipDescription(item.tooltip_description);
                        }
                    });
                    return true;
                }
            ).fail(
                function (response) {
                    let error = JSON.parse(response.responseText);

                    fullScreenLoader.stopLoader();
                    messageList.addErrorMessage({
                        message: error.message
                    });

                    return false;
                }
            );
        },
        getInstalmentTerm: function (method) {
            var self = this;
            fullScreenLoader.startLoader()
            storage.post(
                urlManager.build('rest/V1/payment/get-instalment-term'),
                JSON.stringify({ customerId: window.checkoutConfig.customerData.id, paymentMethod: method }),
                false
            ).done(
                function (response) {
                    fullScreenLoader.stopLoader();
                    /**
                     * reload block totals
                     */
                    self.instalmentTerms(response);
                }
            ).fail(
                function (response) {
                    fullScreenLoader.stopLoader();
                    return false;
                }
            );
        },
        getPaymentLogo: function (method) {
            var paymentList = window.checkoutConfig.paymentMethods;
            var logo;
            paymentList.forEach(function (item) {
                if (item.method == method) {
                    if (item.logo) {
                        logo = item.logo;
                    } else {
                        logo = require.toUrl('images/svg-icons/Voucher.svg');
                    }
                }
            });
            return logo;
        }
    });
});
