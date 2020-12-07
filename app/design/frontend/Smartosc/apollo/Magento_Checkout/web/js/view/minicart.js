/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'jquery',
    'ko',
    'underscore',
    'mage/url',
    'SM_GTM/js/gtm/sm-gtm-cart-collect-data',
    'sidebar',
    'mage/translate',
    'mage/dropdown',
], function (Component, customerData, $, ko, _, urlBuilder, gtm) {
    'use strict';

    var sidebarInitialized = false,
        addToCartCalls = 0,
        miniCart;

    miniCart = $('[data-block=\'minicart\']');
    updateMiniCart();

    /**
     * @return {Boolean}
     */
    function initSidebar()
    {
        if (miniCart.data('mageSidebar')) {
            miniCart.sidebar('update');
        }

        if (!$('[data-role=product-item]').length) {
            return false;
        }
        miniCart.trigger('contentUpdated');

        if (sidebarInitialized) {
            return false;
        }
        sidebarInitialized = true;
        miniCart.sidebar({
            'targetElement': 'div.block.block-minicart',
            'url': {
                'checkout': window.checkout.checkoutUrl,
                'update': window.checkout.updateItemQtyUrl,
                'remove': window.checkout.removeItemUrl,
                'loginUrl': window.checkout.customerLoginUrl,
                'isRedirectRequired': window.checkout.isRedirectRequired
            },
            'button': {
                'checkout': '#top-cart-btn-checkout',
                'remove': '#mini-cart a.action.delete',
                'close': '#btn-minicart-close',
            },
            'showcart': {
                'parent': 'span.counter',
                'qty': 'span.counter-number',
                'label': 'span.counter-label'
            },
            'minicart': {
                'list': '#mini-cart',
                'content': '#minicart-content-wrapper',
                'qty': 'div.items-total',
                'subtotal': 'div.subtotal span.price',
                'maxItemsVisible': window.checkout.minicartMaxItemsVisible
            },
            'item': {
                'qty': ':input.cart-item-qty',
                'button': ':button.update-cart-item'
            },
            'confirmMessage': $.mage.__('Are you sure you would like to remove this item from the shopping cart?')
        });
    }

    miniCart.on('dropdowndialogopen', function () {
        initSidebar();
        var hideBackground = '<div class="modals-overlay" id="background-hidden"></div>';
        $('body').after(hideBackground);
    });

    miniCart.on('dropdowndialogclose', function () {
        $('#background-hidden').remove();
    });

    return Component.extend({
        shoppingCartUrl: window.checkout.shoppingCartUrl,
        maxItemsToDisplay: window.checkout.maxItemsToDisplay,
        cart: {},

        // jscs:disable requireCamelCaseOrUpperCaseIdentifiers
        /**
         * @override
         */
        initialize: function () {
            var self = this,
                cartData = customerData.get('cart');

            this.update(cartData());
            cartData.subscribe(function (updatedCart) {
                addToCartCalls--;
                this.isLoading(addToCartCalls > 0);
                sidebarInitialized = false;
                this.update(updatedCart);
                initSidebar();
            }, this);
            $('[data-block="minicart"]').on('contentLoading', function () {
                addToCartCalls++;
                self.isLoading(true);
            });

            if (
                cartData().website_id !== window.checkout.websiteId && cartData().website_id !== undefined ||
                cartData().storeId !== window.checkout.storeId && cartData().storeId !== undefined
            ) {
                customerData.reload(['cart'], false);
            }
            return this._super();
        },
        //jscs:enable requireCamelCaseOrUpperCaseIdentifiers

        isLoading: ko.observable(false),
        initSidebar: initSidebar,

        /**
         * Close mini shopping cart.
         */
        closeMinicart: function () {
            $('[data-block="minicart"]').find('[data-role="dropdownDialog"]').dropdownDialog('close');
        },

        /**
         * @return {Boolean}
         */
        closeSidebar: function () {
            var minicart = $('[data-block="minicart"]');

            minicart.on('click', '[data-action="close"]', function (event) {
                event.stopPropagation();
                minicart.find('[data-role="dropdownDialog"]').dropdownDialog('close');
            });

            return true;
        },

        /**
         * @param {String} productType
         * @return {*|String}
         */
        getItemRenderer: function (productType) {
            return this.itemRenderer[productType] || 'defaultRenderer';
        },

        /**
         * Update mini shopping cart content.
         *
         * @param {Object} updatedCart
         * @returns void
         */
        update: function (updatedCart) {
            _.each(updatedCart, function (value, key) {
                if (!this.cart.hasOwnProperty(key)) {
                    this.cart[key] = ko.observable();
                }
                this.cart[key](value);
            }, this);
            this.updateQtyAddedToProduct();
        },

        /**
         * Get cart param by name.
         * @param {String} name
         * @returns {*}
         */
        getCartParam: function (name) {
            if (!_.isUndefined(name)) {
                if (!this.cart.hasOwnProperty(name)) {
                    this.cart[name] = ko.observable();
                }
            }

            return this.cart[name]();
        },

        /**
         * Returns array of cart items, limited by 'maxItemsToDisplay' setting
         * @returns []
         */
        getCartItems: function () {
            var items = this.getCartParam('items') || [];

            items = items.slice(parseInt(-this.maxItemsToDisplay, 10));

            return items;
        },

        /**
         * Returns count of cart line items
         * @returns {Number}
         */
        getCartLineItemsCount: function () {
            var items = this.getCartParam('items') || [];

            return parseInt(items.length, 10);
        },

        /**
         * Update quanty to product show in product list page
         * @returns void
         */
        updateQtyAddedToProduct: function () {
            var items = this.getCartParam('items') || [];
            _.each(items, function (item, key) {
                var addToCartForm = $(".page-loading [data-product-sku='" + item.product_sku + "' ]");
                if (addToCartForm.length) {
                    var updateContainer = addToCartForm.children('.update-cart-qty');
                    updateContainer.children("input[name='item_id']").val(item.item_id);
                    if (!sidebarInitialized) {
                        updateContainer.find("input[name='item_qty']").val(item.qty);
                        updateContainer.show();
                        addToCartForm.children('.action.tocart').hide();
                        if (item.qty >= item.product_stock) {
                            updateContainer.children('.increase-qty')
                                .attr('disabled', 'disabled')
                                .css("background", "grey")
                        }
                    }
                }
            });
            if (!sidebarInitialized) {
                $('.product-item-details .actions-primary').addClass('visibility-visible');
                $('.action-primary-loader').hide();
            }
        }
    });

    /**
     * Transmart advanced mini cart
     */
    function updateMiniCart()
    {
        var  typingDone,
            typingTime = 2000;

        let miniCartWrapper = $("#minicart-content-wrapper");
        miniCartWrapper.on("click", ".minicart-increase-qty", function () {

            let itemId = $(this).attr('itemId'),
                itemQty = $('#cart-item-'+ itemId),
                elementId = $('button#minicart-add-cart-item-' + itemId),
                downElementId = $('button#minicart-subtract-cart-item-' + itemId),
                itemStockQty = $(this).attr('itemStock'),
                productId = $(this).attr('productId');

            if (elementId.is('[readonly]')) {
                return this;
            }
            itemQty.val(parseInt(itemQty.val()) + 1);

            //Increase quantity GTM
            gtm.collectData('addToCart', productId, itemQty.val())

            if (parseInt(itemQty.val()) >= 99 || parseInt(itemQty.val()) >= itemStockQty) {
                elementId.css("background", "#ccc");
                elementId.attr('readonly', true);
            } else {
                downElementId.css("background", "#f7b500");
                downElementId.attr('readonly', false);
            }

            clearTimeout(typingDone);
            typingDone = setTimeout(function () {
                updateItemQty(itemId, itemQty.val());
            }, typingTime);
        });

        miniCartWrapper.on("click", ".minicart-decrease-qty", function () {
            let itemId = $(this).attr('itemId'),
                itemQty = $('#cart-item-'+ itemId),
                elementId = $('button#minicart-subtract-cart-item-' + itemId),
                plusElementId = $('button#minicart-add-cart-item-' + itemId),
                productId = $(this).attr('productId');

            if (elementId.is('[readonly]')) {
                return this;
            }

            if (itemQty.val() > 1) {
                itemQty.val(itemQty.val() - 1);
            }

            //Decrease quantity GTM
            gtm.collectData('removeFromCart', productId, itemQty.val())

            if (parseInt(itemQty.val()) <= 1) {
                elementId.css("background", "#ccc");
                elementId.attr('readonly', true);
            } else {
                plusElementId.css("background", "#f7b500");
                plusElementId.attr('readonly', false);
            }

            clearTimeout(typingDone);
            typingDone = setTimeout(function () {
                updateItemQty(itemId, itemQty.val());
            }, typingTime);
        });

        function updateItemQty(itemId, qty)
        {
            ajaxSubmit('checkout/sidebar/UpdateItemQty', {
                item_id: itemId,
                item_qty: qty
            });
        }
        /**
         * @param {String} url - ajax url
         * @param {Object} data - post data for ajax call
         */
        function ajaxSubmit(url, data)
        {
            $.ajax({
                url: urlBuilder.build(url),
                data: data,
                type: 'post',
                dataType: 'json',
                context: this,
            }).done(function (response) {
            }).fail(function (error) {
                console.log(JSON.stringify(error));
            });
        }

        miniCartWrapper.on("click", "a", function (e) {
            e.preventDefault();
            window.location.href = $(this).attr('href');
        });
    }

});
