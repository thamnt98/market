/*
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

define([
    'jquery',
    'mage/url',
    'gtmInspireMe'
], function ($, urlBuilder, gtm) {
    let filter = $('input[name="topic"]');

    $(document).ready(function () {
        $(filter).change(function () {
            let url = this.value;
            if (typeof dataLayerSourceObjects !== "undefined") {
                let data = [];
                data['articleCategory'] = $(this).attr('data-gtm');
                gtm.push('selectTopic_inspire_me', data);
            }
            setTimeout(function(){ location.href = url; }, 2000);
        });
    });
});
