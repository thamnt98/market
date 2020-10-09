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

define([
    'jquery',
    'underscore',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/action/get-payment-information'
], function(
    $,
    _,
    ko,
    Component,
    checkoutData,
    stepNavigator,
    getPaymentInformation
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Trans_Sprint/payment/custom-placeorder-button',
        },

        isError: ko.observable(false),
        isButtonShow: ko.observable(true),

        initialize: function() {
            var self = this;

            this._super();
        },

        isActive: function() {
            return true;
        },

        /**
         * Process to place order
         *
         */
        customPlaceOrderAction: function() {
            var self = this,
                paymentMethod,
                target,
                selected = $('input[type="radio"][name="payment[method]"]:checked').val(),
                tenorVal = $('input[name="sprint_term_channelid"]:checked');

            paymentMethod = checkoutData.getSelectedPaymentMethod();

            // validate payment method
            this.isError(false);

            // if ($('input[type="radio"][name="payment_group"]:checked').val() == undefined) {
            //     this.isError(true);
            // }

            if ($('input[type="radio"][name="payment[method]"]:checked').val() == undefined) {
                this.isError(true);
            } else {
                // validate installment term
                // if ($('select#' + selected + '_term_channelid').length > 0 && $('select#' + selected + '_term_channelid').val() == "") {
                if (tenorVal.length > 0 && tenorVal.val() == "") {
                    this.isError(true);
                } else if ($('select#' + selected + '_tenor').length > 0 && $('select#' + selected + '_tenor').val() == "") {
                    this.isError(true);
                }

                // validate klikbca user id
                if ($('input[type="text"]#' + selected + '_userid').length > 0 && $('input[type="text"]#' + selected + '_userid').val() == "") {
                    this.isError(true);
                }
            }

            if (!this.isError()) {
                target = $('#placeOrder-' + paymentMethod);
                target.trigger('click');
            }
        }
    });
});