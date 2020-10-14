/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiElement',
    'mage/translate'
], function(Element, $t) {
    'use strict';

    var DEFAULT_GROUP_ALIAS = 'default';

    return Element.extend({
        defaults: {
            alias: 'ccfullpayment',
            title: $t('Credit Card Full Payment'),
            sortOrder: 111,
            displayArea: 'payment-methods-items-${ $.alias }'
        },

        /**
         * Checks if group instance is default
         *
         * @returns {Boolean}
         */
        isDefault: function() {
            return this.alias === DEFAULT_GROUP_ALIAS;
        }
    });
});