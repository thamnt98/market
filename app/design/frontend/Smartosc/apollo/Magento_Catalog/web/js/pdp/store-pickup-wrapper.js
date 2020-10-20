
define(
    [
        'jquery',
        'mage/url'
    ],
    function ($,urlBuilder) {
        "use strict";
        return function (config) {
            var wrapper = $("#store-pickup-wrapper");
            var data = {
                product_id : wrapper.attr("data-product-id")
            };

            $.ajax({
                url: urlBuilder.build("catalog/ajax/getstorepickup"),
                data: data,
                type: 'post',
                success: function (response) {
                    if (response.status == 1) {
                        wrapper.append(response.html).trigger('contentUpdated');
                    }
                }
            });
        }
    }
);

