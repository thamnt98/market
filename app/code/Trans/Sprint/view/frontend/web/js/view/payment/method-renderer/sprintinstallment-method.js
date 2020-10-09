/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
define(
    [
        'Magento_Checkout/js/view/payment/default',
        'jquery',
        'mage/url',
        'Magento_Ui/js/modal/alert',
        'Magento_Checkout/js/checkout-data',
        'mage/loader',
        'Magento_Ui/js/model/messageList',
        'mage/translate',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/action/select-payment-method',
        'Trans_Sprint/js/action/set-service-fee'
    ],
    function(Component, $, url, alert, checkoutData, loader, messageList, $t, quote, priceUtils, selectPaymentMethodAction, setServiceFee) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Trans_Sprint/payment/creditCard'
            },
            redirectAfterPlaceOrder: false,

            initialize: function() {
                this._super();
            },

            afterPlaceOrder: function() {
                var redirectUrl = url.build('sprint/payment/authorization');

                $.ajax({
                    type: 'post',
                    showLoader: true,
                    url: redirectUrl,
                    cache: false,
                    success: function(data) {
                        var status = data.insertStatus;

                        if (status == '00') {
                            var sprintDirect = data.redirectURL;
                            window.location.replace(sprintDirect);
                        }

                        var message = $t('Connection error. Please retry again later.');

                        if (data === false) {
                            messageList.addErrorMessage({
                                message: message
                            });
                        } else {
                            if (status != '00') {
                                messageList.addErrorMessage({
                                    message: message
                                });

                                alert({
                                    title: 'Error',
                                    content: message,
                                    actions: {
                                        always: function() {}
                                    }
                                });
                                window.location.replace(url.build('checkout/onepage/failure'));
                            }
                        }
                    } //end of ajax success
                });
            },

            /**
             * @return array
             */
            getTermList: function(method) {
                var installment = window.checkoutConfig.payment.sprint.installmentTerms;
                var term = '';
                $.each(installment, function(index, value) {
                    $.map(value, function(data, key) {
                        if (key === method) {
                            $.each(data, function(ind, val) {});
                            term = JSON.stringify(data);
                        }
                    });
                });

                var tenor = JSON.parse(term);
                return tenor;
            },

            /**
             * @return array
             */
            getData: function(tenor, serviceFeeValue) {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'tenor': tenor,
                        'serviceFeeValue': serviceFeeValue
                    }
                };
            },

            /**
             * @return {Boolean}
             */
            selectTenor: function(parent, tenor, serviceFeeValue) {
                if($('input[name="sprint_term_channelid"]:checked').val()) {
                    selectPaymentMethodAction(this.getData(tenor, serviceFeeValue));
                    checkoutData.setSelectedPaymentMethod(this.item.method);
                    setServiceFee(serviceFeeValue);
                }

                // return true;
            }
        });
    }
);