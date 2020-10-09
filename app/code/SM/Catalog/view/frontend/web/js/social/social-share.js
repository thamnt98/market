/**
 * @category SM
 * @package SM_Catalog
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
        'JsSocials',
        'mage/translate'
    ],
    function ($, jsSocials) {
        "use strict";

        var socialshareEl = $('.social-bt');

        socialshareEl.jsSocials({
            showLabel: false,
            showCount: false,
            shareIn: "popup",
            shares: ["messenger", "facebook", "googleplus", "pinterest", "twitter"]
        });
    }
);

