/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'matchMedia',
    'Magento_PageBuilder/js/utils/breakpoints',
    'Magento_PageBuilder/js/events',
    'slick'
], function ($, _, mediaCheck, breakpointsUtils, events) {
    'use strict';

    /**
     * Initialize slider.
     *
     * @param {jQuery} $element
     * @param {Object} config
     */
    function buildSlick(
        $element,
        config
    ) {
        /**
         * Prevent each slick slider from being initialized more than once which could throw an error.
         */
        if ($element.hasClass('slick-initialized')) {
            $element.slick('unslick');
        }
        if (config.slidesToShow) {
            config.slidesToScroll = config.slidesToShow;
        }
        $element.slick(config);
    }

    return function (config, element) {
        let $element = $(element);
        if ($element.data('appearance') === 'default') {
            if ($element.data('slider-option') === 'medium') {
                config = {
                    centerMode: true,
                    centerPadding: '200px',
                    autoplay: $element.data('autoplay'),
                    autoplaySpeed: $element.data('autoplay-speed') || 0,
                    fade: false,
                    infinite: $element.data('is-infinite'),
                    arrows: $element.data('show-arrows'),
                    dots: $element.data('show-dots'),
                };
                buildSlick($element, config);
            } else {
                config = {
                    autoplay: $element.data('autoplay'),
                    autoplaySpeed: $element.data('autoplay-speed') || 0,
                    fade: $element.data('fade'),
                    infinite: $element.data('is-infinite'),
                    arrows: $element.data('show-arrows'),
                    dots: $element.data('show-dots'),

                };
                buildSlick($element, config);
            }
        } else {
            var centerModeClass = 'center-mode',
                carouselMode = $element.data('carousel-mode'),
                slickConfig = {
                    autoplay: $element.data('autoplay'),
                    autoplaySpeed: $element.data('autoplay-speed') || 0,
                    arrows: $element.data('show-arrows'),
                    dots: $element.data('show-dots')
                };

            _.each(config.breakpoints, function (breakpoint) {
                mediaCheck({
                    media: breakpointsUtils.buildMedia(breakpoint.conditions),

                    /** @inheritdoc */
                    entry: function () {
                        breakpoint.options.products[carouselMode].slidesToShow = $(element).data('carousel-slider-display');
                        var slidesToShow = breakpoint.options.products[carouselMode] ?
                            breakpoint.options.products[carouselMode].slidesToShow :
                            breakpoint.options.products.default.slidesToShow;

                        slickConfig.slidesToShow = parseFloat(slidesToShow);

                        slickConfig.responsive =  [
                            {
                                breakpoint: 1900,
                                settings: {
                                    slidesToShow: parseFloat(slidesToShow),
                                }
                            },
                            {
                                breakpoint: 1200,
                                settings: {
                                    slidesToShow: 3,
                                    slidesToScroll: 3
                                }
                            },
                            {
                                breakpoint: 700,
                                settings: {
                                    slidesToShow: 2,
                                    slidesToScroll: 2
                                }
                            },
                            {
                                breakpoint: 400,
                                settings: {
                                    slidesToShow: 1,
                                    slidesToScroll: 1

                                }
                            }
                        ]

                        if (carouselMode === 'continuous' ) {
                            $element.addClass(centerModeClass);
                            slickConfig.centerPadding = $element.data('center-padding');
                            slickConfig.centerMode = true;
                        } else {
                            $element.removeClass(centerModeClass);
                            slickConfig.infinite = $element.data('infinite-loop');
                        }

                        buildSlick($element, slickConfig);
                    },
                    // Switch to Mobile Version
                    exit: function () {

                    }
                });
            });
        }

        // Redraw slide after content type gets redrawn
        events.on('contentType:redrawAfter', function (args) {
            if ($element.closest(args.element).length) {
                $element.slick('setPosition');
            }
        });
    };
});
