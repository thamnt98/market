define(
    [
        'jquery',
    ],
    function ($, modal) {
        "use strict";

        return function (config) {
            var reviewForm = $('#review-form');

            reviewForm.append('<input type="hidden" name="order_id" value="' + config.order_id + '" />');
        };
    }
);

