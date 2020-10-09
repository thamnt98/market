define([
    "jquery",
    'matchMedia',
    'slick',
    'domReady!',
], function ($, mediaCheck) {
    "use strict";

    const header = $('.page-header');

    if (header.length) {
        $(window).scroll(function () {
            if ($(window).scrollTop() > header.offset().top && !(header.hasClass('sticky'))) {
                header.addClass('sticky');
                if ($('#modals-overlay-default').length > 0) {
                    var heightHeader = $('.page-wrapper .page-header').outerHeight();
                    $('#modals-overlay-default').css("top",heightHeader+'px');
                }
            } else if ($(window).scrollTop() === 0) {
                header.removeClass('sticky');
            }
        });
    }

    $('.slick-carousel').slick({
        slidesToShow: 5,
        slidesToScroll: 1,
        variableWidth: true,
        arrows: true,
        infinite: true

    });

    let switchLanguage = $('#switcher-language-nav'),
        accountLink    = $('.nav-header .links');

    mediaCheck({
        media: '(min-width: 1024px)',

        /**
         * Switch to Desktop Version.
         */
        entry: function () {
            switchLanguage.css('display', 'none');
            accountLink.css('display', 'none');
        },

        /**
         * Switch to Mobile Version.
         */
        exit: function () {
            switchLanguage.css('display', 'inline-block');
            accountLink.css('display', 'inline-block');
        }
    });
});
