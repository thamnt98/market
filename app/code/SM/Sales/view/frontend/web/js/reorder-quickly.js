define(
    [
        'jquery',
        'mage/url'
    ],
    function ($, url) {
        "use strict";

        return function () {
            $.ajax({
                url: url.build('sales/ajax/reorderquickly'),
                type: 'post',
                success: function (data) {
                    if (data.status == 1) {
                        $(".row-reorder-quickly-block").removeClass("hidden");
                        $("#reorder-quickly-content").append(data.block).show();
                    }
                }
            });
        }
    }
);

