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
        'ForgotPassword',
        'gtmSha256',
        'mage/translate',
        'mage/validation',
        'mage/mage'
    ],
    function ($, modal, ForgotPassword, sha256) {
        'use strict';

        var downloadTimer,
            tabLoginModal,
            formEnableByCounter = true,
            step = 1,
            caseCustomerOnEco = false,
            caseSentOTP = false,
            caseResendOTP = false;

        $.widget(
            'sm.login',
            {
                _create: function () {
                    this.init();
                    this.submitForm();
                    this.editEmail();
                    this.showPassword();
                    this.backAction();
                    this.initInputEvent();
                    this.forgotAction();
                },

                init: function () {

                    /* Custom select design */
                    $('.drop-down').append('<div class="button"></div>');
                    $('.drop-down').append('<ul class="select-list"></ul>');
                    $('.drop-down select option').each(function () {
                        var bg = $(this).css('background-image');
                        bg = bg.replace('url("','').replace('")','');
                        var dataHref = $(this).attr("data-href");
                        var isSelect = $(this).attr("data-is-select");
                        $('.select-list').append('' +
                            '<li class="clsAnchor ' + ((isSelect == 1)? 'active' : '') + '">' +
                            '<a href="' + (!(isSelect == 1)? dataHref : '#') + '">' +
                            '<span value="' + $(this).val() + '" class="' + $(this).attr('class') +
                            '">' +
                            '<img class="store-view-fags" src="'+  bg +'" alt="">' +
                            $(this).text() +
                            '</span>' +
                            '</a>' +
                            '</li>');
                    });

                    var img = $('.drop-down select').find(':selected').css('background-image');
                    img = img.replace('url("','').replace('")','');
                    var storeCode = 'id_ID';
                    $('.drop-down .button').html('' +
                        '<a href="javascript:void(0);" class="select-list-link">' +
                        '<span>' +
                        '<img class="store-view-fags" src="'+  img +'" alt="">' +
                        $('.drop-down select').find(':selected').text() +
                        '<i class="triangle-down"> </i>' +
                        '</span>' +
                        '</a>');
                    // $('.drop-down ul li').each(function() {
                    //     if ($(this).find('span').text() == $('.drop-down select').find(':selected').text()) {
                    //         $(this).addClass('active');
                    //     }
                    // });
                    $('.drop-down .select-list span').on('click', function () {
                        var dd_text = $(this).text();
                        var dd_img = $(this).children('img').attr('src');
                        var dd_val = $(this).attr('value');
                        $('.drop-down .button').html('' +
                            '<a href="javascript:void(0);" class="select-list-link">' +
                            '<span>' +
                            '<img class="store-view-fags" src="' + dd_img + '" alt="" />' +
                            dd_text +
                            '<i class="triangle-down"> </i>' +
                            '</span>' + '' +

                            '</a>');
                        // $('.drop-down .select-list span').parent().removeClass('active');
                        // $(this).parent().addClass('active');
                        $('.drop-down select[name=options]').val(dd_val);
                        storeCode = dd_val;
                        $('.drop-down ul.select-list').slideUp();
                    });
                    $('.drop-down .button').on('click','a.select-list-link', function () {
                        $('.drop-down ul.select-list').slideToggle();
                    });

                    window.storeCode = storeCode;
                    window.count = window.count || 1;
                    var self = this,
                        options = {
                            type: 'popup',
                            responsive: true,
                            innerScroll: false,
                            title: '',
                            buttons: [],
                            modalClass: 'modal-popup-signin',
                            clickableOverlay: false,
                            keyEventHandlers: {
                                escapeKey: function () {
                                    return;
                                }
                            }
                    };
                    modal(options, $(self.options.tabLoginSelector));
                    var urlParams = new URLSearchParams(window.location.search);
                    if (!urlParams.has('recovery') && !urlParams.has('recoverytoken')) {
                        tabLoginModal = $(self.options.tabLoginSelector).modal('openModal').show();
                    } else {
                        tabLoginModal = $(self.options.tabLoginSelector);
                    }
                    tabLoginModal.find('input').each(function () {
                        $(this).change(function () {
                            $.validator.validateSingleElement($(this));
                            if ($(this).attr('name') == 'username') {
                                tabLoginModal.find('#user-not-exist').text('').hide();
                            }
                        })
                    });
                    $('body').on('click', '#register #login', function () {
                        $('#register').modal('closeModal');
                        $(self.options.tabLoginSelector).modal('openModal').show();
                    });
                },

                submitForm: function () {
                    const CUSTOMER_HAS_NOT_ON_ECOSYSTEM = 'hasNotOnEco';
                    const CUSTOMER_EXIST_ON_MAGENTO = 'existOnMagento';
                    const CUSTOMER_EXIST_ON_ECOSYSTEM = 'existOnEco';
                    const CUSTOMER_HAS_NOT_ON_MAGENTO = 'hasNotOnMagento';
                    var self = this;
                    tabLoginModal.find('form').submit(function (e) {
                        e.preventDefault();

                        var usernameElm = document.getElementById('username');
                        var username = usernameElm.value;
                        usernameElm.value = username.trim();
                        if ($(this).valid()) {
                            if (step == 1) {
                                $.ajax({
                                    type: "POST",
                                    url: self.options.checkUserUrl,
                                    data: {user: $(this).find('input[name=username]').val()},
                                    success: function (result) {
                                        /** case if customer has existed on eco system*/
                                        if (result.customerEcosystem === CUSTOMER_EXIST_ON_ECOSYSTEM
                                            && result.customerOnMagento === CUSTOMER_HAS_NOT_ON_MAGENTO) {
                                            caseCustomerOnEco = true;
                                            self.showTitleWelcomeEcoUserField(result.customer_name);
                                            self.showNotifyWelcomeEcoUserField();
                                            self.showMobileNumberField(result.customer_phone);
                                            self.showDeliveryAndDistrictField();
                                            self.showButtonLogin();
                                            self.hideFormLoginAfter();
                                            self.addEcoDataToHidenField(
                                                result.customer_firstname,
                                                result.customer_lastname,
                                                result.customer_email,
                                                result.customer_phone
                                            );
                                            tabLoginModal.find('button[id=form-login]').text($.mage.__('Sign In'));
                                        } else {
                                            /** case customer has not on ecosystem*/
                                            caseCustomerOnEco = false;
                                            if (result.message == '') {
                                                tabLoginModal.find('#user-not-exist').text(result.message).hide();
                                                if (result.type == 'email') {
                                                    caseCustomerOnEco = false;
                                                    self.showPasswordField();
                                                } else {
                                                    caseCustomerOnEco = false;
                                                    self.sendOTP('send');
                                                }
                                            } else {
                                                $(document).trigger('customer:login-fail-email-not-registered');
                                                if (result.status == true) {
                                                    tabLoginModal.modal('closeModal');
                                                    $('#tab-lock').modal('openModal').show();
                                                } else {
                                                    tabLoginModal.find('#user-not-exist').text(result.message).show();
                                                }
                                            }
                                        }
                                    },
                                });
                            } else if (step == 2 || step == 3) {
                                if (caseCustomerOnEco) {
                                    if (!caseSentOTP && step == 2) {
                                        $.ajax({
                                            type: "POST",
                                            url: self.options.checkUserUrl,
                                            data: {user: $(this).find('input[name=username]').val()},
                                            success: function (result) {
                                                if (result.message) {
                                                    self.sendOTP('send');
                                                } else {
                                                    tabLoginModal.find('#user-not-exist')
                                                        .text($.mage.__("Your email has already been registered")).show();
                                                }
                                            }
                                        });
                                    }
                                    if (step == 3) {
                                        self.processLogin();
                                    }
                                } else {
                                    self.processLogin();
                                }
                            }
                        } else {
                            tabLoginModal.find('#user-not-exist').text('').hide();
                        }
                    });
                },

                showPasswordField: function () {
                    step = 2;
                    tabLoginModal.find('label[for=username] span').text($.mage.__('Email'));
                    tabLoginModal.find('input[name=username]').attr('readonly', true).addClass('readonly');
                    tabLoginModal.find('input[name=password]').prop("disabled", false);
                    tabLoginModal.find('[selector=password-block]').show();
                    setTimeout(function () {
                        tabLoginModal.find('input[name=password]').focus();
                    }, 200);
                    tabLoginModal.find('[selector=edit]').show();
                    tabLoginModal.find('[selector=additional]').show();
                    tabLoginModal.find('[selector=action-back]').show();
                },

                showMobileNumberField: function (mobileNumber) {
                    step = 2;
                    var convertPhoneNumber = mobileNumber.replace("62", "0");
                    tabLoginModal.find('label[for=username] span').text($.mage.__('Email'));
                    tabLoginModal.find('label[for=mobile_number] span').text($.mage.__('Mobile number'));
                    tabLoginModal.find('[selector=mobile_number-block]').show();
                    $(tabLoginModal.find('input[name=mobile_number]')).val(convertPhoneNumber);
                    tabLoginModal.find('[selector=edit]').hide();
                    tabLoginModal.find('[selector=additional]').hide();
                    tabLoginModal.find('[selector=action-back]').show();
                },

                showDeliveryAndDistrictField: function () {
                    step = 2;
                    tabLoginModal.find('[selector=delivery-district]').show();
                },

                showTitleWelcomeEcoUserField: function (customerName) {
                    step = 2;
                    tabLoginModal.find('[selector=block-customer-login-heading]').hide();
                    tabLoginModal.find('[selector=welcome-eco-user]').show();
                    tabLoginModal.find('[selector=welcome-eco-fullname]').text(customerName);
                },

                showNotifyWelcomeEcoUserField: function () {
                    step = 2;
                    tabLoginModal.find('[selector=user-eco-confirm]').show();
                },

                showButtonLogin: function () {
                    step = 2;
                    tabLoginModal.find('button[id=form-login]').text($.mage.__('Sign In'));
                },

                hideFormLoginAfter: function () {
                    step = 2;
                    tabLoginModal.find('[selector=form-login-after]').hide();
                },

                addEcoDataToHidenField: function (firstname, lastname, email, phoneNumber) {
                    step = 2;
                    var convertPhoneNumber = phoneNumber.replace("62", "0");
                    $(tabLoginModal.find('input[name=eco_firstname]')).val(firstname);
                    $(tabLoginModal.find('input[name=eco_lastname]')).val(lastname);
                    $(tabLoginModal.find('input[name=eco_email]')).val(email);
                    $(tabLoginModal.find('input[name=eco_phonenumber]')).val(convertPhoneNumber);
                    $(tabLoginModal.find('input[name=username]')).val(email);
                },

                sendOTP: function (type) {
                    var self = this,
                        errorMessage = $.mage.__('System error, please try again later.'),
                        data = {};
                    if (caseCustomerOnEco) {
                        data.phone_number = tabLoginModal.find('input[name=mobile_number]').val();
                    } else {
                        data.phone_number = tabLoginModal.find('input[name=username]').val();
                    }
                    data.check_exist_customer_phone = false;
                    tabLoginModal.find('#user-not-exist').text('').hide();
                    tabLoginModal.find('#message-otp-error').text('').hide();

                    $.ajax({
                        type: "POST",
                        url: self.options.sendOTPUrl,
                        dataType: "json",
                        data: JSON.stringify(data),
                        showLoader: true,
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader('Accept', 'application/json');
                            xhr.setRequestHeader('Content-Type', 'application/json');
                        },
                        statusCode: {
                            400: function (response) {
                                tabLoginModal.find('#message-otp-error').text(response.responseJSON.message).show();
                            },
                            403: function (response) {
                                tabLoginModal.find('#user-not-exist').text(response.responseJSON.message).show();
                            },
                            404: function () {
                                if (type == 'send') {
                                    tabLoginModal.find('#user-not-exist').text(errorMessage).show();
                                } else {
                                    tabLoginModal.find('#message-otp-error').text(errorMessage).show();
                                }
                            },
                            500: function () {
                                if (type == 'send') {
                                    tabLoginModal.find('#user-not-exist').text(errorMessage).show();
                                } else {
                                    tabLoginModal.find('#message-otp-error').text(errorMessage).show();
                                }
                            },
                            200: function () {
                                step = 3;
                                if (type == 'send') {
                                    self.showOTPContent();
                                } else {
                                    self.counter();
                                }
                            }
                        }
                    });
                },

                showOTPContent: function () {
                    var self = this;
                    /** hide element case caseCustomerOnEco*/
                    if (caseCustomerOnEco) {
                        tabLoginModal.find('[selector=welcome-eco-user]').hide();
                        tabLoginModal.find('[selector=user-eco-confirm]').hide();
                        tabLoginModal.find('[selector=username-block]').hide();
                        tabLoginModal.find('[selector=mobile_number-block]').hide();
                        tabLoginModal.find('[selector=password-block]').hide();
                        tabLoginModal.find('[selector=delivery-district]').hide();
                    }
                    tabLoginModal.find('#block-customer-login-heading').show();
                    tabLoginModal.find('#block-customer-login-heading').text($.mage.__('Verification'));
                    tabLoginModal.find('button[id=form-login]').text($.mage.__('Continue'));
                    tabLoginModal.find('[selector=username-block]').hide();
                    tabLoginModal.find('[selector=additional]').hide();
                    tabLoginModal.find('[selector=form-login-after]').hide();
                    tabLoginModal.find('[selector=otp-block] input').each(function () {
                        $(this).prop("disabled", false);
                    });
                    tabLoginModal.find('[selector=otp-block]').show();
                    if (caseCustomerOnEco) {
                        var mobileNumber = tabLoginModal.find('input[name=mobile_number]').val(),
                            prefixMobileCutString = mobileNumber.slice(0, 4),
                            convertPhoneNumber = prefixMobileCutString + 'xxxxxxxx';
                        tabLoginModal.find('[selector=phone-otp]').text(convertPhoneNumber);
                    } else {
                        var mobileNumber = tabLoginModal.find('input[name=username]').val(),
                            prefixMobileCutString = mobileNumber.slice(0, 4),
                            convertPhoneNumber = prefixMobileCutString + 'xxxxxxxx';
                        tabLoginModal.find('[selector=phone-otp]').text(convertPhoneNumber);
                    }
                    setTimeout(function () {
                        tabLoginModal.find('input[name="digit-1"]').focus();
                    }, 200);
                    tabLoginModal.find('[selector=action-back]').show();
                    self.counter();
                    self.resendOTP();
                },

                counter: function () {
                    var self = this,
                        timeleft = self.options.count;
                    tabLoginModal.find('[selector=count] span').text(timeleft);
                    formEnableByCounter = true;
                    tabLoginModal.find('[selector=resend-otp]')
                        .addClass("txt-count")
                        .css({cursor:"not-allowed"});
                    downloadTimer = setInterval(function () {
                        if (timeleft > 0) {
                            timeleft--;
                        }
                        tabLoginModal.find('[selector=count] span').text(timeleft);
                        if (timeleft == 0) {
                            clearInterval(downloadTimer);
                            formEnableByCounter = false;
                            tabLoginModal.find('[selector=resend-otp]')
                                .removeClass("txt-count")
                                .css({cursor:"pointer"});
                        }
                    },1000);
                },

                resendOTP: function () {
                    var self =  this;
                    tabLoginModal.find('[selector=resend-otp]').on('click', function () {
                        if (!formEnableByCounter) {
                            clearInterval(downloadTimer);
                            self.sendOTP('resend');
                            if (caseCustomerOnEco) {
                                var phone_number = tabLoginModal.find('input[name=mobile_number]').val();
                            } else {
                                var phone_number = tabLoginModal.find('input[name=username]').val();
                            }

                            if (typeof phone_number !== 'undefined') {
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
                },

                processLogin: function () {
                    var self = this,
                        data = {};
                    //alert('step'+step);
                    if (step == 2) {
                        $.each(tabLoginModal.find('form').serializeArray(), function ( key, value ) {
                            data[value.name] = value.value;
                        });
                        tabLoginModal.find('#wrong-password-field').text('').hide();
                        tabLoginModal.find('#user-not-exist').text('').hide();
                        if (caseCustomerOnEco) {
                            data.firstname = tabLoginModal.find('input[name=eco_firstname]').val();
                            data.lastname = tabLoginModal.find('input[name=eco_lastname]').val();
                            data.email = tabLoginModal.find('input[name=username]').val();
                            data.phonenumber = tabLoginModal.find('input[name=eco_phonenumber]').val();
                            data.password = tabLoginModal.find('input[name=password]').val();
                            data.case_user_on_eco_system = true;
                            data.city = tabLoginModal.find('input[name=city]').val();
                            data.district = $('#tab-login select[name=district]').find('option:selected').val();
                            data.username = tabLoginModal.find('input[name=mobile_number]').val();
                            data.step = 2;
                        }
                        self.loginRequest(data);
                    } else if (step == 3) {
                        var formEnableByValidateFieldOTP = true,
                            otp = '';
                        tabLoginModal.find('[selector=otp-block] input').each(function () {
                            if (this.value.length !== 1 || !$.isNumeric(this.value)) {
                                $(this).addClass('mage-error');
                                formEnableByValidateFieldOTP = false;
                            } else {
                                $(this).removeClass('mage-error');
                            }
                            otp += this.value;
                        });
                        if (formEnableByValidateFieldOTP && formEnableByCounter) {
                            data.username = tabLoginModal.find('input[name=username]').val();
                            data.otp = otp;
                            if (caseCustomerOnEco) {
                                data.firstname = tabLoginModal.find('input[name=eco_firstname]').val();
                                data.lastname = tabLoginModal.find('input[name=eco_lastname]').val();
                                data.email = tabLoginModal.find('input[name=username]').val();
                                data.phonenumber = tabLoginModal.find('input[name=eco_phonenumber]').val();
                                data.password = tabLoginModal.find('input[name=password]').val();
                                data.case_user_on_eco_system = true;
                                data.city = tabLoginModal.find('input[name=city]').val();
                                data.district = $('#tab-login select[name=district]').find('option:selected').val();
                                data.username = tabLoginModal.find('input[name=mobile_number]').val();
                                data.step = 3;
                            }
                            self.loginRequest(data);
                        } else if (!formEnableByCounter) {
                            tabLoginModal.find('[selector=message-otp-error]').text($.mage.__('Make sure the code is correct')).show();
                        }
                    }
                },

                loginRequest: function (data) {
                    var self = this;

                    $.ajax({
                        type: "POST",
                        url: self.options.loginUrl,
                        data: JSON.stringify(data),
                        showLoader: true,
                        success: function (result) {
                            if (result.errors === true) {
                                if (result.status === true) {
                                    tabLoginModal.modal('closeModal');
                                    $('#tab-lock').modal('openModal').show();
                                    return;
                                }
                                if (step == 2) {
                                    $(document).trigger('customer:login-fail-password-not-match');
                                    tabLoginModal.find('#wrong-password-field').text(result.message).show();
                                    tabLoginModal.find('#user-not-exist').text(result.message).show();
                                    tabLoginModal.find('.fieldInputText ').addClass('fieldInputError');

                                    if (!caseCustomerOnEco) {
                                        self.sendLockCustomer(data.username);
                                    }
                                } else if (step == 3) {
                                    tabLoginModal.find('[selector=message-otp-error]').text(result.message).show();
                                    if (caseCustomerOnEco) {
                                        var phone_number = tabLoginModal.find('input[name=mobile_number]').val();
                                    } else {
                                        var phone_number = tabLoginModal.find('input[name=username]').val();
                                    }

                                    if (typeof phone_number !== 'undefined') {
                                        window.dataLayer = window.dataLayer || [];
                                        window.dataLayer.push({
                                            'event': "otp_failed",
                                            'phone_number': sha256(phone_number)
                                        });
                                    }
                                }
                            } else {
                                if (step == 3) {
                                    $(document).trigger('customer:otp');
                                }
                                tabLoginModal.find('#wrong-password-field').text('').hide();
                                tabLoginModal.find('#user-not-exist').text('').hide();
                                tabLoginModal.find('[selector=message-otp-error]').text('').hide();
                                tabLoginModal.find('.fieldInputText ').removeClass('fieldInputError');
                                $(document).trigger('customer:login');
                                caseCustomerOnEco = false;
                                window.location.href = result.redirectUrl;
                            }
                        },
                    });
                },

                editEmail: function () {
                    tabLoginModal.find('[selector=edit]').on('click', function () {
                        tabLoginModal.find('[selector=action-back]').trigger('click');
                    })
                },

                backAction: function () {
                    tabLoginModal.find('[selector=action-back]').click(function () {
                        if (step == 2 && !caseCustomerOnEco) {
                            step = 1;
                            $(this).hide();
                            tabLoginModal.find('label[for=username] span').text($.mage.__('Email or Mobile Number'));
                            tabLoginModal.find('input[name=username]').attr('readonly', false).removeClass('readonly');
                            tabLoginModal.find('input[name=password]').prop("disabled", true);
                            tabLoginModal.find('[selector=password-block]').hide();
                            tabLoginModal.find('[selector=edit]').hide();
                            tabLoginModal.find('[selector=additional]').hide();
                            tabLoginModal.find('[selector=action-back]').hide();
                            tabLoginModal.find('[selector=edit]').hide();
                        }

                        if (step == 3 && !caseCustomerOnEco) {
                            step = 1;
                            clearInterval(downloadTimer);
                            tabLoginModal.find('#block-customer-login-heading').text($.mage.__('Sign In'));
                            tabLoginModal.find('[selector=username-block]').show();
                            tabLoginModal.find('[selector=form-login-after]').show();
                            tabLoginModal.find('[selector=otp-block] input').each(function () {
                                $(this).val('').prop("disabled", true);
                            });
                            tabLoginModal.find('[selector=otp-block]').hide();
                            tabLoginModal.find('[selector=action-back]').hide();
                        }

                        if (step == 2 && caseCustomerOnEco) {
                            step = 1;
                            $(this).hide();
                            var mobileNumber = tabLoginModal.find('input[name=mobile_number]').val();

                            tabLoginModal.find('[selector=block-customer-login-heading]').show();
                            tabLoginModal.find('#block-customer-login-heading').text($.mage.__('Sign In'));
                            tabLoginModal.find('label[for=username] span').text($.mage.__('Email or Mobile Number'));
                            tabLoginModal.find('[selector=welcome-eco-user]').hide();
                            tabLoginModal.find('[selector=user-eco-confirm]').hide();
                            $(tabLoginModal.find('input[name=username]')).val(mobileNumber);
                            tabLoginModal.find('[selector=mobile_number-block]').hide();
                            tabLoginModal.find('[selector=action-back]').hide();
                            tabLoginModal.find('[selector=edit]').hide();
                            tabLoginModal.find('[selector=mobile_number-block]').hide();
                            tabLoginModal.find('[selector=delivery-district]').hide();
                            tabLoginModal.find('[selector=form-login-after]').show();
                            tabLoginModal.find('input[name=password]').prop("disabled", true);
                            tabLoginModal.find('[selector=password-block]').hide();
                        }

                        if (step == 3 && caseCustomerOnEco) {
                            step = 2;
                            clearInterval(downloadTimer);
                            tabLoginModal.find('#block-customer-login-heading').show();
                            tabLoginModal.find('[selector=username-block]').show();
                            tabLoginModal.find('[selector=otp-block] input').each(function () {
                                $(this).val('').prop("disabled", true);
                            });
                            tabLoginModal.find('[selector=otp-block]').hide();
                            tabLoginModal.find('[selector=block-customer-login-heading]').hide();
                            tabLoginModal.find('[selector=welcome-eco-user]').show();
                            tabLoginModal.find('[selector=user-eco-confirm]').show();
                            tabLoginModal.find('[selector=mobile_number-block]').show();
                            tabLoginModal.find('[selector=password-block]').hide();
                            tabLoginModal.find('[selector=delivery-district]').show();
                        }
                    });
                },

                showPassword: function () {
                    tabLoginModal.find('[selector=show-password]').on('click', function () {
                        var passwordField = tabLoginModal.find('input[name=password]');
                        if (passwordField.attr('type') === 'password') {
                            passwordField.attr('type', 'text');
                        } else {
                            passwordField.attr('type', 'password');
                        }
                        $(this).toggleClass('show-password');
                    });
                },

                initInputEvent: function () {
                    tabLoginModal.find('[selector=otp-block] input').each(function () {
                        // todo why do not move to inside 48 <= keyCode <= 57
                        $(this).on("keyup touchend",function (e) {
                            var parent = $(this).parent();
                            parent.removeClass('wrong');
                            if (e.keyCode === 8 || e.keyCode === 37) {
                                var prev = parent.find('input[name=' + $(this).data('previous') + ']');

                                if (prev.length) {
                                    prev.select();
                                }
                            }
                            if (this.value.length === 1 && $.isNumeric(this.value)) {
                                $(this).removeClass('mage-error');
                            } else {
                                if (!$(this).hasClass('mage-error')) {
                                    $(this).addClass('mage-error');
                                }
                            }
                        });
                        $(this).on("keypress touchend", function (e) {
                            var parent = $(this).parent();
                            var reg = new RegExp(/^\d+$/);
                            if (((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 96 && e.keyCode <= 105) || e.keyCode === 39)&&(reg.test(String.fromCharCode(e.keyCode)))) {
                                var next = parent.find('input[name=' + $(this).data('next') + ']');
                                if (next.length) {
                                    next.select();
                                } else {
                                    if (parent.data('autosubmit')) {
                                        parent.submit();
                                    }
                                }
                            }
                        });

                        $(this).on("keydown touchend",function (e) {
                            if ($(this).val().length >= 1&&((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 96 && e.keyCode <= 105) || e.keyCode === 39)) {
                                $(this).removeClass('mage-error');
                                e.preventDefault();
                            }
                        });
                    });
                },

                forgotAction: function () {
                    tabLoginModal.find('#action-forgot-password').click(function () {
                        $('#tab-forgot-password').removeClass('not-open')
                        ForgotPassword.openPopup();
                    });
                },

                sendLockCustomer: function (user) {
                    var self = this;
                    $.ajax({
                        type: "POST",
                        url: self.options.sendRecoveryUrl,
                        data: {user: user, type: 'lock'},
                        showLoader: false,
                        success: function (result) {

                        },
                    });
                }
            }
        );
        return $.sm.login;
    }
);
