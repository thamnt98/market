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
], function ($, Component) {
    'use strict';
    alert('Home page');
    return Component.extend({
        /**
         * init function
         */
        initialize: function () {
            alert('Home page2');
            /** @ block add DOM loading*/
            $(document).ready(function () {
                //case readyState not complete yet

                }
            });


        },
    })
});
