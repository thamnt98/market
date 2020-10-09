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

define(
    [
        'jquery',
        'mage/translate'
    ],
    function ($) {
        "use strict";

        var triggerEl = $('#trigger-overview'),
            tabLabelDes = $("#tab-label-description-title");

        triggerEl.on('click', function(){
            //open full description
            tabLabelDes.trigger("click");
            $([document.documentElement, document.body]).animate({
                scrollTop: tabLabelDes.offset().top
            }, 2000);
        });
    }
);

