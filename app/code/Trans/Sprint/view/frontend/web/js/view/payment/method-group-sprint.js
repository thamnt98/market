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
        'uiElement',
        'mage/translate'
], function (Element, $t) {
    'use strict';

    var DEFAULT_GROUP_ALIAS = 'sprint';

    return Element.extend({
        defaults: {
            alias: DEFAULT_GROUP_ALIAS,
            title: $t('Sprint Asia'),
            sortOrder: 100,
            displayArea: 'payment-methods-items-${ $.alias }'
        },

        /**
         * Checks if group instance is default
         *
         * @returns {Boolean}
         */
        isDefault: function () {
            return this.alias === DEFAULT_GROUP_ALIAS;
        }
    });
});
