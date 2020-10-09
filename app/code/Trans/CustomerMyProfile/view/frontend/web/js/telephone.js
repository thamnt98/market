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
        'mage/translate',
        'mage/validation',
        'mage/mage'
    ], function ($) {
        'use strict';

        var downTimer;

        $.widget(
            'trans.telephone', {
                _create: function () {
                    this.options.systemMessage = $.mage.__('System error, please try again later.');
                    this.options.notChangeMesage = $.mage.__('You are currently using this mobile number');
                    this.options.validateTelephone = true;
                    this.options.validateOTP = false;
                    this.inputChange();
                    this.submitForm();
                    this.resetTelephone();
                    this.sendOTP();
                },

                inputChange: function () {
                    var self = this;
                    $(self.options.telephoneSelector).change(function () {
                        $(self.options.telephoneMsg).hide();
                        $(self.options.sendOTPSelector).text($.mage.__('Send Verification Code'));
                        $.validator.validateSingleElement($(this));
                    });
                    $(self.options.otpSelector).change(function () {
                        $(self.options.otpMsg).hide();
                        $.validator.validateSingleElement($(this));
                    });
                },

                submitForm: function () {
                    var self = this;
                    $(self.options.formSelector).submit(function (e) {
                        var validateTelephone = $.validator.validateSingleElement($(self.options.telephoneSelector));
                        if (!validateTelephone) {
                            e.preventDefault();
                            $(self.options.telephoneMsg).hide();
                            $(self.options.otpMsg).hide();
                            //console.log("aaaaaaa");
                            return;
                        }
                        if ($(self.options.telephoneSelector).val() == self.options.telephone) {
                            e.preventDefault();
                            $(self.options.telephoneMsg).text(self.options.notChangeMesage).show();
                            $(self.options.otpMsg).hide();
                            $("#telephone").addClass("mage-error");
                            return;
                        }
                        var validateOTP = $.validator.validateSingleElement($(self.options.otpSelector));
                        if (!validateOTP) {
                            e.preventDefault();
                            $(self.options.otpMsg).hide();
                            console.log("ccccccc");
                            return;
                        }

                        if (!self.options.validateTelephone) {
                            e.preventDefault();
                            console.log("ddddddddddd");
                            return;
                        }
                        if (!self.options.validateOTP) {
                            e.preventDefault();
                            self.verifyOTP();
                            console.log("eeeeeeeeeee");
                        }
                    })
                },

                resetTelephone: function () {
                    var self = this;
                    $(self.options.resetButtonSelector).click(function () {
                        $(self.options.telephoneMsg).hide();
                        $(self.options.otpMsg).hide();
                        $(self.options.otpSelector).val('').trigger('change');
                        $(self.options.telephoneSelector).val(self.options.telephone).trigger('change');
                        self.options.validateOTP = false;
                        self.options.validateTelephone = true;
                    });
                },

                sendOTP: function () {
                    var self = this;
                    $(self.options.sendOTPSelector).click(function () {
                        if ($(this).hasClass('enable')) {
                            var validateTelephone = $.validator.validateSingleElement($(self.options.telephoneSelector));
                            if ($(self.options.telephoneSelector).val() == self.options.telephone) {
                                $(self.options.telephoneMsg).text(self.options.notChangeMesage).show();
                                $(self.options.otpMsg).hide();
                                return;
                            }
                            if (validateTelephone) {
                                // check isset telephone
                                $(self.options.countdownSelector).hide();
                                clearInterval(downTimer);
                                self.telephoneExist();
                            }
                        }
                    })
                },

                telephoneExist: function () {
                    var self = this;
                    $.ajax({
                        type: "POST",
                        url: BASE_URL+'rest/V1/customers/existUser',
                        dataType: "json",
                        data: JSON.stringify({"user": $(self.options.telephoneSelector).val(), "type": "telephone"}),
                        showLoader: true,
                        beforeSend: function(xhr){
                            xhr.setRequestHeader('Accept', 'application/json');
                            xhr.setRequestHeader('Content-Type', 'application/json');
                        },
                        statusCode: {
                            400: function(response) {
                                var message = response.responseJSON.message;
                                $(self.options.telephoneMsg).show().text(message);
                            },
                            404: function(response) {
                                var message = response.responseJSON.message;
                                $(self.options.telephoneMsg).show().text(message);
                            },
                            500: function() {
                                $(self.options.telephoneMsg).show().text(self.options.systemMessage);
                            },
                            200: function(response) {
                                if (!response) {
                                    $(self.options.telephoneMsg).hide();
                                    self.processSendOTP();
                                } else {
                                    $(self.options.telephoneMsg).text($.mage.__('Your mobile number has already been registered')).show();
                                }
                            }
                        }
                    });
                },

                processSendOTP: function () {
                    var self = this;
                    self.options.validateOTP = false;
                    $(self.options.telephoneMsg).hide();
                    $(self.options.otpMsg).hide();
                    $(self.options.otpSelector).val('');
                    $(self.options.countdownSelector).hide();

                    $.ajax({
                        type: "POST",
                        url: BASE_URL+'rest/V1/sms/verification/send',
                        dataType: "json",
                        data: JSON.stringify({"phone_number": $(self.options.telephoneSelector).val(), "check_exist_customer_phone": false}),
                        showLoader: true,
                        beforeSend: function(xhr){
                            xhr.setRequestHeader('Accept', 'application/json');
                            xhr.setRequestHeader('Content-Type', 'application/json');
                        },
                        statusCode: {
                            400: function() {
                                $(self.options.telephoneMsg).show().text(self.options.systemMessage);
                            },
                            404: function() {
                                $(self.options.telephoneMsg).show().text(self.options.systemMessage);
                            },
                            500: function() {
                                $(self.options.telephoneMsg).show().text(self.options.systemMessage);
                            },
                            200: function() {
                                $(self.options.telephoneMsg).hide();
                                var action = $(self.options.sendOTPSelector).attr('action');
                                if (action == 'send') {
                                    $(self.options.sendOTPSelector).attr('action', 're-send');
                                    $(self.options.sendOTPSelector).text($.mage.__('Resend Verification Code'));
                                } else if (!$(self.options.countdownSelector).find('.message-send').hasClass('re-send')) {
                                    $(self.options.countdownSelector).find('.message-send').addClass('re-send').text($.mage.__('Resend OTP successfully.'));
                                }
                                $(self.options.sendOTPSelector).removeClass('enable');
                                $(self.options.countdownSelector).show();
                                self.counter();
                            }
                        }
                    });
                },

                verifyOTP: function () {
                    var self = this;
                    $(self.options.otpMsg).hide();
                    $.ajax({
                        type: "POST",
                        url: BASE_URL+'rest/V1/sms/verification/verify',
                        dataType: "json",
                        showLoader: true,
                        data: JSON.stringify({"phone_number": $(self.options.telephoneSelector).val(), "verification_code": $(self.options.otpSelector).val(), "action": ""}),
                        beforeSend: function(xhr){
                            xhr.setRequestHeader('Accept', 'application/json');
                            xhr.setRequestHeader('Content-Type', 'application/json');
                        },
                        statusCode: {
                            400: function(response) {
                                self.options.validateOTP = false;
                                $(self.options.otpMsg).show().text(response.responseJSON.message);
                            },
                            404: function() {
                                self.options.validateOTP = false;
                                $(self.options.otpMsg).show().text(self.options.systemMessage);
                            },
                            500: function() {
                                self.options.validateOTP = false;
                                $(self.options.otpMsg).show().text(self.options.systemMessage);
                            },
                            200: function(response) {
                                self.options.validateOTP = true;
                                $(self.options.formSelector).submit();
                            }
                        }
                    });
                },

                counter: function() {
                    var self = this,
                        timeleft = self.options.countdownTime;
                    $(self.options.countdownSelector).find('.txt-second span').text(timeleft);
                    downTimer = setInterval(function(){
                        timeleft--;
                        $(self.options.countdownSelector).find('.txt-second span').text(timeleft);
                        if(timeleft <= 0){
                            clearInterval(downTimer);
                            $(self.options.sendOTPSelector).addClass('enable');
                        }
                    },1000);
                }

            }
        );
        return $.trans.telephone;
    }
);
