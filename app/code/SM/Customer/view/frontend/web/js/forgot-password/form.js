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
        'ModalCreator',
        'OtpVerification',
        'ForgotPasswordConfirm',
        'RecoveryPassword',
        'mage/translate'
    ],
    function ($, ModalCreator, OtpVerification, ForgotPasswordConfirm, RecoveryPassword) {
        "use strict";

        let mod = {};

        mod.init = function () {
            mod.initElements();
            mod.initEvents();
        };

        mod.initElements = function () {
            mod.container = $('#tab-forgot-password');
            mod.tabLogin = $('#tab-login');
            mod.input = mod.container.find('input[name="fgPass-request"]');
            mod.submit = mod.container.find('button.action');
            mod.errorLabel = mod.container.find('#forgot-password-error');
            mod.form = mod.container.find('form#form-customer-forgot-password');
            mod.backLogin = mod.container.find('#back-login');
        };

        mod.initEvents = function () {
            ModalCreator.create(mod.container, 'modal-popup-forgot-password modal-popup-signin');
            mod.openPopup();
            mod.input.on('change', function () {
                mod.errorLabel.hide();
                mod.input.removeClass('mage-error');
                $.validator.validateSingleElement(mod.input);
            });

            mod.backLogin.on('click', function () {
                mod.container.addClass('not-open');
                mod.container.modal('closeModal');
                mod.tabLogin.modal('openModal');
                mod.tabLogin.show();
            });

            mod.form.on('submit', function (e) {
                e.preventDefault();
                mod.errorLabel.hide();
                mod.submit.addClass('disabled');
                if (mod.isMobileNumber(mod.input.val())) {

                    var telephone = mod.input.val();
                    $.ajax({
                        type: "POST",
                        url: BASE_URL + 'customer/account/userexist',
                        dataType: "json",
                        data: {user: telephone, type: 'forgot'},
                        success: function (result) {
                            if (result.message == '') { // Do this if an phone number is already exists
                                $('#phone-otp').text(mod.container.find('#fgPass-request').val());
                                mod.container.addClass('not-open');
                                mod.input.addClass('mage-error');

                                OtpVerification.sendOtp('reset_password', telephone, true, mod.errorLabel, mod.container, function (response) {
                                    RecoveryPassword.setEmail(telephone);
                                    RecoveryPassword.setToken(response);
                                    RecoveryPassword.openPopup(OtpVerification.getPopup());
                                });
                            } else { // Do this if an phone number not exists
                                mod.input.addClass('mage-error');
                                mod.errorLabel.text(result.message);
                                mod.errorLabel.show();

                            }
                            mod.submit.removeClass('disabled');
                        }
                    });
                } else if (mod.validateEmail(mod.input.val())) {
                    mod.sendRecoveryEmail(mod.input.val());

                }
                else {
                    mod.submit.removeClass('disabled');
                }
            });
            $('#action-forgot-password').click(function () {
                mod.openPopup();
            });
        };

        mod.sendRecoveryEmail = function(email) {
            $.ajax({
                type: "PUT",
                url: BASE_URL+'rest/V1/customers/password',
                dataType: "json",
                data: JSON.stringify({email: email, template: "email_reset"}),
                beforeSend: function(xhr){
                    xhr.setRequestHeader('Accept', 'application/json');
                    xhr.setRequestHeader('Content-Type', 'application/json');
                },
                success: function (response) {
                    if (response == 'You are not yet registered') {
                        mod.errorLabel.text($.mage.__('You are not yet registered'));
                        mod.errorLabel.show();
                        mod.input.addClass('mage-error');
                        mod.submit.removeClass('disabled');
                    } else {
                        ForgotPasswordConfirm.openPopup(mod.container, email);
                    }
                },
                error: function (response) {
                    mod.errorLabel.text(response.responseJSON.message);
                    mod.errorLabel.show();
                    mod.input.addClass('mage-error');
                    mod.submit.removeClass('disabled');
                }
            });
        };

        mod.isMobileNumber = function(inputValue) {
            return /^(^\+628|^628|^08|^8)\d+$/i.test(inputValue);
        };

        mod.validateEmail = function(inputValue) {
            return /^([a-z0-9,!\#\$%&'\* \+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*@([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*\.(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]){2,})$/i.test(inputValue);
        };

        mod.openPopup = function() {
            $('#tab-login').modal('closeModal');
            mod.container.modal('openModal').on('modalclosed', function() {
                if (!$(this).hasClass('not-open')) {
                    $('#tab-login').modal('openModal');
                }
            });
            mod.container.show();
        };

        return mod;
    }
);
