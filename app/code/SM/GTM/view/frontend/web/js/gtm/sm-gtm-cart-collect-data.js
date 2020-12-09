define([
    'jquery',
    'gtmProduct',
    'mage/url'
], function ($, gtm, urlBuilder) {
    'use strict';
    return {
        collectData: function (event, productId, qty) {
            if (typeof dataLayerSourceObjects !== 'undefined') {
                let productIds = [];
                let dataProduct = {
                    'productId' : productId,
                    'productQty' : qty,
                    'delivery_option' : 'Not available'
                };
                productIds.push(dataProduct);
                let data = {
                    'productsInfo' : productIds
                };
                $.ajax({
                    type: 'POST',
                    url: urlBuilder.build('sm_gtm/gtm/product'),
                    data: data,
                    dataType: "json",
                    async: true,
                    success: function (result) {
                        if (result) {
                            $.each(result, function(key, value){
                                try {
                                    let product = JSON.parse(value);
                                    gtm.push(event,product);
                                } catch (e) {

                                }
                            });
                        }
                    },
                    error: function () {

                    }
                });
            }
        },

        removeItemSelected: function () {
            if (typeof dataLayerSourceObjects !== 'undefined') {
                let productIds = [];
                $('input.item-checked:checked').each(function () {
                    let data = {
                        'productId' : $(this).attr('data-gtm-product-id'),
                        'productQty' : $(this).parents().eq(2).find('input[data-role="cart-item-qty"]').val(),
                        'delivery_option' : 'Not available'
                    };
                    productIds.push(data);
                });
                let data = {
                    'productsInfo' : productIds
                };
                $.ajax({
                    type: 'POST',
                    url: urlBuilder.build('sm_gtm/gtm/product'),
                    data: data,
                    dataType: "json",
                    async: true,
                    success: function (result) {
                        if (result) {
                            var dataProducts = [];
                            var quantity = 0;
                            var total = 0;
                            $.each(result, function(key, value){
                                try {
                                    let product = JSON.parse(value);
                                    dataProducts.push(product);
                                    quantity += (product.quantity * 1);
                                    total += (product.quantity * product.price);
                                } catch (e) {

                                }
                            });
                            dataProducts['basket_value'] = total;
                            dataProducts['basket_quantity'] = quantity;
                            gtmCheckout.push('removeFromCart',dataProducts);
                        }
                    },
                    error: function () {

                    }
                });
            }
        }
    };
});
