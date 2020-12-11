/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'Magento_Customer/js/customer-data',
], function ($, _, customerData) {
    'use strict';
    $.widget('mage.updateQtyAddedToProduct', {
        // Optional
        options: {
            ajax: false
        },

        /** @inheritdoc */
        _create: function () {
            if (this.options.ajax === true) {
                const actionPrimary = this.element,
                    addToCartForm = this.element.children("form[data-role='tocart-form']");
                if (addToCartForm.length) {
                    const items = customerData.get('cart')().items,
                        sku = addToCartForm.attr('data-product-sku');

                    let inCart = false;
                    _.each(items, function (item, key) {
                        if (item.product_sku === sku) {
                            const updateContainer = addToCartForm.children('.update-cart-qty');
                            updateContainer.children("input[name='item_id']").val(item.item_id);
                            updateContainer.find("input[name='item_qty']").val(item.qty);
                            updateContainer.show();
                            addToCartForm.children('.action.tocart').hide();
                            if (item.qty >= item.product_stock) {
                                updateContainer.children('.increase-qty')
                                    .attr('disabled', 'disabled')
                                    .css("background", "grey")
                            }
                            actionPrimary.prev('.action-primary-loader').hide();
                            actionPrimary.addClass('visibility-visible');
                            inCart = true;
                            return true;
                        }
                    });
                    if (inCart === false) {
                        actionPrimary.addClass('visibility-visible');
                        actionPrimary.prev('.action-primary-loader').hide();
                    }
                    addToCartForm.catalogAddToCartAdvanced();
                } else {
                    actionPrimary.addClass('visibility-visible');
                    actionPrimary.prev('.action-primary-loader').hide();
                }
            }
        },
    });

    return $.mage.updateQtyAddedToProduct;

});
