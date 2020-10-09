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
        'Magento_Ui/js/lib/view/utils/async',
        'mage/url',
        'dropdownDatePicker',
        'domReady!'
    ], function ($, async, urlBuilder) {
        'use strict';
        return function (config) {
            var dobSelector = config.dob,
                picturePreviewSelector = config.picturePreview,
                pictureSelector = config.picture,
                resetButton = config.reset,
                customerData = config.customer;

            $(pictureSelector).change(function() {
                showPreview(this);
            });

            function showPreview(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $(picturePreviewSelector).attr('src', e.target.result);
                        $(resetButton).click(function () {
                            $(picturePreviewSelector).attr('src', customerData.profile_picture);
                            $(pictureSelector).val("");
                        });
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }

            $(dobSelector).dateDropdowns({
                daySuffixes: false,
                required: true,
                wrapperClass: "dob-date-dropdown date-dropdowns",
                displayFormat: "dmy",
                submitFormat: "mm/dd/yyyy",
                defaultDateFormat: "mm/dd/yyyy",
                defaultDate: config.default
            });

            async.async('select[name="date_[day]"]', function () {
                $('select[name="date_[day]"]').wrap('<div class="dob-control-field"></div>');
            });

            async.async('select[name="date_[month]"]', function () {
                $('select[name="date_[month]"]').wrap('<div class="dob-control-field"></div>');
            });

            async.async('select[name="date_[year]"]', function () {
                $('select[name="date_[year]"]').wrap('<div class="dob-control-field"></div>');
            });

            $.ajax({
                type: "POST",
                url : urlBuilder.build("rest/V1/transcustomer/limitChangeDob"),
                success: function (response) {
                    if (response === false) {
                        $('select[name="date_[day]"]').prop('disabled', 'disabled');
                        $('select[name="date_[month]"]').prop('disabled', 'disabled');
                        $('select[name="date_[year]"]').prop('disabled', 'disabled');
                    }
                }
            });

            $(resetButton).click(function () {
                $.each(customerData, function (key, value) {
                    if (key == 'profile_picture') {
                        $('.profile-picture-image').attr('src', value);
                    } else if (key == 'dob') {
                        var day = '',
                            month = '',
                            year = '';
                        if (value != '') {
                            var parts = value.split('/');
                            day = parts[0],
                            month = parts[1],
                            year = parts[2];
                        }
                        $('.dob-date-dropdown .day').val(day);
                        $('.dob-date-dropdown .month').val(month);
                        $('.dob-date-dropdown .year').val(year).trigger('change');
                    } else if (key == 'gender' || key == 'marital_status') {
                        $('input:radio[name="' + key + '"]:checked').prop('checked', false);
                        if (value != '') {
                            $('input:radio[name="' + key + '"]').filter('[value="' + value + '"]').prop('checked', true);
                        }
                    } else {
                        $('[name="' + key + '"]').val(value);
                    }
                });
            });
        }
    }
);
