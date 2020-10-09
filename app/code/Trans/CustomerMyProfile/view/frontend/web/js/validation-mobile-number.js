/**
 * @category Trans
 * @package  Trans_CustomerMyProfile
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Dwi Septha Kurniawan <septha.kurniawan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

require([
        'jquery',
        'jquery/ui',
        'jquery/validate',
        'mage/translate'
    ], function ($) {

    //validate mobile number      
    $.validator.addMethod(
        'validate-mobile-number', function (v, elm) {
            
            return v.match(/^(0|08|08[0-9]{1,13})$/);

        }, $.mage.__('Please enter format mobile number (ex. 08xxxxxxxx) in this field.'));


});