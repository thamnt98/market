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

        $.widget(
            'trans.email', {
                _create: function () {
                    this.options.systemMessage = $.mage.__('System error, please try again later.');
                    this.options.validateEmail = true;
                    this.emailChange();
                    this.submitForm();
                    this.resetEmail();
                    this.sendVerificationLink();
                },

                submitForm: function () {
                    var self = this;
                    $(self.options.formSelector).submit(function (e) {
                        var validateEmail = $.validator.validateSingleElement($(self.options.emailSelector));
                        if (!validateEmail) {
                            e.preventDefault();
                            $(self.options.emailMsg).hide();
                            return;
                        }
                        if (!self.options.validateEmail) {
                            e.preventDefault();
                            self.emailExist();
                            return;
                        }
                    })
                },

                emailChange: function () {
                    var self = this;
                    $(self.options.emailSelector).change(function () {
                        self.options.validateEmail = false;
                    });
                },

                emailExist: function () {
                    var self = this;
                    $.ajax({
                        type: "POST",
                        url: BASE_URL+'rest/V1/customers/existUser',
                        dataType: "json",
                        data: JSON.stringify({"user": $(self.options.emailSelector).val(), "type": "email"}),
                        showLoader: true,
                        beforeSend: function(xhr){
                            xhr.setRequestHeader('Accept', 'application/json');
                            xhr.setRequestHeader('Content-Type', 'application/json');
                        },
                        statusCode: {
                            400: function(response) {
                                var message = response.responseJSON.message;
                                $(self.options.emailMsg).show().text(message);
                            },
                            404: function(response) {
                                var message = response.responseJSON.message;
                                $(self.options.emailMsg).show().text(message);
                            },
                            500: function() {
                                $(self.options.emailMsg).show().text(self.options.systemMessage);
                            },
                            200: function(response) {
                                if (!response) {
                                    $(self.options.emailMsg).hide();
                                    self.options.validateEmail = true;
                                    $(self.options.formSelector).submit();
                                } else {
                                    $(self.options.emailMsg).text($.mage.__('Your email has already been registered')).show();
                                }
                            }
                        }
                    });
                },

                resetEmail: function () {
                    var self = this;
                    $(self.options.resetButtonSelector).click(function () {
                        $(self.options.emailSelector).val(self.options.email);
                        self.options.validateEmail = true;
                        $(self.options.emailMsg).hide();
                        $.validator.validateSingleElement($(self.options.emailSelector));
                    });
                },

                sendVerificationLink: function () {
                    var self = this;
                    $(self.options.sendVerificationLinkSelector).click(function () {
                        $.ajax({
                            type: "POST",
                            url: BASE_URL+'rest/V1/customers/sendVerificationLink',
                            dataType: "json",
                            data: JSON.stringify({"email": self.options.email}),
                            showLoader: true,
                            beforeSend: function(xhr){
                                xhr.setRequestHeader('Accept', 'application/json');
                                xhr.setRequestHeader('Content-Type', 'application/json');
                            },
                            statusCode: {
                                400: function(response) {
                                    var message = response.responseJSON.message;
                                    $(self.options.emailMsg).show().text(message);
                                },
                                404: function(response) {
                                    var message = response.responseJSON.message;
                                    $(self.options.emailMsg).show().text(message);
                                },
                                500: function() {
                                    $(self.options.emailMsg).show().text(self.options.systemMessage);
                                },
                                200: function(response) {
                                    if (response) {
                                        $(self.options.emailMsg).text($.mage.__('Send Verification Link Success.')).show();
                                    } else {
                                        $(self.options.emailMsg).show().text(self.options.systemMessage);
                                    }
                                }
                            }
                        });
                    });
                }
            }
        );
        return $.trans.email;
    }
);
