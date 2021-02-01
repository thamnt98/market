/**
 * @category SM
 * @package SM_Customer
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author Nam Nguyen <namnd2@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */
define(
    [
        'jquery',
        'Magento_Ui/js/modal/modal',
        'mage/url',
        'CustomerSocialProvider'
    ], function ($, modal, urlBuilder) {
        'use strict';

        let mod = {},
            loginForm = false,
            registerForm = false,
            forgotPassForm = false,
            recoveryForm = false,
            optForm = false;

        mod.init = function () {
            $('.sign-link a').click(function(event){
                event.preventDefault();
                if (loginForm) {
                    $('#tab-login').modal('openModal');
                } else {
                    mod.callAjax('login-form');
                }
            });
            var urlParams = new URLSearchParams(window.location.search);
            console.log(urlParams.has('recovery'));
            if (urlParams.has('recovery') && recoveryForm === false) {
                // show recovery popup
                mod.callAjax('recovery-form');
            } else if (urlParams.has('recoverytoken')) {
                mod.callAjax('lock-reset-form');
            } else {
                // show login popup
                $('.sign-link a').trigger('click');
            }
        };

        mod.callAjax = function (type) {
            $.ajax({
                url: urlBuilder.build("customer/popup/index"),
                type: 'POST',
                dataType: 'json',
                data: {'type': type, 'otp': optForm},
                showLoader: true,
                async: true,
                success: function(response) {
                    $('body').append(response.html).trigger('contentUpdated');
                    if (type == 'login-form') {
                        loginForm = true;
                        $('#create-account').click(function (){
                            $('#tab-login').modal('closeModal');
                            if (!registerForm) {
                                mod.callAjax('register-form');
                            }
                        });
                        $('#action-forgot-password').click(function () {
                            if (!forgotPassForm) {
                                mod.callAjax('forgot-password-form');
                            }
                        });
                    } else if (type == 'register-form') {
                        registerForm = true;
                    } else if (type == 'forgot-password-form') {
                        forgotPassForm = true;
                    } else if (type == 'recovery-form') {
                        recoveryForm = true;
                    }
                    optForm = true;
                }
            });
        };

        return mod.init();
    }
);
