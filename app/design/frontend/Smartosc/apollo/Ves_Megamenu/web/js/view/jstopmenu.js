/**
 * @category Ves
 * @package Ves_Megamenu
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author Logan Ta <phuongtt2@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

define([
    'jquery',
    'uiComponent',
    'mage/translate'
], function ($, Component) {
    'use strict';

    return Component.extend({
        /**
         * init function
         */
        initialize: function () {
            var self = this;

            var ipadWidth = 768;
            var ipadLand = 1024;
            var ipadLargest = 1360;

            if($(window).width() > ipadWidth) {
                self.__canCustomAction();
            }

            if((ipadLand < $(window).width()) && ($(window).width() < ipadLargest)) {
                self.__megaMenuLand();
            }

            $(window).on('resize', function () {
                self.__canCustomAction();

                if(ipadLand < $(window).width() < ipadLargest) {
                    self.__megaMenuLand();
                }
            });
        },

        /**
         * __can Action
         */

        __canCustomAction: function () {
            var containerBody = $(document).find("[data-container=body]"),
                header = $('.page-header'),
                section = $('.nav-sections'),
                topmenu = $('.top-navigation');

            /*$(".navigation").hover(function (event) {
                header.css('z-index', 10);
                section.css('z-index', 9);
                topmenu.css('z-index', 9);

            });*/

            $(".ves-megamenu .all-categories").hover(function (event) {
                var heightHeader = $('.page-wrapper .page-header').outerHeight() + $('.sections.nav-sections').outerHeight();
                /**add body modal marks**/
                if(containerBody.find('#modals-overlay-default').length < 1){
                    containerBody.append('<div class="modals-overlay" style="top: '+ heightHeader +'px;z-index: 7;" id="modals-overlay-default"></div>');
                }
            }, function () {
                if(containerBody.find('#modals-overlay-default').length > 0){
                    $('#modals-overlay-default').remove();
                }
            });

            //click sub menu item
            $('.subitems-group').on('click',function (event) {
                $('#modals-overlay-default').remove();
            });
        },

        __megaMenuLand: function () {
            var subMenuMega = $('.ves-megamenu .navigation .nav-item.subhover .dropdown-submenu > .submenu');
            var widthCategory = $('.ves-megamenu .navigation .nav-item.subhover > .submenu').outerWidth();
            var widthSubMenu = $(window).width() - widthCategory;

            subMenuMega.css("width", widthSubMenu);
        },
    })
});
