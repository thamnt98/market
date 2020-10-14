/**
 * @category Trans
 * @package  Trans_Customer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

var config = {
    config: {
        mixins: {
            'Mageplaza_SocialLogin/js/popup': {
                'Trans_Customer/js/login': true
            }
        }
    },
    paths: {
        otpVerification: 'Trans_Customer/js/otp-verification',
    }
};
