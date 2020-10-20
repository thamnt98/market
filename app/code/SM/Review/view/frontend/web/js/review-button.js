
define(
    [
        'jquery',
    ],
    function ($) {
        "use strict";
        return function (config) {
            var data = {
                product_id : config.product_id
            };
            $.ajax({
                url: config.process_url,
                data: data,
                type: 'post',
                success: function (data) {
                    if (data != 0) {
                        $("#review-pdp").append(data).trigger('contentUpdated');
                        $("#notice-empty").show();
                    }
                }
            });
        }
    }
);

