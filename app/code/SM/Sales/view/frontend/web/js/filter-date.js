define(
    [
        'jquery'
    ],
    function ($) {
        "use strict";

        return function () {
            var fromInput = $('#date_from');
            var toInput = $('#date_to');
            fromInput.datepicker({
                dateFormat:'d M yy'
            });

            toInput.datepicker({
                dateFormat:'d M yy'
            });

            fromInput.on("change", function () {
                $("#filter-form").submit();
            });

            toInput.on("change", function () {
                $("#filter-form").submit();
            });

        }
    }
);

