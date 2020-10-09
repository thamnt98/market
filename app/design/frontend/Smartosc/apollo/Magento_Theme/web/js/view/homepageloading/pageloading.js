/**
 * @category Magento
 * @package Magento_Catalog
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
            var homepageEl = $(document).find(".cms-index-index"),
                loadingclassEl = $(document).find(".page-loading");

            /** Dom loading*/
            var panelHeader = $('.panel .header'),
                headerContent = $('.page-header'),
                sectionsNavSections = $('.nav-sections'),
                pagebuilderSlider = $('.pagebuilder-slider'),
                featuredPickupContent = $('.featured-pickup-content'),
                selectedCatHomepage = $('.selected-cat-homepage'),
                productSpecialPicks = $('.product-special-picks'),
                productItem = $('.product-item'),
                latestPromoInfo = $('.latest-promo-info'),
                reorderCard = $('.reorder-card'),
                inspireCard = $('.inspire-card');

            /** @ block add DOM loading*/
            $(document).ready(function () {
                //case readyState not complete yet
                if (document.readyState !== 'complete' && homepageEl.length > 0 && loadingclassEl.length > 0) {
                    panelHeader.after("<span class='panelHeader-loading'></span>");
                    headerContent.after("<span class='headerContent-loading'></span>");
                    sectionsNavSections.after("<span class='sectionsNavSections-loading'></span>");
                    pagebuilderSlider.after("<span class='pagebuilderSlider-loading'></span>");
                    featuredPickupContent.after("<span class='featuredPickupContent-loading'></span>");
                    selectedCatHomepage.after("<span class='selectedCatHomepage-loading'></span>");
                    productSpecialPicks.after("<span class='productSpecialPicks-loading'></span>");
                    productItem.after("<span class='productItem-loading'></span>");
                    latestPromoInfo.after("<span class='latestPromoInfo-loading'></span>");
                    reorderCard.after("<span class='reorderCard-loading'></span>");
                    inspireCard.after("<span class='inspireCard-loading'></span>");
                }
            });

            /** @ block remove DOM loading*/
            $(window).load(function () {
                if (homepageEl.length > 0) {
                    //case readyState complete
                    if (document.readyState === 'complete' && loadingclassEl.length > 0) {
                        $(document).find(".cms-index-index").removeClass('page-loading');
                        $(".panelHeader-loading").remove();
                        $(".headerContent-loading").remove();
                        $(".sectionsNavSections-loading").remove();
                        $(".pagebuilderSlider-loading").remove()
                        $(".featuredPickupContent-loading").remove();
                        $(".selectedCatHomepage-loading").remove();
                        $(".productSpecialPicks-loading").remove();
                        $(".productItem-loading").remove();
                        $(".latestPromoInfo-loading").remove();
                        $(".reorderCard-loading").remove();
                        $(".inspireCard-loading").remove();
                    }
                }
            });
        },
    })
});
