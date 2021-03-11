define(
    [
        'jquery',
        '!domReady'
    ],
    function ($) {
        "use strict";
        let show = {};
        show.init = function () {
            $(window).load(function () {
                $('.sign-link a').trigger('click');
            });
        }

        return show.init();
    });
