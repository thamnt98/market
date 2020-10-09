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
    'mage/utils/wrapper'
], function($, wrapper) {
    'use strict';

    return function(placeOrderModel) {

        /** Override default place order model to request */
        return wrapper.wrap(placeOrderModel, function(originalAction, serviceUrl, payload, messageContainer) {
            var paymentData = payload.paymentMethod;
            var paymentMethod = paymentData.method;

            // var tenorVal = $('#' + paymentMethod + '_term_channelid').val();
            var tenorVal = $('input[name="sprint_term_channelid"]:checked').val();

            if (paymentData['extension_attributes'] === undefined) {
                paymentData['extension_attributes'] = {};
            }

            if (tenorVal) {
                paymentData['extension_attributes']['sprint_term_channelid'] = tenorVal;
            }

            return originalAction(serviceUrl, payload, messageContainer);
        });
    };
});