define([
    'jquery',
    'mage/url',
    'mage/cookies'
], function ($, url) {
    "use strict";

    return function () {
        $(".quick-transaction-item").each(function () {
            $(this).on("click", function () {
                var buyRequest = $(this).attr("data-buy-request");
                var sku = $(this).attr("data-sku");
                var price = $(this).attr("data-price");

                buyRequest = JSON.parse(buyRequest);
                var digitalInfo = buyRequest.digital;
                var digitalTransaction = buyRequest.digital_transaction;

                var formReorder = $('<form/>', {
                    "method": "post",
                    "action": url.build('digitalproduct/index/reorder')
                });

                var inputSku = $('<input/>', {"type": "hidden", "value": sku, "name": "sku"});
                var inputPrice = $('<input/>', {"type": "hidden", "value": price, "name": "price"});
                var formKey = $('<input/>', {"type": "hidden", "value": $.mage.cookies.get('form_key'), "name": "form_key"});
                var serviceType = $('<input/>', {"type": "hidden", "value": buyRequest.service_type, "name": "service_type"});

                formReorder
                    .append(inputSku)
                    .append(inputPrice)
                    .append(formKey)
                    .append(serviceType);

                Object.keys(digitalInfo).forEach(function (key) {
                    formReorder.append($('<input/>', {"type": "hidden", "value": digitalInfo[key], "name": "digital[" + key + "]"}));
                });

                Object.keys(digitalTransaction).forEach(function (key) {
                    formReorder.append($('<input/>', {"type": "hidden", "value": digitalTransaction[key], "name": "digital_transaction[" + key + "]"}));
                });
                $(document.body).append(formReorder);
                formReorder.submit();
            })
        });
    }
});
