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
        'mage/translate',
        'mage/validation'
    ],
    function ($, ModalCreator) {
        "use strict";

        let mod = {};

        mod.init = function () {
            mod.initElements();
            mod.initEvents();
            mod.backAction();
        };

        mod.initElements = function () {
            mod.container = $('#tab-recovery-password');
            mod.email = mod.container.find('input#reset-password-email');
            mod.token = mod.container.find('input#reset-password-token');
            mod.resetPassWord = mod.container.find('input#reset-password');
            mod.inputComfirm = mod.container.find('input#reset-password-confirmation');
            mod.resetPassWordConfirmation = mod.container.find('input#reset-password-confirmation');
            mod.submit = mod.container.find('button.action');
            mod.labelSuccess = mod.container.find('.reset-password-success');
            mod.labelFailed = mod.container.find('.reset-password-failed');
            mod.showHidePassWord = mod.container.find('#show_password');
            mod.form = mod.container.find('form#form-customer-confirm-new-password');
            mod.fieldPassword = mod.container.find('#field-password-reset');
            mod.fieldConfirm = mod.container.find('#field-password-confirm');
        };

        mod.initEvents = function () {
            ModalCreator.create(mod.container, 'modal-popup-recovery-password modal-popup-signin');

            mod.resetPassWord.on('change', function () {
                mod.labelFailed.hide();
                $.validator.validateSingleElement(mod.resetPassWord);
            });
            mod.resetPassWordConfirmation.on('change', function () {
                $.validator.validateSingleElement(mod.resetPassWordConfirmation);
            });
            mod.submit.on('click', function(e) {
                e.preventDefault();

                mod.submit.addClass('disabled');
                if (mod.form.validation('isValid')) {
                    var status = mod.container.find('form').validation('isValid');
                    if (status) {
                        mod.sendResetPassword(mod.email.val(), mod.token.val(), mod.resetPassWord.val());
                    }
                } else {
                    mod.submit.removeClass('disabled');
                }
            });

            mod.fieldPassword.find('#show_password').on('click', function () {
                if(mod.resetPassWord.attr('type') === 'password'){
                    mod.resetPassWord.attr('type','text');
                } else {
                    mod.resetPassWord.attr('type','password');
                }
                $(this).toggleClass('show-password');
            });

            mod.fieldConfirm.find('#show_password').on('click', function () {
                if(mod.inputComfirm.attr('type') === 'password'){
                    mod.inputComfirm.attr('type','text');
                } else {
                    mod.inputComfirm.attr('type','password');
                }
                $(this).toggleClass('show-password');
            });

            mod.container.find('#show_password').mouseover(function(){
                mod.container.find('.confirmation span').addClass('disable-forcus');
                mod.container.find('#password-strength-meter').addClass('disable-forcus');
            });
            mod.container.find('#show_password').mouseleave(function(){
                mod.container.find('.confirmation span').removeClass('disable-forcus');
                mod.container.find('#password-strength-meter').removeClass('disable-forcus');
            });

            var urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('recovery')) {
                mod.container.modal('openModal').on('modalclosed', function() {
                    $('#tab-login').modal('openModal').show();
                });
            }
        };

        mod.sendResetPassword = function (email, token, newPassword) {
            $.ajax({
                type: "POST",
                url: BASE_URL+'rest/V1/customers/resetPassword',
                dataType: "json",
                data: JSON.stringify({email: email, resetToken: token, newPassword: newPassword}),
                beforeSend: function(xhr){
                    xhr.setRequestHeader('Accept', 'application/json');
                    xhr.setRequestHeader('Content-Type', 'application/json');
                },
                success: function (response) {
                    $(document).trigger('customer:forgot-password');
                    mod.labelSuccess.text($.mage.__('Your password has been changed.'));
                    window.location = BASE_URL;
                    mod.submit.removeClass('disabled');
                },
                error: function (xhr, status, error) {
                    mod.labelFailed.text(xhr.responseJSON.message);
                    mod.labelFailed.show();
                    mod.resetPassWord.addClass("mage-error");
                    mod.submit.removeClass('disabled');
                }
            });
        };

        mod.openPopup = function(closingPopup) {
            closingPopup.addClass('not-open').modal('closeModal');
            mod.container.modal('openModal').on('modalclosed', function() {
                closingPopup.removeClass('not-open');
                $('#tab-login').modal('openModal').show();
            });
            mod.container.show();
        };

        mod.setEmail = function(email) {
            mod.email.val(email);
        };

        mod.backAction = function() {
            mod.container.find('[selector=action-back]').click(function () {
                mod.container.modal('closeModal');
            });
        };

        mod.setToken = function(token) {
            mod.token.val(token);
        };

        return mod;
    }
);
