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
    'ko',
    'mage/translate',
    'uiRegistry',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils'
], function($, ko, $t, registry, quote, priceUtils) {
    'use strict';

    return function(customList) {
        return customList.extend({
            defaults: {
                template: 'Trans_Sprint/payment-methods/list',
            },

            /**
             * Open payment method wrap per group
             *
             * @param {String} groupAlias
             */
            openPaymentMethodGroup: function(groupAlias) {
                $('.payment-method-group').hide()

                if ($('#payment_group_radio_' + groupAlias).val() === groupAlias) {
                    $('#payment-method-group-' + groupAlias).show();
                    $('#payment-method-group-' + groupAlias).find('.payment-method-title').each(function() {
                        var title = $(this);
                        var label = title.find('label');
                        var paragraph = title.find('p');
                        if (paragraph.length == 0 && label.length > 0) {
                            var code = label.attr('for');
                        }
                    });
                }
            },

            /**
             * Returns payment group title
             *
             * @param {Object} group
             * @returns {String}
             */
            getGroupTitle: function(group) {
                var title = group().title,
                    CONFIG_GROUPS_LABEL_ARRAY = window.checkoutConfig.payment.group.label_array;

                if (group().isDefault() && this.paymentGroupsList().length > 1) {
                    title = this.defaultGroupTitle;
                }

                var paymentGroup = CONFIG_GROUPS_LABEL_ARRAY[group().alias];

                if (paymentGroup) {
                    if (paymentGroup.label) {
                        title = paymentGroup.label;
                    }
                }

                return title + ':';
            },
        });
    };
});