/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'Magento_Checkout/js/model/totals',
    'uiComponent',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/model/quote',
    'SM_Checkout/js/view/cart-items/current-items',
    'SM_Checkout/js/view/global-observable',
    'mage/url'
], function ($, ko, totals, Component, stepNavigator, quote, currentItemsData, globalVar, urlBuilder) {
    'use strict';

    var useQty = window.checkoutConfig.useQty;

    return Component.extend({
        defaults: {
            template: 'Magento_Checkout/summary/cart-items'
        },
        totals: totals.totals(),
        items: ko.observable([]),
        maxCartItemsToDisplay: window.checkoutConfig.maxCartItemsToDisplay,
        cartUrl: window.checkoutConfig.cartUrl,
        currentItems: currentItemsData.getCurrentItems(),

        /**
         * @deprecated Please use observable property (this.items())
         */
        getItems: totals.getItems(),

        /**
         * Returns cart items qty
         *
         * @returns {Number}
         */
        getItemsQty: function () {
            return parseFloat(this.totals['items_qty']);
        },

        /**
         * Returns count of cart line items
         *
         * @returns {Number}
         */
        getCartLineItemsCount: function () {
            return parseInt(totals.getItems()().length, 10);
        },

        /**
         * Returns shopping cart items summary (includes config settings)
         *
         * @returns {Number}
         */
        getCartSummaryItemsCount: function () {
            return useQty ? this.getItemsQty() : this.getCartLineItemsCount();
        },

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super();
            let self = this,
                count = 0;
            // Set initial items to observable field
            this.setItems(totals.getItems()());

            $.each(totals.getItems()(), function (index, item) {
                count += item.qty;
                self.currentItems.push(item.item_id.toString());
                currentItemsData.setCurrentItemsData(item.item_id, item.qty, item.row_total);
            });
            currentItemsData.setCountItems(count);

            // Subscribe for items data changes and refresh items in view
            totals.getItems().subscribe(function (items) {
                let countUpdate = 0;
                self.currentItems.removeAll();
                $.each(items, function (index, item) {
                    countUpdate += item.qty;
                    self.currentItems.push(item.item_id.toString());
                    currentItemsData.setCurrentItemsData(item.item_id, item.qty, item.price * item.qty);
                });
                currentItemsData.setCountItems(countUpdate);
                if (Object.keys(items).length == 0) {
                    globalVar.disableGoPaymentButton(true);
                    setTimeout(
                    function()
                    {
                        window.location.href = urlBuilder.build("checkout/cart");
                    }, 5000);

                }
            }.bind(this));

        },

        /**
         * Set items to observable field
         *
         * @param {Object} items
         */
        setItems: function (items) {
            if (items && items.length > 0) {
                items = items.slice(parseInt(-this.maxCartItemsToDisplay, 10));
            }
            this.items(items);
        },

        /**
         * Returns bool value for items block state (expanded or not)
         *
         * @returns {*|Boolean}
         */
        isItemsBlockExpanded: function () {
            return quote.isVirtual() || stepNavigator.isProcessed('shipping');
        },

        isDisable: function (itemId) {
            if (this.currentItems.indexOf(itemId.toString()) === -1) {
                return true;
            }
            return false;
        }
    });
});
