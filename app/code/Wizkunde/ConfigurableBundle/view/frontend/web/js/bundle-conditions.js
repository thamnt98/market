/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'underscore',
    'jquery/ui',
    'jquery/jquery.parsequery'
], function ($, _) {
    'use strict';

    $.widget('mage.bundleConditions', {
        options: {
            cascade: 0
        },

        /**
         * @private
         */
        _init: function cascadeBundleQty() {
            if (this.options.cascade == 1) {
                $('.bundle-options-wrapper input.input-text.qty:visible').attr('readonly', true);

                $('.super-attribute-input').on('keyup', function (key) {
                    var totalQty = 0;
                    $('.product-add-form tbody:first-of-type .super-attribute-input').each(function (item) {
                        if ($.isNumeric($(this).val()) === true && $(this).val() > 0) {
                            totalQty += parseInt($(this).val());
                        }
                    });
                    $('.bundle-options-wrapper input.input-text.qty:visible').val(totalQty);
                    $('.bundle-options-wrapper input.input-text.qty:visible').trigger('keyup');
                });
            }

        },
    });

    return $.mage.bundleConditions;
});
