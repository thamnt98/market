define(
    [
        'jquery',
        '!domReady'
    ],
    function ($) {
        "use strict";
        let show = {};
        show.init = function () {
            $(window).ready(function () {
                $('.sign-link a').trigger('click');
            });
        }

        return show.init();
    });
