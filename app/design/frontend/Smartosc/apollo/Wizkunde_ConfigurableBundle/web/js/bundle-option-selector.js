
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'priceUtils',
    'mage/translate',
    'underscore',
    'jquery/ui',
    'jquery/jquery.parsequery'
], function ($, priceUtils, $t, _) {
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
            $widget.getInputOptionClickEvent();

            $("body").on('DOMSubtreeModified', "span.price-configured_price", function() {
                $('span.price-final_price').html($('span.price-configured_price').html());
            });

            // Trigger a first click to ensure everything is properly handled
            $('.select-images:not(.multi-select) a.bunselect-link.active:visible', '[data-role=select-option-' + $widget.options.optionId + ']').trigger('click');

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
                    inputSelector.prop('checked', $(this).hasClass('active') === false);
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
                var inputVal = $(inputSelector).val();
                var optionVal = $(inputSelector).attr('selection-id');
                $('#bundle-option-config-' + inputVal).val(optionVal);

                $(this).toggleClass('active');
                $(inputSelector).trigger('change');
            });
        },

        setOnInputChangeEvents: function() {
            var $widget = this;

            $('[data-role=select-option-' + $widget.options.optionId + ']').on('change', "input,select", function(event) {

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
        },

        getInputOptionClickEvent: function () {
            var $widget = this;
            $('.field.option .control').on('click', ".wrapper-option-config", function(event) {
                let oldPrice = 0;
                let finalPrice = 0;
                var discPercentEl = $('.disc-percent'),
                    childIds = [];
                $.each($('.wrapper-option-config.active').find('.option-data'), function (key, value) {
                    if (childIds.indexOf(parseInt($(value).attr('childid'))) === -1) {
                        oldPrice = oldPrice + parseFloat($(value).attr('oldprice'));
                        finalPrice = finalPrice + parseFloat($(value).attr('finalprice'));
                    }

                    childIds.push(parseInt($(value).attr('childid')));
                });
                //caculate price
                if (oldPrice != '' && finalPrice != '' && finalPrice < oldPrice) {
                    var originalPriceFormated = 0,
                        badgeDiscountEl = $('.badge-discount-pdp'),
                        oriPriceEl = $('.original-price'),
                        discount = 0;

                    var rgx = /(\d+)(\d{3})/;
                    var oldPriceText = oldPrice.toString();
                    while (rgx.test(oldPriceText)) {
                        oldPriceText = oldPriceText.replace(rgx, '$1' + '.' + '$2');
                    }
                    discPercentEl.show();
                    discPercentEl.parent().removeClass('no-percent-discount');
                    var priceText = oriPriceEl.text();
                    priceText = priceText.replace(/ /g, ' %');
                    priceText = priceText.replace(/\d+/g, '');
                    priceText = priceText.split('.').join("");
                    priceText = priceText.split(',').join("");
                    originalPriceFormated = priceText.replace(/%/g, oldPriceText);

                    if (oldPrice > finalPrice) {
                        discount = (oldPrice - finalPrice) * 100 / oldPrice;
                        if (discount > 0) {
                            badgeDiscountEl.empty();
                            badgeDiscountEl.text($t('Disc: %1 %').replace('%1', Math.round(discount.toFixed(2))));
                        }

                        oriPriceEl.empty();
                        oriPriceEl.text($t(originalPriceFormated));
                    }
                } else {
                    discPercentEl.hide();
                    discPercentEl.parent().addClass('no-percent-discount');
                }
            });
        }
    });

    return $.mage.bundleOptionSelector;
});
