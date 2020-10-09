define([
    'jquery'
], function ($) {
    "use strict";

    return function() {
        $.validator.addMethod(
            'validate-username',
            function (v) {
                return $.mage.isEmptyNoTrim(v) || /^([a-z0-9,!\#\$%&'\* \+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*@([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*\.(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]){2,})$/i.test(v)
                    || /^(^\+628|^628|^08|^8)\d+$/i.test(v); //eslint-disable-line max-len
            },
            $.mage.__('Make sure you follow the format')
        );
        $.validator.addMethod(
            'validate-customer-email',
            function (v) {
                return $.mage.isEmptyNoTrim(v) || /^([a-z0-9,!\#\$%&'\* \+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*@([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*\.(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]){2,})$/i.test(v); //eslint-disable-line max-len
            },
            $.mage.__('Make sure you follow the format')
        );

        $.validator.addMethod(
            'custom-validate-password',
            function (v) {
                var pass;

                if (v == null) {
                    return false;
                }
                //strip leading and trailing spaces
                pass = $.trim(v);

                if (!pass.length) {
                    return true;
                }

                if (!(/[0-9]/.test(pass) && /[a-z]/.test(pass) && /[A-Z]/.test(pass))) {
                    return false;
                }

                return !(pass.length > 0 && pass.length < 7);
            },
            $.mage.__('Minimum of 7 characters with combination of number & uppercase letters')
        );

        $.validator.addMethod(
            'validate-phone',
            function (v) {
                return $.mage.isEmptyNoTrim(v) ||  /^(^\+628|^628|^08|^8)\d+$/i.test(v);
            },
            $.mage.__('Make sure that format is 08xxxxxxxxxx')
        );

        $.validator.addMethod(
            'validate-phone-require',
            function (v) {
                var pass = $.trim(v);
                if (pass.length > 2) {
                    return true;
                }
            },
            $.mage.__('This field is required')
        );

        $.validator.messages = $.extend($.validator.messages, {
            equalTo: $.mage.__('Your passwords don\'t match')
        });

        $.validator.addMethod(
                'validate-trans-password',
                function (v, elm) {
                    var validator = this,
                        passwordMinLength = $(elm).data('password-min-length'),
                        pass = $.trim(v),
                        messageError = $.mage.__('Minimum of %1 characters with combination of number & uppercase letters').replace('%1', passwordMinLength);

                    if (pass.length < passwordMinLength) {
                        validator.passwordErrorMessage = messageError; //eslint-disable-line max-len
                        return false;
                    }

                    if (!pass.match(/\d+/)) {
                        validator.passwordErrorMessage = messageError; //eslint-disable-line max-len
                        return false;
                    }

                    if (!pass.match(/[a-z]+/)) {
                        validator.passwordErrorMessage = messageError; //eslint-disable-line max-len
                        return false;
                    }

                    if (!pass.match(/[A-Z]+/)) {
                        validator.passwordErrorMessage = messageError; //eslint-disable-line max-len
                        return false;
                    }
                    return true;
                }, function () {
                    return this.passwordErrorMessage;
                }
        );
        $.validator.addMethod(
            'required-radio',
            function (value) {
                return !$.mage.isEmpty(value);
            }, $.mage.__('Please select one!')
        );
    }
});
