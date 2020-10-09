/**
 * SMCommerce
 *
 * @category    SM
 * @package     SM_GroupProduct
 *
 * Date: May, 13 2020
 * Time: 10:20 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

define([
    'jquery',
    'Magento_Swatches/js/swatch-renderer',
    'priceBox'
], function ($) {
    'use strict';

    $.widget('sm.ProductGroupSwatchRenderer', $.mage.SwatchRenderer, {
        /**
         * @private
         */
        _init: function () {
            if ($(this.element).attr('data-rendered')) {
                return;
            }

            $(this.element).attr('data-rendered', true);
            if (_.isEmpty(this.options.jsonConfig.images)) {
                this.options.useAjax = true;
                this._debouncedLoadProductMedia = _.debounce(this._LoadProductMedia.bind(this), 500);
            }

            if (this.options.jsonConfig !== '' && this.options.jsonSwatchConfig !== '') {
                // store unsorted attributes
                this.options.jsonConfig.mappedAttributes = _.clone(this.options.jsonConfig.attributes);
                this._sortAttributes();
                this._RenderControls();
                this.setDefaultSelected();
                $(this.element).trigger('swatch.initialized');
            } else {
                console.log('SwatchRenderer: No input data received');
            }
            this.options.tierPriceTemplate = $(this.options.tierPriceTemplateSelector).html();

            if (this.options.defaultQty === 0) {
                this.element.find('input').prop('disabled', true);
            }
        },

        _create: function () {
            this.productForm = this.element.parents(this.options.selectorProductTile).find('form:first');
            $(this.options.selectorProductPrice).priceBox({
                'priceConfig': {
                    'priceFormat': this.options.jsonConfig.priceFormat,
                    'prices'     : this.options.jsonConfig.prices,
                    'productId'  : this.options.jsonConfig.productId
                }
            });
        },

        /**
         * Event for select
         *
         * @param {Object} $this
         * @param {Object} $widget
         * @private
         */
        _OnChange: function ($this, $widget) {
            let $parent     = $this.parents('.' + $widget.options.classes.attributeClass),
                attributeId = $parent.attr('attribute-id'),
                $input      = $parent.find('.' + $widget.options.classes.attributeInput);

            if ($widget.productForm.length > 0) {
                $input = $widget.productForm.find(
                    '.' + $widget.options.classes.attributeInput + '[name="super_attribute[' + attributeId + ']"]'
                );
            }

            if ($this.val() > 0) {
                $parent.attr('option-selected', $this.val());
                $input.val($this.val());
            } else {
                $parent.removeAttr('option-selected');
                $input.val('');
            }

            $widget._Rebuild();
            $widget._UpdatePrice();
            $input.trigger('change');
        },


        /**
         * Event for swatch options
         *
         * @param {Object} $this
         * @param {Object} $widget
         * @private
         */
        _OnClick: function ($this, $widget) {
            let $parent     = $this.parents('.' + $widget.options.classes.attributeClass),
                $wrapper    = $this.parents('.' + $widget.options.classes.attributeOptionsWrapper),
                $label      = $parent.find('.' + $widget.options.classes.attributeSelectedOptionLabelClass),
                attributeId = $parent.attr('attribute-id'),
                $input      = $parent.find('.' + $widget.options.classes.attributeInput);

            if ($widget.inProductList) {
                $input = $widget.productForm.find(
                    '.' + $widget.options.classes.attributeInput + '[name="super_attribute[' + attributeId + ']"]'
                );
            }

            if ($this.hasClass('disabled')) {
                return;
            }

            if ($this.hasClass('selected')) {
                $parent.removeAttr('option-selected').find('.selected').removeClass('selected');
                $input.val('');
                $label.text('');
                $this.parent().parent().find('.active').removeClass('active');
                $this.attr('aria-checked', false);
            } else {
                $parent.attr('option-selected', $this.attr('option-id')).find('.selected').removeClass('selected');
                $this.parent().parent().find('.active').removeClass('active');
                $label.text($this.attr('option-label'));
                $input.val($this.attr('option-id'));
                $input.attr('data-attr-name', this._getAttributeCodeById(attributeId));
                $this.addClass('selected');
                $this.parent().addClass('active');
                $widget._toggleCheckedAttributes($this, $wrapper);
            }

            $widget._Rebuild();
            $widget._UpdatePrice();

            $(document).trigger(
                'updateMsrpPriceBlock',
                [
                    _.findKey($widget.options.jsonConfig.index, $widget.options.jsonConfig.defaultValues),
                    $widget.options.jsonConfig.optionPrices
                ]
            );

            $widget.updateStorePickup();

            $input.trigger('change');
        },

        /**
         * Event listener
         *
         * @private
         */
        _EventListener: function () {
            let $widget = this,
                options = this.options.classes,
                target;

            $widget.element.on('click', '.wrapper-option-config', function () {
                return $widget._OnClick($(this).find('.' + options.optionClass), $widget);
            });

            $widget.element.on('change', '.' + options.selectClass, function () {
                return $widget._OnChange($(this), $widget);
            });

            $widget.element.on('click', '.' + options.moreButton, function (e) {
                e.preventDefault();

                return $widget._OnMoreClick($(this));
            });

            $widget.element.on('keydown', function (e) {
                if (e.which === 13) {
                    target = $(e.target);

                    if (target.is('.' + options.optionClass)) {
                        return $widget._OnClick(target, $widget);
                    } else if (target.is('.' + options.selectClass)) {
                        return $widget._OnChange(target, $widget);
                    } else if (target.is('.' + options.moreButton)) {
                        e.preventDefault();

                        return $widget._OnMoreClick(target);
                    }
                }
            });
        },

        _getSelectedAttributes: function () {
            return {};
        },

        _UpdatePrice: function () {
            this._super();

            let newPrice  = this._getNewPrices(),
                $qtyInput = $('input[name="super_group[' + this.options.jsonConfig.productId + ']"]'),
                price     = 0;

            if (newPrice && newPrice.finalPrice && newPrice.finalPrice.amount) {
                price = newPrice.finalPrice.amount;
            }

            $($qtyInput).data('item-price', price);
            $($qtyInput).trigger('change');
        },

        updateStorePickup: function () {
            let $storePickup = $('#store-pickup-popup-' + this.options.jsonConfig.productId),
                $options     = $('.swatch-opt[data-option-id=' + this.options.jsonConfig.productId + '] .swatch-attribute'),
                productAttr  = {},
                productId    = null;

            $.each($options, function (key, item) {
                productAttr[$(item).attr('attribute-id')] = $(item).attr('option-selected');
            });

            $storePickup.find('ul').hide();
            if (JSON.stringify(productAttr) === JSON.stringify({})) {
                $storePickup.find('ul[data-option-id=0]').show();
            } else {
                $.each(this.options.jsonConfig.index, function (id, value) {
                    if (JSON.stringify(productAttr) === JSON.stringify(value)) {
                        productId = id;
                        return false;
                    }
                });

                if (productId) {
                    $storePickup.find('ul[data-option-id=' + productId + ']').show();
                } else {
                    $storePickup.find('ul[data-option-id=0]').show();
                }
            }
        },

        setDefaultSelected: function () {
            if (!this.options['defaultProduct']) {
                return;
            }

            let self        = this,
                $attributes = $('.swatch-opt[data-option-id=' + self.options.jsonConfig.productId + '] .swatch-attribute');

            $.each($attributes, function (key, attribute) {
                let attrId   = $(attribute).attr('attribute-id'),
                    $options = $(attribute).find('.swatch-option');

                $.each($options, function (key, option) {
                    let optionId   = $(option).attr('option-id'),
                        productIds = self.getOptionProducts(attrId, optionId);

                    console.log(self.options['defaultProduct']);
                    console.log(productIds);
                    if (productIds && productIds.indexOf(self.options['defaultProduct']) > -1) {
                        $(option).trigger('click');
                    }
                });
            });
        },

        getAttributeData: function (id) {
            let data = this.options.jsonConfig.attributes,
                result;

            $.each(data, function (key, item) {
                if (item.id == id) {
                    result = item;

                    return false;
                }
            });

            return result;
        },

        getOptionProducts: function (attributeId, optionId) {
            let attrData = this.getAttributeData(attributeId),
                result;

            if (!attrData['options']) {
                return null;
            }

            $.each(attrData['options'], function (key, option) {
                if (option.id == optionId) {
                    result = option.products;

                    return false;
                }
            });

            return result;
        },

        /**
         * @override
         * @param oldPrice
         * @param finalPrice
         * @private
         */
        _updateBadgeDiscount: function (oldPrice, finalPrice) {}
    });

    return $.sm.ProductGroupSwatchRenderer;
});

