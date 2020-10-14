define(
    [
        'jquery',
        'Magento_Ui/js/modal/modal',
        'gtmSha256',
        'mage/translate'
    ],
    function($, ModalCreator, sha256) {
        "use strict";

        var downloadTimer,
            time;
        let mod = {};

        mod.init = function () {
            window.count = window.count || 1;
            mod.initVars();
            mod.initElements();
            mod.initEvents();
        };

        mod.initVars = function () {
            mod.vars = {};
            mod.vars.phoneNumber = null;
            mod.vars.checkExist = null;
            mod.vars.action = null;

            mod.func = {};
            mod.func.callback = null;
        };

        mod.initElements = function () {
            mod.container = $('#tab-verification');
            mod.phoneNumber = mod.container.find('#phone-otp');
            mod.combine = mod.container.find('input#combine');
            mod.inputs = mod.container.find('input.combine');
            mod.errorLabel = mod.container.find('#message-otp-error');
            mod.noticeLabel = mod.container.find('#message-otp-notice');
            mod.submit = mod.container.find('button#otp-validate');
            mod.resendBtn = mod.container.find('#resend-otp');
            mod.currentCountDown = mod.container.find('.txt-second span');
            time = parseInt(mod.container.find('.txt-second').attr('count'));
        };

        mod.initEvents = function () {
            let options = {
                type: 'popup',
                responsive: true,
                innerScroll: false,
                buttons: [],
                modalClass: 'modal-popup-otp-verification modal-popup-signin',
                clickableOverlay: false,
                focus: mod.container.find('input[name="digit-1"]'),
                keyEventHandlers: {
                    enterKey: function (event) {
                        event.preventDefault();
                        if (mod.submit.hasClass('disabled')) {
                            mod.inputs.each(function () {
                                if (this.value.length === 1 && $.isNumeric(this.value)) {
                                    $(this).removeClass('mage-error');
                                } else if (!$(this).hasClass('mage-error')) {
                                    $(this).addClass('mage-error');
                                }
                            });
                        } else {
                            mod.submit.trigger('click');
                        }
                    }
                }
            };

            ModalCreator(options, mod.container);

            mod.submit.addClass('disabled');
            mod.initInputEvent();

            mod.submit.on('click', function() {
                mod.sendValidateRequest(mod.combine.val());
                return false;
            });

            mod.resendBtn.on('click', function() {
                if (mod.currentCountDown.text() == 0) {
                    clearInterval(downloadTimer);
                    mod.container.find('.txt-second span').text(time);
                    mod.sendOtp(mod.vars.action, mod.vars.phoneNumber, mod.vars.checkExist, mod.errorLabel, null, null, true);
                    let phone_number = mod.phoneNumber.text();
                    if(typeof phone_number !== 'undefined') {
                        window.dataLayer = window.dataLayer || [];
                        window.dataLayer.push({
                            'event': "otp_resend",
                            'phone_number': sha256(phone_number),
                            'resend_count' : window.count
                    });
                        window.count = window.count + 1;
                    }
                }
            });
        };

        mod.initInputEvent = function () {
            mod.inputs.each(function () {
                let inputs      = $('.form-verification input.otp-input'),
                    inputParent = null;

                $(inputs).on('keydown', function (event) {
                    if (!inputParent) {
                        inputParent = $(this).parent();
                    }

                    if ($(this).val().length > 0) {
                        $(this).val('');
                    }

                    if (mod.currentCountDown.text() == 0 && !mod.submit.hasClass('disabled')) {
                        mod.submit.addClass('disabled');

                        return false;
                    }

                    if (event.keyCode === 8 ||
                        event.keyCode === 37 ||
                        (event.keyCode >= 48 && event.keyCode <= 57) ||
                        (event.keyCode >= 96 && event.keyCode <= 105)
                    ) {
                        $(this).removeClass('mage-error');

                        return true;
                    } else {
                        if (!$(this).hasClass('mage-error')) {
                            $(this).addClass('mage-error');
                        }

                        return false;
                    }
                });

                $(inputs).on('input', function () {
                    let value = $(this).val();

                    mod.combine.val(
                        mod.inputs.map(function () {
                            return $(this).val();
                        }).get().join('')
                    );

                    if (value.length === 1 && $.isNumeric(value)) {
                        let next = inputParent.find('input[name=' + $(this).data('next') + ']'),
                            errorField = $('.form-verification input.otp-input.mage-error'),
                            emptyField = $(inputs).filter(function () {
                                return $(this).val() == "";
                            });

                        if (errorField.length < 1 && emptyField.length < 1) {
                            mod.submit.removeClass('disabled');
                        } else if (!mod.submit.hasClass('disabled')) {
                            mod.submit.addClass('disabled');
                        }

                        $(this).removeClass('mage-error');
                        if (next.length && $(this).data('next')) {
                            next.select();
                        } else if (inputParent.data('autosubmit') && errorField.length < 1 && emptyField.length < 1) {
                            $(this).parents('form').submit();
                        }
                    } else if (!mod.submit.hasClass('disabled')) {
                        mod.submit.addClass('disabled');
                    }
                });

                $(inputs).on('keyup', function (event) {
                    if (!inputParent) {
                        inputParent = $(this).parent();
                    }

                    if ((event.which === 8 || event.keyCode === 37) && $(this).data('previous')) {
                        let prev = inputParent.find('input[name=' + $(this).data('previous') + ']');

                        if (prev.length) {
                            prev.select();
                        }
                    }
                });

                $(inputs).on('focus', function () {
                    let element = $(this);

                    $(this).select();
                    if (!inputParent) {
                        inputParent = $(this).parent();
                    }

                    while ($(element).data('previous')) {
                        element = inputParent.find('input[name=' + $(element).data('previous') + ']');
                        if (element.length) {
                            if (!$(element).hasClass('mage-error') && !$(element).val()) {
                                $(element).addClass('mage-error');
                            }
                        } else {
                            break;
                        }
                    }
                });
            });
        };

        mod.sendOtp = function(action, phoneNumber, checkExist, errorLabel, openingPopup, callback, resend=false) {
            errorLabel.hide();
            mod.phoneNumber.text(phoneNumber);
            $.ajax({
                type: "POST",
                url: BASE_URL+'rest/V1/sms/verification/send',
                dataType: "json",
                data: JSON.stringify({"phone_number": phoneNumber, "check_exist_customer_phone": checkExist}),
                showLoader: true,
                beforeSend: function(xhr){
                    xhr.setRequestHeader('Accept', 'application/json');
                    xhr.setRequestHeader('Content-Type', 'application/json');
                },
                statusCode: {
                    400: function(response) {
                        if (errorLabel.attr('id') == 'forgot-password-error') {
                            var message = $.mage.__('You are not yet registered');
                        } else {
                            var message = response.responseJSON.message;
                        }
                        errorLabel.text(message);
                        errorLabel.show();
                    },
                    500: function() {
                        errorLabel.text($.mage.__('System error, please try again later'));
                        errorLabel.show();
                    },
                    200: function() {
                        if (resend === false) {
                            mod.vars.action = action;
                            mod.vars.phoneNumber = phoneNumber;
                            mod.vars.checkExist = checkExist;
                            mod.func.callback = callback;
                            mod.openPopup(openingPopup);
                        }
                        else {
                            mod.counter();
                        }
                    }
                }
            });
        };

        mod.sendValidateRequest = function(verificationCode) {
            mod.errorLabel.hide();
            mod.noticeLabel.hide();
            mod.submit.addClass('disabled');
            $.ajax({
                type: "POST",
                url: BASE_URL+'rest/V1/sms/verification/verify',
                dataType: "json",
                data: JSON.stringify({"phone_number": mod.vars.phoneNumber, "verification_code": verificationCode, "action": mod.vars.action}),
                beforeSend: function(xhr){
                    xhr.setRequestHeader('Accept', 'application/json');
                    xhr.setRequestHeader('Content-Type', 'application/json');
                },
                statusCode: {
                    400: function(response) {
                        mod.errorLabel.text(response.responseJSON.message);
                        mod.errorLabel.show();
                        mod.submit.addClass('disabled');
                        mod.inputs.each(function () {
                            $(this).addClass('mage-error');
                        });
                        let phone_number = mod.phoneNumber.text();
                        if(typeof phone_number !== 'undefined') {
                            window.dataLayer = window.dataLayer || [];
                            window.dataLayer.push({
                                'event': "otp_failed",
                                'phone_number': sha256(phone_number)
                            });
                        }
                    },
                    500: function() {
                        mod.errorLabel.text($.mage.__('System error, please try again later'));
                        mod.errorLabel.show();
                        mod.submit.addClass('disabled');
                        mod.inputs.each(function () {
                            $(this).addClass('mage-error');
                        });
                        let phone_number =  mod.phoneNumber.text();
                        if(typeof phone_number !== 'undefined') {
                            window.dataLayer = window.dataLayer || [];
                            window.dataLayer.push({
                                'event': "otp_failed",
                                'phone_number': sha256(phone_number)
                            });
                        }
                    },
                    200: function(response) {
                        $(document).trigger('customer:otp');
                        $(document).trigger('customer:register');
                        mod.func.callback(response);
                    }
                }
            });
        };

        mod.openPopup = function(openingPopup) {
            openingPopup.modal('closeModal');
            mod.counter();
            mod.container.modal('openModal').on('modalclosed', function() {
                clearInterval(downloadTimer);
                mod.container.find('.txt-second span').text(time);
                mod.errorLabel.text($.mage.__(''));
                mod.errorLabel.hide();
                mod.inputs.each(function () {
                    $(this).val('');
                    $(this).removeClass('mage-error');
                });
                if (!$(this).hasClass('not-open')) {
                    openingPopup.removeClass('not-open').modal('openModal');
                }
            });
            mod.container.show();
        };

        mod.counter = function() {
            var timeleft = time;
            mod.resendBtn
                .addClass("txt-count")
                .css({cursor:"not-allowed"});
            downloadTimer = setInterval(function(){
                if (timeleft > 0) {
                    timeleft--;
                }
                mod.container.find('.txt-second span').text(timeleft);
                if(timeleft <= 0){
                    clearInterval(downloadTimer);
                    mod.submit.addClass('disabled');
                    mod.resendBtn
                        .removeClass("txt-count")
                        .css({cursor:"pointer"});
                }
            },1000);
        };

        mod.getPopup = function() {
            return mod.container;
        };

        return mod;
    }
);
