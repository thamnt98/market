/**
 *
 */

define([
    'jquery',
    'Magento_Customer/js/customer-data',
    'mage/translate'
], function ($, customerData) {
    'use strict';

    $.widget('otp.verification', {
        options: {
            /*General*/
            formLoginUrl: '',
            /*Email*/
            forgotFormUrl: '',
            /*Create*/
            createFormUrl: ''
        },

        /**
         * @private
         */
        _create: function () {
            var self = this;
        }
    });

    return $.otp.verification;
});
