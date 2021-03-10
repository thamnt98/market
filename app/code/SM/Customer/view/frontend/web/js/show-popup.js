require(
    [
        'jquery',
        '!domReady'
    ],
    function ($) {
        "use strict";

        $(window).load(function () {
            $('.sign-link a').trigger('click');
        });
    });
