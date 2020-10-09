/*
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

require([
    'jquery'
], function ($) {
    "use strict";
    $.validator.addMethod(
        'validateee-customer-email',
        function (v) {
            $("#email").css("border-color","#e02020");
            return $.mage.isEmptyNoTrim(v) || /^([a-z0-9,!\#\$%&'\* \+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*@([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*\.(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]){2,})$/i.test(v); //eslint-disable-line max-len
        },
        $.mage.__('Make sure you follow the format')
    );

    $.validator.addMethod(
        'validate-email-not-change',
        function (v) {
            return $("#form-edit-email input[name=email]").attr("data-current-email") !== v;
        },
        $.mage.__('You are currently using this email')
    );
});
