/**
 * @category Trans
 * @package  Trans_Customer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Original Imam Kusuma <imam.kusuma@transdigital.co.id>
 * @author   Another  Dwi Septha Kurniawan <septha.kurniawan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

define([
	'jquery',
	'mage/url',
	'mage/translate',
    'jquery/ui',
    'jquery/validate'
], function($, urlBuilder) {
	'use strict'; 
	return function(popup) {
	
		$.widget('mageplaza.socialpopup', popup, {
			options: {
	            formLogin: '.form-customer-login',
	            passwordField: 'input[name="password"]',
	            usernameField: 'input[name="username"]',
	            usernameFieldById: 'input[id="social_login_email"]',
	            passwordOuter: '.field-password',
	            otpOuter: '.field-otp-popup',
	            username: '',
	            typeEmail: 'email',
	            typePhone: 'phone'
	        },

            // processLogin: function () {
            	// $(this.options.formLogin).submit();
                // return this._super();
            // },

            /**
	         * Init button click
	         */
	        initObserve: function () {
	            this._super()
	            
	            $(this.options.usernameFieldById).on('change', this.processUsername.bind(this));
	        },

	        /**
	         * Init links login
	         */
	        initLink: function () {
	            var self = this,
	                headerLink = $(this.options.headerLink);

	            if (headerLink.length) {
	                headerLink.find('a').each(function (link) {
	                    var el = $(this),
	                        href = el.attr('href');

	                    if (typeof href !== 'undefined' && (href.search('customer/account/login') !== -1)) {
	                        el.addClass('social-login');
	                        el.attr('href', self.options.popup);
	                        el.attr('data-effect', self.options.popupEffect);
	                        el.on('click', function (event) {
	                            self.showLogin();
	                            
	                            event.preventDefault();
	                        });
	                    }
	                });

	                headerLink.magnificPopup({
	                    delegate: 'a.social-login',
	                    removalDelay: 500,
	                    callbacks: {
	                        beforeOpen: function () {
	                            this.st.mainClass = this.st.el.attr('data-effect');
	                        }
	                    },
	                    midClick: true
	                });
	            }

	            this.options.createFormUrl = this.correctUrlProtocol(this.options.createFormUrl);
	            this.options.formLoginUrl = this.correctUrlProtocol(this.options.formLoginUrl);
	            this.options.forgotFormUrl = this.correctUrlProtocol(this.options.forgotFormUrl);
	            this.options.fakeEmailUrl = this.correctUrlProtocol(this.options.fakeEmailUrl);
	        },

	        /**
	         * Show create page
	         */
	        showCreate: function () {
	            
	        },

	        processUsername: function(args) {
	        	var username = $(this.options.usernameFieldById).val(); 
	        	this.options.username = username;
	        	this.checkUsername();
	        },

	        checkUsername: function() {
	        	$('.wrapper-msg-popup .message-popup').html('');
				$('.wrapper-msg-popup').hide();
                $('#social_login_email-error').remove();
                $(this.options.usernameFieldById).removeClass();
                $(this.options.usernameFieldById).addClass('input-text');
				$(this.options.passwordOuter).hide();
				$(this.options.otpOuter).hide();
				
	        	var username = this.options.username, email, phone, type = this.options.typeEmail, html;
				var emailReg = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
				var intRegex = /[0-9 -()+]+$/;
                var phoneNumberRegex = /^(0|08|08[0-9]{1,13})$/;
				
				type = this.checkUsernameType();

				if(type == this.options.typeEmail) {
					
					if(username == ''){
                    html = $.mage.__('This is a required field.');
                    $(this.options.usernameFieldById).after('<div for="social_login_email" generated="true" class="mage-error" id="social_login_email-error">'+html+'</div>');
	                $(this.options.usernameFieldById).addClass('mage-error');
	                $(this.options.usernameFieldById).focus();
	                    return false;
	                }

	                if(!emailReg.test(username))
	                {   
	                    html = $.mage.__('Please enter a valid email address. For example johndoe@domain.com.');
	                    $(this.options.usernameFieldById).after('<div for="social_login_email" generated="true" class="mage-error" id="social_login_email-error">'+html+'</div>');
	                	$(this.options.usernameFieldById).addClass('mage-error');
	                	$(this.options.usernameFieldById).addClass('validate-format-email');
	                	$(this.options.usernameFieldById).focus();
	                    return false;
	                }
					$(this.options.passwordOuter).show();
				}

				if(type == this.options.typePhone) {
					if((username.length < 6) || (!intRegex.test(username)))
	                {
	                    html = $.mage.__('Please enter a valid phone number.');
	                    $(this.options.usernameFieldById).after('<div for="social_login_email" generated="true" class="mage-error" id="social_login_email-error">'+html+'</div>');
	                	$(this.options.usernameFieldById).addClass('mage-error');
	                	$(this.options.usernameFieldById).addClass('validate-phone-number');
	                	$(this.options.usernameFieldById).focus();
	                	return false;
	                }

	                if(!phoneNumberRegex.test(username))
	                {
	                    html = $.mage.__('Please enter format mobile number (ex. 08xxxxxxxx) in this field.');
	                    $(this.options.usernameFieldById).after('<div for="social_login_email" generated="true" class="mage-error" id="social_login_email-error">'+html+'</div>');
	                	$(this.options.usernameFieldById).addClass('mage-error');
	                	$(this.options.usernameFieldById).addClass('validate-format-phone-number');
	                	$(this.options.usernameFieldById).focus();
	                	return false;
	                }
					this.sendVerification();
				}
	        },

	        sendVerification: function() {
	        	$('.wrapper-msg-popup .message-popup').html('');
				$('.wrapper-msg-popup').hide();
				var url = urlBuilder.build('rest/default/V1/customer/send-sms-verification', {}), telephone, postData, verification, isNeedCheck = 1, checkphone;

				telephone = $('input[name="username"]').val(); //get username value

				if(!telephone) {
					telephone = $('input[name="login[username]"]').val(); //get username value
				}
				
				postData = '{"telephone":"' + telephone + '", "isNeedCheck": ' + isNeedCheck + '}';
				
				verification = $.parseJSON(
	                $.ajax({
						type: 'post',
	                    showLoader: true,
	                    url: url,
	                    data: postData,
	                    contentType: 'application/json',
						cache: false,
	                    async: false
	                }).responseText
	            );

	            verification = $.parseJSON(verification);
				
				if(!verification.error) {
					$('input[name="verification_id"]').val(verification.verification_id);
					$(this.options.otpOuter).show();
				} else {
					var msg = verification.message ? verification.message : $.mage.__('Send verification code failed. Please make sure your phone number is valid and active.'); 
					if (verification.message.error_message){
	                    msg = verification.message.error_message;
	                } 
					$('.wrapper-msg-popup .message-popup').html(msg);
					$('.wrapper-msg-popup').show();
					$(this.options.otpOuter).hide();
				}
			},

	        /**
	         * Check username are email or phone number	
	         */
			checkUsernameType: function() {
				var type = this.options.typeEmail, intRegex = /[0-9 -()+]+$/;

				if(intRegex.test(this.options.username)) { type = this.options.typePhone; }

				return type;
			}
			
        });

        /**
         * Validation Email & Mobile Number
         */
        //validate format mobile number      
        $.validator.addMethod(
        'validate-format-phone-number', function (v, elm) {
            
            return v.match(/^(0|08|08[0-9]{1,13})$/);

        }, $.mage.__('Please enter format mobile number (ex. 08xxxxxxxx) in this field.'));
        
        //validate mobile number      
        $.validator.addMethod(
        'validate-phone-number', function (v, elm) {
            
            if(v.length < 6){
                return false;
            }
            return v.match(/[0-9 -()+]+$/);

        }, $.mage.__('Please enter a valid phone number.'));
        
        //validate format email      
        $.validator.addMethod(
        'validate-format-email', function (v, elm) {
            
            return v.match(/^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i);

        }, $.mage.__('Please enter a valid email address. For example johndoe@domain.com.'));

        return $.mageplaza.socialpopup;
	};
});