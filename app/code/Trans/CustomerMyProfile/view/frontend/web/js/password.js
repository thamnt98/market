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
        'mage/url',
        'SuggestPassword',
        'mage/translate',
        'mage/validation',
        'mage/mage'
    ], function ($, urlBuilder, SuggestPassword) {
        'use strict';

        $.widget(
            'trans.pass', {
                _create: function () {
                    this.options.systemMessage = $.mage.__('System error, please try again later.');
                    this.submitForm();
                    this.resetPassWord();
                    SuggestPassword.create($(this.options.formSelector));
                },

                submitForm: function () {
                    var self = this;

                    $(self.options.formSelector).submit(function (e) {
                        e.preventDefault();

                        $(self.options.passMsg).hide();
                        $(self.options.newPassMsg).hide();
                        $('#current-password').removeClass('mage-error');
                        $('#password').removeClass('mage-error');

                        var validateCurrentPassword = $.validator.validateSingleElement($(self.options.currentPassSelector));

                        if (!validateCurrentPassword) {
                            $(self.options.passMsg).hide();
                            return;
                        } else  {
                            $('#password').addClass('mage-error');
                        }

                        var validatePassword = $.validator.validateSingleElement($(self.options.newPasswordSelector));
                        if (!validatePassword) {
                            return;
                        }

                        var validateRePassword = $.validator.validateSingleElement($(self.options.newRePasswordSelector));
                        if (!validateRePassword) {
                            return;
                        }
                        self.changePassword();
                    })
                },

                changePassword: function () {
                    var self = this;
                    $.ajax({
                        type: "POST",
                        url: $(self.options.formSelector).attr('action'),
                        dataType: "json",
                        data: JSON.stringify({"email": self.options.email, "current_password": $(self.options.currentPassSelector).val(), "new_password": $(self.options.newPasswordSelector).val(),"os":'web'}),
                        showLoader: true,
                        beforeSend: function(xhr){
                            xhr.setRequestHeader('Accept', 'application/json');
                            xhr.setRequestHeader('Content-Type', 'application/json');
                        },
                        statusCode: {
                            400: function(response) {
                                var message = response.responseJSON.message;
                                $(self.options.passMsg).show().text(message);
                            },
                            404: function(response) {
                                var message = response.responseJSON.message;
                                $(self.options.passMsg).show().text(message);
                            },
                            500: function() {
                                $(self.options.passMsg).show().text(self.options.systemMessage);
                            },
                            200: function(response) {
                                var result = $.parseJSON(response);
                                if (result.status) {
                                    $(self.options.passMsg).hide();
                                    location.href = urlBuilder.build('customer/account/');
                                    $('.message-success').hide();
                                } else {
                                    var currentPassMessage = $.mage.__('This is not your current password'),
                                        newPassMessage = $.mage.__("You can't use your old password");

                                    if (result.newPassErr) {
                                        $('#password').addClass('mage-error');
                                        $(self.options.newPassMsg).show().text(newPassMessage);
                                    } else {
                                        $('#current-password').addClass('mage-error');
                                        if (result.curPassErr) {
                                            $(self.options.passMsg).show().text(currentPassMessage);
                                        } else {
                                            $(self.options.passMsg).show().text(result.message);
                                        }
                                    }
                                }
                            }
                        }
                    });
                },

                resetPassWord: function () {
                    var self = this;
                    $(self.options.resetButtonSelector).click(function () {
                        $(self.options.emailSelector).val(self.options.email);
                        self.options.validateEmail = true;
                        $(self.options.emailMsg).hide();
                        $.validator.validateSingleElement($(self.options.emailSelector));
                    });
                }
            }
        );
        return $.trans.pass;
    }
);
