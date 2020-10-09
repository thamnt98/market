define(
    [
        'jquery',
        'mage/translate',
        'mage/validation',
    ],
    function ($) {
        let mod = {};
        mod.create = function (formSelector) {
            var passwordField = formSelector.find('#password'),
                suggestPassword = formSelector.find('#suggestion-password'),
                sgTitle = formSelector.find('#sg-title'),
                sgPassword = formSelector.find('#sg-password'),
                confirmPassword = formSelector.find('#password-confirmation'),
                strongPassWordSelector = formSelector.find('#password-strength-meter-container'),
                nameSelector = formSelector.find("input[name='name']"),
                emailSelector= formSelector.find("input[name='email']"),
                telephoneSelector= formSelector.find("input[name='telephone']"),
                strongPassWord = '';

            // Generate a strong password string with mix cases
            var upToCase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
                lowToCase = 'abcdefghijklmnopqrstuvwxyz',
                number = '0123456789',
                special = '![]{}()%&*$#^<>~@|',
                possible = upToCase + lowToCase + number + special;
            strongPassWord += upToCase.charAt(Math.floor(Math.random() * upToCase.length));
            strongPassWord += lowToCase.charAt(Math.floor(Math.random() * lowToCase.length));
            strongPassWord += number.charAt(Math.floor(Math.random() * number.length));
            for (var i = 0; i < 9; i++) {
                strongPassWord += possible.charAt(Math.floor(Math.random() * possible.length));
            }
            strongPassWord = mod.shuffle(strongPassWord);

            //show pass word suggestion
            passwordField.focus(function(){
                $.validator.validateSingleElement(nameSelector);
                if (this.value == ''
                    && $.validator.validateSingleElement(nameSelector)
                    && $.validator.validateSingleElement(emailSelector)
                    && $.validator.validateSingleElement(telephoneSelector)
                ) {
                    suggestPassword.show();
                } else {
                    suggestPassword.hide();
                }
                var content = $.mage.__('Use Suggested Password');
                sgTitle.text(content);
                sgPassword.text(strongPassWord);
            });

            passwordField.keyup(function (event) {
                if (this.value == ''
                    && $.validator.validateSingleElement(nameSelector)
                    && $.validator.validateSingleElement(emailSelector)
                    && $.validator.validateSingleElement(telephoneSelector)
                ) {
                    strongPassWordSelector.hide();
                    suggestPassword.show();
                } else {
                    strongPassWordSelector.show();
                    suggestPassword.hide();
                }
            });

            // fill out values for field password and confirm password
            suggestPassword.on("click", function () {
                var copyPass = document.getElementById("sg-password");

                //copy to clipboard
                var range = document.createRange();
                range.selectNodeContents(copyPass);
                var sel = window.getSelection();
                sel.removeAllRanges();
                sel.addRange(range);
                document.execCommand('copy');

                //auto fill password suggested for customer
                passwordField.val(strongPassWord).trigger('change');
                confirmPassword.val(strongPassWord);
                suggestPassword.hide();
                strongPassWordSelector.show();
            });
        };

        mod.shuffle = function (string) {
            var parts = string.split('');
            for (var i = parts.length; i > 0;) {
                var random = parseInt(Math.random() * i);
                var temp = parts[--i];
                parts[i] = parts[random];
                parts[random] = temp;
            }
            return parts.join('');
        };

        return mod;
    }
);
