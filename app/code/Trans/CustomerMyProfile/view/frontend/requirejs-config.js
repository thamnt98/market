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

var config = {
    map: {
        '*': {
            validatemobilenumberjs: 'Trans_CustomerMyProfile/js/validation-mobile-number',
            validateprofilepicturejs: 'Trans_CustomerMyProfile/js/profile-picture',
            validateCustomerEmail: 'Trans_CustomerMyProfile/js/validation-customer-email',
            dropdownDatePicker: 'Trans_CustomerMyProfile/js/lib/jquery-dropdown-datepicker.min'
        }
    },
    shim: {
        dropdownDatePicker: {
            deps: ['jquery']
        }
    }
};
