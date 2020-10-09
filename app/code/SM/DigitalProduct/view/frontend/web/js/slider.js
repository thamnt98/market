define([
    'jquery',
    'slick'
], function ($) {
    "use strict";

    return function () {
        $(".quick-transaction-carousel").slick({
            dots: false,
            infinite: false,
            arrows: true,
            speed: 300,
            variableWidth: true,
            slidesToShow: 4,
            slidesToScroll: 4,
            responsive: [
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 3
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 2
                    }
                },
                {
                    breakpoint: 320,
                    settings: {
                        slidesToShow: 1
                    }
                }
            ]
        });
    }
});
