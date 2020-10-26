define([
    'jquery',
    'gtmCheckout',
    'mage/url'
], function ($, gtmCheckout, urlBuilder) {
    'use strict';
    return {
        collectData: function (event) {
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
                            gtmCheckout.push(event,dataProducts);
                        }
                    },
                    error: function () {

                    }
                });
            }
        }
    };
});
