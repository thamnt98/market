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
        'Magento_Ui/js/model/messageList'
    ],
    function(Component, $, url, alert, checkout, loader, messageList) {
        'use strict';

        return Component.extend({
            paymentMethodLogo: window.checkoutConfig.payment.config.logo,
            disabledMethods: window.checkoutConfig.payment.disabledMethods,

            getPaymentLogo: function(paymentCode) {
                var self = this,
                    logoarray = self.paymentMethodLogo,
                    logo = '';

                $.each(logoarray, function(index, value) {
                    if (paymentCode == index) {
                        logo = value;
                    }
                });

                return logo;
            },

            isMethodDisabled: function(paymentCode) {
                var self = this,
                    disabledArray = self.disabledMethods,
                    result = false;

                $.each(disabledArray, function(index, value) {
                    $.map(value, function(data, key) {
                        if (key === paymentCode) {
                            result = true;
                        }
                    });
                });

                return result;
            }
        });
    }
);