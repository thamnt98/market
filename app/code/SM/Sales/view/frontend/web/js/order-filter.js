define(
    [
        'jquery'
    ],
    function ($) {
        "use strict";

        return function () {
            $('input[type=radio][name=sort]').change(function () {
                $("#filter-form").submit();
            });

            $("#btn-search-submit").on("click", function () {
                $("#filter-form").submit();
            });
        }
    }
);

