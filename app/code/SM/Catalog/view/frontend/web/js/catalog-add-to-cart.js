/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/translate',
    'underscore',
    'Magento_Catalog/js/product/view/product-ids-resolver',
    'jquery-ui-modules/widget'
], function ($, $t, _, idsResolver) {
    'use strict';
    const $productList =  $('#amasty-shopby-product-list');
    // Start Customize
    $productList.on('click', '.action.increase-qty',function () {
        var $this = $(this),
            updateContainer = $this.parent('.update-cart-qty'),
            qty = updateContainer.find("[name='item_qty']").stop().val();

        updateContainer.find("[name='qty_type']").stop().val('1');
        if (qty >= 99) {
            $this.attr("disabled", true);
        }
        if (qty >= 98) {
            $this.css("background", "grey");
            $this.css("background-color", "grey");
        }
    });

    $productList.on('click', '.action.decrease-qty', function () {
        var $this = $(this),
            updateContainer = $this.parent('.update-cart-qty'),
            qty = updateContainer.find("[name='item_qty']").stop().val(),
            addToCarForm = $this.parents("[data-role='tocart-form']");

        updateContainer.find("[name='qty_type']").stop().val('0');
        addToCarForm = $this.parents("[data-role='tocart-form']");

        if (qty <=0 ) {
            $this.attr('disabled', 'disabled');
            $this.css("background", "grey");
        }
        if (qty <= 1) {
            updateContainer.hide();
            addToCarForm.children('.action.tocart').show();
        }
        if (qty >= 1 && qty <= 99) {
            $this.closest('.update-cart-qty').find('.action.increase-qty').css("background", "#f7b500");
            $this.closest('.update-cart-qty').find('.action.increase-qty').css("background-color", "#f7b500");
            $this.closest('.update-cart-qty').find('.action.increase-qty').prop('disabled',false);
        }
    });
    // End Customize

    $.widget('mage.catalogAddToCartAdvanced', {
        options: {
            processStart: null,
            processStop: null,
            bindSubmit: true,
            minicartSelector: '[data-block="minicart"]',
            messagesSelector: '[data-placeholder="messages"]',
            productStatusSelector: '.stock.available',
            addToCartButtonSelector: '.action.tocart',
            increaseQty: 'action.increase-qty',
            decreaseQty: '.action.decrease-qty',
            addToCartButtonDisabledClass: 'disabled',
            addToCartButtonTextWhileAdding: '',
            addToCartButtonTextAdded: '',
            addToCartButtonTextDefault: ''
        },

        /** @inheritdoc */
        _create: function () {
            if (this.options.bindSubmit) {
                this._bindSubmit();
            }
        },

        /**
         * @private
         */
        _bindSubmit: function () {
            var self = this;

            if (this.element.data('catalog-addtocart-initialized')) {
                return;
            }

            this.element.data('catalog-addtocart-initialized', 1);
            this.element.on('submit', function (e) {
                e.preventDefault();
                self.submitForm($(this));
            });
        },

        /**
         * @private
         */
        _redirect: function (url) {
            var urlParts, locationParts, forceReload;

            urlParts = url.split('#');
            locationParts = window.location.href.split('#');
            forceReload = urlParts[0] === locationParts[0];

            window.location.assign(url);

            if (forceReload) {
                window.location.reload();
            }
        },

        /**
         * @return {Boolean}
         */
        isLoaderEnabled: function () {
            return this.options.processStart && this.options.processStop;
        },

        /**
         * Handler for the form 'submit' event
         *
         * @param {jQuery} form
         */
        submitForm: function (form) {
            this.ajaxSubmit(form);
        },

        /**
         * @param {jQuery} form
         */
        ajaxSubmit: function (form) {
            var self = this,
                productIds = idsResolver(form),
                formData,
                loader = form.parent().prev('.action-primary-loader'),
                updateCartQty = form.children('.update-cart-qty');

            $(self.options.minicartSelector).trigger('contentLoading');
            //updateCartQty.hide();
            loader.show();
            self.disableAddToCartButton(form);
            formData = new FormData(form[0]);

            // Start Customize
            var itemQty = parseFloat(formData.get('item_qty')),
                updateQty = 0,
                formAction = '';
            if (itemQty) {
                if (parseInt(formData.get('qty_type'))) {
                    updateQty = itemQty + 1;
                } else {
                    updateQty = itemQty - 1;
                }
                let $qtyElement = form.find('input[name="item_qty"]').stop();
                $qtyElement.val(updateQty);
                formData.set('item_qty', updateQty.toString());
                formAction = BASE_URL + 'checkout/sidebar/updateItemQty';
            } else {
                formAction = form.attr('action');
                let $qtyElement = form.find('input[name="item_qty"]').stop();
                $qtyElement.val(1);
            }
            // End Customize
            $.ajax({
                url: formAction,
                data: formData,
                type: 'post',
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,

                /** @inheritdoc */
                beforeSend: function () {
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStart);
                    }
                },

                /** @inheritdoc */
                success: function (res) {
                    var eventData, parameters;

                    $(document).trigger('ajax:addToCart', {
                        'sku': form.data().productSku,
                        'productIds': productIds,
                        'form': form,
                        'response': res
                    });

                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStop);
                    }

                    if (res.backUrl) {
                        eventData = {
                            'form': form,
                            'redirectParameters': []
                        };
                        // trigger global event, so other modules will be able add parameters to redirect url
                        $('body').trigger('catalogCategoryAddToCartRedirect', eventData);

                        if (eventData.redirectParameters.length > 0 &&
                            window.location.href.split(/[?#]/)[0] === res.backUrl
                        ) {
                            parameters = res.backUrl.split('#');
                            parameters.push(eventData.redirectParameters.join('&'));
                            res.backUrl = parameters.join('#');
                        }

                        self._redirect(res.backUrl);
                        return;
                    }

                    if (res.messages) {
                        $(self.options.messagesSelector).html(res.messages);
                    }

                    if (res.minicart) {
                        $(self.options.minicartSelector).replaceWith(res.minicart);
                        $(self.options.minicartSelector).trigger('contentUpdated');
                    }

                    if (res.product && res.product.statusText) {
                        $(self.options.productStatusSelector)
                            .removeClass('available')
                            .addClass('unavailable')
                            .find('span')
                            .html(res.product.statusText);
                    }
                    self.enableAddToCartButton(form);
                    // Start Customize
                    if (!itemQty) {
                        if(form.data('custom') != "nothide"){
                            form.children('.action.tocart').hide();
                        }
                        updateCartQty.show();
                    }

                    if (res.success === false && (res.qty || res.qty == 0)) {
                        self.disableIncreaseButton(form, res.qty);
                    } else {
                        if (res.qty === updateQty) {
                            self.disableIncreaseButton(form, res.qty);
                        }
                    }
                    loader.hide();
                    // End Customize

                },

                /** @inheritdoc */
                error: function (res) {
                    $(document).trigger('ajax:addToCart:error', {
                        'sku': form.data().productSku,
                        'productIds': productIds,
                        'form': form,
                        'response': res
                    });
                },

                /** @inheritdoc */
                complete: function (res) {
                    if (res.state() === 'rejected') {
                        location.reload();
                    }
                }
            });
        },

        disableIncreaseButton: function (form, qty) {
            let $increaseButton = form.find('.increase-qty').stop();
            $increaseButton.attr('disabled', 'disabled');
            $increaseButton.css("background", "grey");

            if (typeof $qtyElement != "undefined") {
                $qtyElement.val(qty);
            } else {
                form.find('input[name="item_qty"]').stop().val(qty);
            }
        },

        /**
         * @param {String} form
         */
        disableAddToCartButton: function (form) {
            var addToCartButtonTextWhileAdding = this.options.addToCartButtonTextWhileAdding || $t('Updating...'),
                addToCartButton = $(form).find(this.options.addToCartButtonSelector);

            addToCartButton.addClass(this.options.addToCartButtonDisabledClass);
            addToCartButton.find('span').text(addToCartButtonTextWhileAdding);
            addToCartButton.attr('title', addToCartButtonTextWhileAdding);
        },

        /**
         * @param {String} form
         */
        enableAddToCartButton: function (form) {
            var addToCartButtonTextAdded = this.options.addToCartButtonTextAdded || $t('Updated'),
                self = this,
                addToCartButton = $(form).find(this.options.addToCartButtonSelector);

            addToCartButton.find('span').text(addToCartButtonTextAdded);
            addToCartButton.attr('title', addToCartButtonTextAdded);

            setTimeout(function () {
                var addToCartButtonTextDefault = self.options.addToCartButtonTextDefault || $t('Add to Cart');

                addToCartButton.removeClass(self.options.addToCartButtonDisabledClass);
                addToCartButton.find('span').text(addToCartButtonTextDefault);
                addToCartButton.attr('title', addToCartButtonTextDefault);
            }, 1000);
        }
    });

    return $.mage.catalogAddToCart;
});
