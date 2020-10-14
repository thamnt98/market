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
        // 'Trans_Sprint/js/view/payment/method-renderer/sprint-core',
        'Magento_Checkout/js/view/payment/default',
        'jquery',
        'mage/url',
        'Magento_Ui/js/modal/alert',
        'Magento_Checkout/js/checkout-data',
        'mage/loader',
        'Magento_Ui/js/model/messageList',
        'mage/translate'
    ],
    function(Component, $, url, alert, checkout, loader, messageList, $t) {
        'use strict';
        
        return Component.extend({
            defaults: {
                template: 'Trans_Sprint/payment/core'
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

                        var message = $t('Connection error. Please retry again later.');

                        if (data === false) {
                            messageList.addErrorMessage({
                                message: message
                            });
                        }
                        
                        if (status == '00') {

                            var afterPlaceOrder = url.build('checkout/onepage/success');

                            if (data.redirectURL) {
                                var afterPlaceOrder = data.redirectURL;
                            }
                            
                            window.location.replace(afterPlaceOrder);

                        } else {
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
                    } //end of ajax success
                });
            }
        });
    }
);