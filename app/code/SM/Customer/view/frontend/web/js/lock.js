/**
 * @category Trans
 * @package Trans_CustomerMyProfile
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
        'OtpVerification',
        'ForgotPasswordConfirm',
        'mage/translate',
        'mage/validation',
        'mage/mage'
    ], function ($, modal, OtpVerification, ForgotPasswordConfirm) {
        'use strict';

        $.widget(
            'sm.lock', {
                _create: function () {
                    this.init();
                    this.submitForm();
                },

                init: function () {
                    var self = this,
                        options = {
                            type: 'popup',
                            responsive: true,
                            innerScroll: false,
                            title: '',
                            buttons: [],
                            modalClass: 'modal-popup-lock modal-popup-signin',
                            clickableOverlay: false
                        };

                    modal(options, $(self.options.tabLockSelector));
                    $(self.options.tabLockSelector).modal('openModal').on('modalclosed', function() {
                        if (!$(this).hasClass('next-open')) {
                            $('#tab-login').modal('openModal').show();
                            $(this).removeClass('next-open');
                        } else {
                            $(this).removeClass('next-open');
                        }
                    });
                    $(self.options.tabLockSelector).find('[selector=close]').click(function () {
                        $(self.options.tabLockSelector).modal('closeModal');
                    });
                },

                submitForm: function () {
                    var self = this,
                        lockContainer = $(self.options.tabLockSelector);
                    lockContainer.find('form').submit(function (e) {
                        var userInput = lockContainer.find('input[name=username]');
                        var user = lockContainer.find('input[name=username]').val();
                        e.preventDefault();
                        if ($(this).valid()) {
                            $.ajax({
                                type: "POST",
                                url: self.options.checkUserUrl,
                                data: {type: 'lock', user: user},
                                success: function (result) {
                                    if (result.message == '') {
                                        lockContainer.find('#user-not-exist').text(result.message).hide();
                                        userInput.removeClass('mage-error');
                                        if (result.type == 'email') {
                                            self.sendRecoveryEmail(user);
                                        } else {
                                            lockContainer.addClass('next-open');
                                            OtpVerification.sendOtp('lock', user, false, lockContainer.find('#user-not-exist'), lockContainer, function (response) {
                                                self.resetPass();
                                            });
                                        }
                                    } else {
                                        userInput.addClass('mage-error');
                                        lockContainer.find('#user-not-exist').text(result.message).show();
                                    }
                                },
                            });
                        } else {
                            userInput.addClass('mage-error');
                            lockContainer.find('#user-not-exist').text('').hide();
                        }
                    });
                },

                sendRecoveryEmail: function (user) {
                    var self = this,
                        lockContainer = $(self.options.tabLockSelector);
                    $.ajax({
                        type: "POST",
                        url: self.options.sendRecoveryUrl,
                        data: {user: user, type: 'recovery'},
                        showLoader: true,
                        success: function (result) {
                            if (result.status) {
                                lockContainer.addClass('next-open');
                                ForgotPasswordConfirm.openPopup(lockContainer, user);
                            }
                        },
                    });
                },

                resetPass: function () {
                    var self = this;
                    $.ajax({
                        type: "POST",
                        url: self.options.redirectUrl,
                        data: {},
                        showLoader: true,
                        success: function (result) {
                            window.location.href = result.url;
                        },
                    });
                }
            }
        );

        return $.sm.lock;
    }
);
