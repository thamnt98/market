
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

    $.widget('mage.bundleOptionSelector', {
        options: {
            optionId: 0
        },

        /**
         * @private
         */
        _create: function createBundleOptionSelector() {
            var $widget = this;

            $widget.setOnImageClickEvents();
            $widget.setOnInputChangeEvents();

            $("body").on('DOMSubtreeModified', "span.price-configured_price", function() {
                $('span.price-final_price').html($('span.price-configured_price').html());
            });

            // Trigger a first click to ensure everything is properly handled
            $('.select-images:not(.multi-select) a.select-link.active:visible', '[data-role=select-option-' + $widget.options.optionId + ']').trigger('click');

            $widget.resetOptionEvents();
        },

        setOnImageClickEvents: function() {
            var $widget = this,
                inputSelector,
                optionSelector;

            $('[data-role=select-option-' + $widget.options.optionId + ']').on('click', ".select-link", function(event) {
                // If its a click on an already active element but there's no multi select,  ignore it
                if($(this).hasClass('multi-select') === false && $(this).hasClass('active')) {
                    return;
                }

                if($(this).hasClass('multi-select') === false) {
                    $('[data-role=select-option-' + $widget.options.optionId + '] .select-link').removeClass('active');
                }

                inputSelector = $('#bundle-option-' + $widget.options.optionId + '-' + $(this).attr('data-selection-id'));

                if(inputSelector.length) {
                    inputSelector.filter('[value=' + $(this).attr('data-selection-id') + ']').prop('checked', $(this).hasClass('active') === false);
                    $(inputSelector).trigger('change');
                } else {
                    inputSelector = $('#bundle-option-' + $widget.options.optionId);

                    if($(inputSelector).prop('multiple')) {
                        optionSelector = $('#bundle-option-' + $widget.options.optionId + ' option[value=' + $(this).attr('data-selection-id') + ']');
                        optionSelector.prop('selected', $(this).hasClass('active') === false);
                    } else {
                        $(inputSelector).val($(this).attr('data-selection-id'));
                    }
                }

                $(this).toggleClass('active');
                $(inputSelector).trigger('change');
            });
        },

        setOnInputChangeEvents: function() {
            var $widget = this;

            $('[data-role=select-option-' + $widget.options.optionId + ']').on('change', "input,select", function(event) {
                $('[data-role=data-option-' + $widget.options.optionId + ']').removeClass('visible-selection');

                $('.checkbox:checked, .bundle-option-radio:checked, .bundle-option-select option:selected', '[data-role=select-option-' + $widget.options.optionId + ']').each(function() {
                    $('[data-role=data-option-' + $widget.options.optionId + '][data-selection-id="' + $(this).val() + '"]').addClass('visible-selection');
                });

                $widget.resetOptionEvents();
            });
        },

        resetOptionEvents: function() {
            // Disable everything
            $('.bundle-selection-data:not(.visible-selection) .super-attribute-select').prop('disabled', 'disabled');
            $('.bundle-selection-data:not(.visible-selection) .product-custom-option').prop('disabled', 'disabled');

            // Reenable visible selections
            $('.visible-selection .super-attribute-select').prop('disabled', false);
            $('.visible-selection .product-custom-option').prop('disabled', false);
        }
    });

    return $.mage.bundleOptionSelector;
});
