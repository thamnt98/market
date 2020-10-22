define([
    'jquery',
    'Magento_Catalog/js/price-box'
], function ($) {
    'use strict';

    return function (widget) {
        $.widget('mage.SwatchRenderer', widget, {
            _RenderFormInput: function(config) {
                var $result = this._super(config);

                // Only applies to the bundle page
                if($(".bundle-options-container").length == 0 && $(".bundle-options-wrapper").length == 0 && $(".grouped-super").length == 0) {
                    return $result;
                }

                return $result.replace("super_attribute[", "super_attribute[%bundle-option-id%][%bundle-sub-id%][");
            },

            _RenderControls: function() {
                var $result = this._super();

                // Only applies to the bundle page
                if($(".bundle-options-container").length == 0 && $(".bundle-options-wrapper").length == 0 && $(".grouped-super").length == 0) {
                    return $result;
                }

                $('.super-attribute-select').each(function() {
                    $(this).attr('name', $(this).attr('name').replace('%bundle-option-id%', $(this).closest('.swatch-opt').attr('data-option-id')));

                    if(typeof($(this).closest('.swatch-opt').attr('data-sub-id')) !== 'undefined') {
                        $(this).attr('name', $(this).attr('name').replace('%bundle-sub-id%', $(this).closest('.swatch-opt').attr('data-sub-id')));
                    } else {
                        $(this).attr('name', $(this).attr('name').replace('[%bundle-sub-id%]', ''));
                    }
                });

                $('.select-link.active').trigger('click');

                return $result;
            },

            /**
             * Event for select
             *
             * @param {Object} $this
             * @param {Object} $widget
             * @private
             */
            _OnChange: function ($this, $widget) {
                // Only applies to the bundle page and grouped page
                if($(".bundle-options-container").length == 0 && $(".bundle-options-wrapper").length == 0 && $(".grouped-super").length == 0) {
                    return this._super($this, $widget);
                }

                var $parent = $this.parents('.' + $widget.options.classes.attributeClass),
                    attributeId = $parent.attr('attribute-id'),
                    $input = $parent.find('.' + $widget.options.classes.attributeInput);

                if ($widget.productForm.length > 0) {

                    if(typeof($(this).closest('.swatch-opt').attr('data-sub-id')) !== 'undefined') {
                        $input = $widget.productForm.find(
                            '.'
                            + $widget.options.classes.attributeInput
                            + '[name="super_attribute['
                            + $(this).closest('.swatch-opt').attr('data-option-id')
                            + ']['
                            + $(this).closest('.swatch-opt').attr('data-sub-id')
                            + ']['
                            + attributeId
                            + ']"]'
                        );
                    } else {
                        $input = $widget.productForm.find(
                            '.'
                            + $widget.options.classes.attributeInput
                            + '[name="super_attribute['
                            + $(this).closest('.swatch-opt').attr('data-option-id')
                            + ']['
                            + attributeId
                            + ']"]'
                        );
                    }
                }

                if ($this.val() > 0) {
                    $parent.attr('option-selected', $this.val());
                    $input.val($this.val());
                } else {
                    $parent.removeAttr('option-selected');
                    $input.val('');
                }

                $widget._Rebuild();

                // This messes up the price, aslong as simple products dont have very special pricing, this is better
                //$widget._UpdatePrice();

                $widget._loadMedia();
                $input.trigger('change');
            }
        });

        return $.mage.SwatchRenderer;
    };
});

