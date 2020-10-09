define(
    [
    'jquery',
    'Magento_Customer/js/customer-data',
    'Magento_Ui/js/modal/modal',
    'ValidateEmail',
    'ValidatePhone',
    'FilloutCityDistrict',
    'OtpVerification',
    'SuggestPassword',
    'mage/validation'
    ], function ($, customerData, modal, validateEmail, validatePhone, fillOutCityDistrict, OtpVerification) {
        'use strict';
        var socialRegisterFormSelector = $('body');

        window.socialRegisterCallback = function (type, name, email, phone, windowObj) {
            var socialRegisterForm = $('#social-register'),
                options = {
                type: 'popup',
                responsive: true,
                innerScroll: false,
                title: '',
                buttons: [],
                clickableOverlay: false,
                opened: function($Event) {
                    $('.modal-header button.action-close', $Event.srcElement).hide();
                },
            };
            modal(options, socialRegisterForm);
            if ($('#tab-login').closest('.modal-popup').hasClass('_show')) {
                $('#tab-login').modal('closeModal');
            }
            if ($('#register').closest('.modal-popup').hasClass('_show')) {
                $('#register').modal('closeModal');
            }
            windowObj.close();
            socialRegisterFormSelector = socialRegisterForm.modal('openModal').show()
                .closest('.modal-popup')
                .addClass('modal-popup-signin');
            validateEmail.create(socialRegisterFormSelector);
            validatePhone.create(socialRegisterFormSelector);
            fillOutCityDistrict.create(socialRegisterFormSelector);
            if (phone && phone != '') {
                phone = phone.replace(/\D/g,'');
                if (phone.substr(0, 2) != '08') {
                    phone = '08' + phone;
                }
            }
            if (phone != '') {
                socialRegisterFormSelector.find('#social-phone').val(phone).trigger('change');
            }
            socialRegisterFormSelector.find('#social-name').change(function () {
                $.validator.validateSingleElement($(this));
            });

            //detected type social login to show icon
            if (type === 'Facebook') {
                socialRegisterForm.find('.sociallogin-facebook-icon-image').show();
                socialRegisterForm.find('.sociallogin-google-icon-image').hide();
            } else {
                socialRegisterForm.find('.sociallogin-google-icon-image').show();
                socialRegisterForm.find('.sociallogin-facebook-icon-image').hide();
            }

            socialRegisterFormSelector.find('.block-subtitle--name').text(name);
            socialRegisterFormSelector.find('#social-name').val(name);
            if (email != '') {
                socialRegisterFormSelector.find('#social-email').val(email).trigger('change');
            }
            socialRegisterFormSelector.find('#social-phone').keyup(function(e) {
                if (this.value.length < 2) {
                    this.value = '08';
                } else if (this.value.indexOf('08') !== 0) {
                    this.value = '08' + String.fromCharCode(e.which);
                }
            });
            socialRegisterFormSelector.find('#login').click(function () {
                socialRegisterForm.modal('closeModal');
                $('#tab-login').modal('openModal').show();
            });
        };

        socialRegisterFormSelector.on('click', '#button-social-register', function(){
            verifyOtp();
        });

        function verifyOtp() {
            event.preventDefault();

            var dataForm = socialRegisterFormSelector.find('.form-create-account');
            var ignore = null;

            dataForm.mage('validation', {
                ignore: ignore ? ':hidden:not(' + ignore + ')' : ':hidden'
            }).find('input:text').attr('autocomplete', 'off');

            dataForm.validation('isValid');
            if (dataForm.validation('isValid')===true)
            {
                $('#phone-otp').text(socialRegisterFormSelector.find("input[name='telephone']").val());
                OtpVerification.sendOtp('social_login', socialRegisterFormSelector.find("input[name='telephone']").val(), false, $('#otp-error'), $('#social-register'), function () {
                    $(document).trigger('customer:register');
                    dataForm.submit();
                });
            }
        }
    }
);
