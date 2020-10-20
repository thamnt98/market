/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiComponent',
    'SM_Checkout/js/view/global-observable',
    'mage/translate'
], function (Component, globalVar, $t) {
    'use strict';

    var quoteData = window.checkoutConfig.quoteData;

    return Component.extend({
        isStepShipping: globalVar.isStepShipping,
        hasFreshItem: !!quoteData.has_fresh_item,
        getTooltip: quoteData.fresh_tooltip,

        placeHolder: function () {
            return $t('Type in your address');
        }
    });
});
