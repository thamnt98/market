/*
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

define([
    'jquery',
    'ko',
    'mage/url',
    'Magento_Customer/js/customer-data',
    'jquery-ui-modules/widget',
    'mage/translate',
    'mage/validation',
    'domReady!'
], function ($, ko, urlBuilder, customerData) {
    'use strict';

    $.widget('inspireme.relatedProducts', {
        options: {
            products   : '.product-add-to-cart',
            qtyBlock   : '.qty-block',
            increaseQty: '.increase-cart-item',
            decreaseQty: '.decrease-cart-item',
            fakeToCart : '.ingredient-button',
            allToCart  : '#add-selected-tocart',
            totalQty   : 0,
            minicartSelector: '[data-block="minicart"]',
        },

        /**
         * Initialize.
         * @private
         */
        _init: function () {
            let self = this;

            self.options.totalQty = 0;
            $(this.options.products).each(function () {
                let qtyField = self._getQuantityField(this);
                self.options.totalQty += self._getQuantityValue(qtyField);
            });
            self._updateTotalToCart();
        },

        /**
         * Bind a click handler to the widget's context element.
         * @private
         */
        _create: function () {
            this._bind($(this.options.increaseQty), 1);
            this._bind($(this.options.decreaseQty), -1);
            this._bind($(this.options.fakeToCart), 0);
            this._bindInputChange();
            this._bindSubmit();
        },

        /**
         * Element click handler update quantity value
         * @param element
         * @param updateVal
         * @private
         */
        _bind: function (element, updateVal) {
            let self = this;

            if (updateVal === 0) {
                element.each(function () {
                    $(this).click(function (event) {
                        event.preventDefault();
                        let parentNode = $(this).parent(),
                            qtyField   = self._getQuantityField(parentNode);

                        qtyField.val(1);
                        parentNode.find(self.options.qtyBlock).css('display', '');
                        $(this).css('display', 'none');

                        self.options.totalQty += 1;
                        self._updateTotalToCart();
                    })
                });
            } else {
                element.each(function () {
                    $(this).click(function (event) {
                        event.preventDefault();
                        let parentNode = $(this).parent(),
                            qtyField   = self._getQuantityField(parentNode),
                            qtyVal     = self._getQuantityValue(qtyField);

                        if ((qtyVal + updateVal) >= 0) {
                            qtyField.val(qtyVal + updateVal);
                            self.options.totalQty += updateVal;
                        } else {
                            qtyField.val(0);
                        }

                        if (qtyField.val() <= 0) {
                            parentNode.css('display', 'none');
                            parentNode.parent().find(self.options.fakeToCart).css('display', '');
                        }

                        self._updateTotalToCart();
                    })
                });
            }
        },

        /**
         * Input field on key up
         * @private
         */
        _bindInputChange: function () {
            let self = this;

            $(self.options.products).each(function () {
                let qtyField = self._getQuantityField($(this));
                qtyField.keyup(function () {
                    self._init();
                })
            });
        },

        /**
         * Submit add all selected to cart
         * @private
         */
        _bindSubmit: function () {
            let self = this;

            $(self.options.allToCart).click(function () {
                let isValid = true;

                $(self.options.products).each(function () {
                    let qtyField = self._getQuantityField($(this));

                    if (!self._validateQty(qtyField)) {
                        isValid = false;
                    }
                });

                if (isValid) {
                    let url      = urlBuilder.build('rest/V1/blog/addSelectedToCart'),
                        dataPost = {
                            'products' : [],
                            'form_key' : ''
                        };

                    $(self.options.products).each(function () {
                        let qtyField = self._getQuantityField($(this)),
                            qtyVal   = self._getQuantityValue(qtyField);

                        if (qtyVal > 0) {
                            let form = $(this).find('form');
                            dataPost['products'].push({
                                'product_id'  : $(form).find("input[name=product]").val(),
                                'product_qty' : $(form).find("input[name=qty]").val()
                            });
                        }
                    });

                    dataPost['form_key'] = $.mage.cookies.get('form_key');

                    $(self.options.minicartSelector).trigger('contentLoading');
                    $(self.options.allToCart).text($.mage.__('Adding...')).addClass('disabled');

                    $.ajax({
                        type    : "POST",
                        url     : url,
                        data    : JSON.stringify(dataPost),
                        dataType: "json",
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('Accept', 'application/json');
                            xhr.setRequestHeader('Content-Type', 'application/json');
                        },
                        success: function (response) {
                            $(self.options.minicartSelector).trigger('contentUpdated');
                            customerData.invalidate(['cart']);
                            customerData.reload(['cart'], true);
                            self._enableAddToCartButton();
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            $(self.options.minicartSelector).trigger('contentUpdated');
                            customerData.invalidate(['cart']);
                            customerData.reload(['cart'], true);
                            self._enableAddToCartButton();
                        }
                    })
                }

            });
        },

        /**
         * Enable button add to cart
         * @private
         */
        _enableAddToCartButton: function () {
            let self = this,
                addToCartButton = $(self.options.allToCart);

            addToCartButton.removeClass('disabled').text($.mage.__('Added'));

            setTimeout(function () {
                self._updateTotalToCart();
            }, 3000);
        },

        /**
         * Validate quantities field
         * @param element
         * @return {jQuery|boolean}
         * @private
         */
        _validateQty: function (element) {
            if (parseInt($(element).val()) !== 0) {
                return $(element).valid();
            }
            return true;
        },

        /**
         * Get each product input field
         * @param element
         * @return {jQuery|[]}
         * @private
         */
        _getQuantityField: function (element) {
            return $(element).find("input[type=number]");
        },

        /**
         * Get each product input value
         * @param quantityField
         * @return {number}
         * @private
         */
        _getQuantityValue: function (quantityField) {
            return (quantityField.val()) ?
                parseInt(quantityField.val()) > 0 ? parseInt(quantityField.val()) : 0
                : 0;
        },

        /**
         * Update value total add to Cart
         * @private
         */
        _updateTotalToCart: function () {
            if (this.options.totalQty <= 0) {
                $(this.options.allToCart).css('display', 'none');
            } else {
                $(this.options.allToCart).css('display', '');
            }

            if (this.options.totalQty > 1) {
                $(this.options.allToCart).text($.mage.__('Add %1 items to cart').replace('%1', this.options.totalQty));
            } else {
                $(this.options.allToCart).text($.mage.__('Add %1 item to cart').replace('%1', this.options.totalQty));
            }
        },
    });

    return $.inspireme.relatedProducts;
});

