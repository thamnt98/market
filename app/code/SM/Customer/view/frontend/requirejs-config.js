var config = {
    config: {
        mixins: {
            'mage/validation': {
                'SM_Customer/js/validation-mixin': true
            }
        }
    },
    map: {
        '*': {
            ModalCreator: 'SM_Customer/js/lib/modal-creator',
            ValidateEmail: 'SM_Customer/js/validate-email',
            ValidatePhone: 'SM_Customer/js/validate-phone',
            SuggestPassword: 'SM_Customer/js/suggestion-password',
            FilloutCityDistrict: 'SM_Customer/js/fillout-city-district',
            FilloutCityDistrictSocial: 'SM_Customer/js/fillout-city-district-social',
            OtpVerification: 'SM_Customer/js/otp/verification',
            ForgotPassword: 'SM_Customer/js/forgot-password/form',
            ForgotPasswordConfirm: 'SM_Customer/js/forgot-password/confirm',
            RecoveryPassword: 'SM_Customer/js/recovery-password/form',
            CustomerSocialProvider: 'SM_Customer/js/social-login-provider',
            CityDistrictData: 'SM_Customer/js/city-district-data',
            ShowPopup: 'SM_Customer/js/show-popup',
        }
    }

};
