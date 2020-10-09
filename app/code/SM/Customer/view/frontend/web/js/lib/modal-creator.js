/**
 * @category SM
 * @package SM_Customer
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
        'Magento_Ui/js/modal/modal'
    ],
    function ($, modal) {
        "use strict";

        let mod = {};

        mod.create = function (targetElement, cssClass) {
            let options = {
                type: 'popup',
                responsive: true,
                innerScroll: false,
                buttons: [],
                modalClass: cssClass,
                clickableOverlay: false
            };

            modal(options, targetElement);
        };

        return mod;
    }
);
