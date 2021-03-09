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
            $('.sign-link a, .authorization-link a, a.showcart').click(function(event){
                mod.triggerLoginForm(event)
            });
            $('.register-link').click(function(event){
                event.preventDefault();
                if (registerForm) {
                    $('#register').modal('openModal');
                } else {
                    mod.callAjax('register-form');
                }
            });
            if ($('.sign-link a').length > 0) {
                $('.page-footer a, .nav.item a').click(function (event) {
                    if($(this).attr('href').indexOf('contactus') != -1){
                        event.preventDefault();
                        $('.sign-link a').trigger('click');
                    }
                });
            }
            var urlParams = new URLSearchParams(window.location.search);
            console.log(urlParams.has('recovery'));
            if (urlParams.has('recovery') && recoveryForm === false) {
                // show recovery popup
                mod.callAjax('recovery-form');
            } else if (urlParams.has('recoverytoken')) {
                mod.callAjax('lock-reset-form');
            } else if (window.location.href.indexOf('checkout/cart') != -1) {
                // show login popup
                $('.sign-link a').trigger('click');
            }

            $('a.button-to-contactus, .pagehelp-contactus a').click(function(event){
                mod.triggerLoginForm(event)
            });

            $('a.towishlist').click(function(event){
                mod.triggerLoginForm(event)
            });

            $('html').on('click', '.navbar-wishlist a', function (event) {
                mod.triggerLoginForm(event)
            });
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
                            $('.register-link').trigger('click');
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

        mod.triggerLoginForm = function (event) {
            event.preventDefault();
            $('.modals-overlay').remove();
            if (loginForm) {
                $('#tab-login').modal('openModal');
            } else {
                mod.callAjax('login-form');
            }
            $('body').addClass('_has-modal');
        }

        return mod.init();
    }
);
