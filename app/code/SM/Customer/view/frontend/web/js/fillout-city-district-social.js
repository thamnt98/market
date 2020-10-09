/**
 * @category SM
 * @package SM_Customer
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author Logan Ta <phuongtt2@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */
define(
    [
        'jquery',
        'mage/translate'
    ],
    function ($) {
        var triggerChangeTimeOut,
            citySelector = $('#social-register #city'),
            cityIdSelector = $('#social-register #cityId'),
            districtSelector = $('#social-register #district'),
            cityListSelector = $('#social-register #city-list'),
            emailSelector = $('#social-register #email'),
            cityCollection = getLocalStorage('city'),
            districtCollection = getLocalStorage('district');
        //function fill out city value to input
        fillOutCityListItem($, true, '', cityListSelector, citySelector, cityIdSelector, cityCollection);
        //get district by city
        citySelector.click(function () {
            if (!cityCollection) {
                cityCollection = getLocalStorage('city');
                districtCollection = getLocalStorage('district');
                fillOutCityListItem($, true, '', cityListSelector, citySelector, cityIdSelector, cityCollection);
            }
            cityListSelector.show();
        });
        citySelector.keyup(function (event) {
            if (!cityCollection) {
                cityCollection = getLocalStorage('city');
                districtCollection = getLocalStorage('district');
            }
            if (event.keyCode == 65 || event.keyCode == 67 || event.keyCode == 17) {
                return false;
            }
            cityIdSelector.val('');
            cityIdSelector.trigger('change');
            var currentCityName = this.value;
            clearTimeout(triggerChangeTimeOut);
            triggerChangeTimeOut = setTimeout(function () {
                var cityId = fillOutCityListItem($, false, currentCityName, cityListSelector, citySelector, cityIdSelector, cityCollection);
                if (cityId != '') {
                    cityIdSelector.val(cityId);
                    cityIdSelector.trigger('change');
                }
            }, 700);
        });
        cityIdSelector.change(function (event) {
            cityListSelector.hide();
            districtSelector.find('option').remove();
            getDistrict($, cityIdSelector.val(), citySelector, cityIdSelector, districtSelector, cityCollection, districtCollection);
        });
        districtSelector.mousedown(function (e) {
            if ($(this).hasClass('disable')) {
                e.preventDefault();
            }
        });
        var emailReg = /^([a-z0-9,!\#\$%&'\* \+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*@([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*\.(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]){2,})$/;
        emailSelector.change(function () {
            cityIdSelector.attr('district', '');
            var cityOld = getOldCityDistrict($);
            var emailValue = $(this).val();
            if (emailValue !== '' && emailReg.test(emailValue)) {
                if (citySelector.val() != '') {
                    saveOldCityDistrict($, emailValue, cityOld, citySelector, cityIdSelector, districtSelector);
                } else {
                    // $.each(cityOld, function (key, item) {
                    //     if (item.email == emailValue) {
                    //         if (item.cityId != '') {
                    //             citySelector.val(item.cityName);
                    //             cityIdSelector.val(item.cityId);
                    //             cityIdSelector.attr('district', item.districtId);
                    //             cityIdSelector.trigger('change');
                    //         } else {
                    //             citySelector.val(item.cityName);
                    //             fillOutCityListItem($, false, item.cityName, cityListSelector, citySelector, cityIdSelector, cityCollection);
                    //         }
                    //     }
                    //
                    // });
                }
            }
        });
        $(".dropdown-city").mouseleave(function () {
            cityListSelector.hide(500);
        });
    }
);

/**
 * Get city district model
 */
function getLocalStorage(item) {
    if (typeof (Storage) !== "undefined") {
        return JSON.parse(localStorage.getItem(item)).value;
    }

    return false;
}

/**
 * core function fill out city value to input
 */
function fillOutCityListItem($, firstLoad, currentCityName, cityListSelector, citySelector, cityIdSelector, cityCollection) {
    var cityId = '';
    cityIdSelector.attr('district', '');
    if (firstLoad) {
        $('#social-register #district').addClass('disable');
    }
    if (!$.isEmptyObject(cityCollection)) {
        if (!firstLoad) {
            cityListSelector.find('li').remove();
        }
        currentCityName = currentCityName.toLowerCase();
        $.each(cityCollection, function (id, city) {
            var cityLow = city.toLowerCase();
            if (currentCityName == cityLow) {
                cityId = id;
            }
            if (cityLow.indexOf(currentCityName) != -1) {
                cityListSelector.append(`<li city-id="${id}">${city}</li>`);
            }
        });
        if (!firstLoad) {
            cityListSelector.show();
        }
        cityListSelector.find('li').click(function () {
            var cityIdSelected = $(this).attr('city-id'),
                cityNameSelected = $(this).text();
            citySelector.val(cityNameSelected);
            cityIdSelector.val(cityIdSelected);
            cityIdSelector.trigger('change');
        });
        var emailReg = /^([a-z0-9,!\#\$%&'\* \+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*@([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*\.(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]){2,})$/;
        var emailValue = $('#register #email').val();
        if (emailValue !== '' && emailReg.test(emailValue)) {
            var cityOld = getOldCityDistrict($);
            saveOldCityDistrict($, emailValue, cityOld, citySelector, cityIdSelector, $('#district'));
        }
        return cityId;
    }
}

/**
 * get District by city
 */
function getDistrict($, cityId, citySelector, cityIdSelector, districtSelector, cityCollection, districtCollection) {
    if (cityId != '' && typeof districtCollection[cityId] !== "undefined") {
        //get district option
        districtSelector.removeClass('disable');
        var i = 0,
            selectedId = 0;
        $.each(districtCollection[cityId], function (id, districtName) {
            i++;
            if (i == 1) {
                selectedId = id;
            }
            if (cityIdSelector.attr('district') == id) {
                selectedId = id;
            }
            districtSelector.append(`<option value="${id}">${districtName} </option>`);
        });
        if (selectedId !== 0) {
            districtSelector.val(selectedId);
            districtSelector.trigger('change');
        }
        districtSelector.change(function () {
            var emailReg = /^([a-z0-9,!\#\$%&'\* \+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*@([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*\.(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]){2,})$/;
            var emailValue = $('#register #email').val();
            if (emailValue !== '' && emailReg.test(emailValue)) {
                var cityOld = getOldCityDistrict();
                saveOldCityDistrict($, emailValue, cityOld, citySelector, cityIdSelector, districtSelector);
            }
        })
    } else {
        if (districtSelector.find('option').length == 0) {
            var districtName = $.mage.__('District');
            districtSelector.append(`<option value>${districtName}</option>`);
            districtSelector.addClass('disable');
        }
    }
}

function getOldCityDistrict($) {
    if (typeof localStorage.getItem("city-old") === "undefined"
        || localStorage.getItem("city-old") === "null"
    ) {
        var cityOld = {};
        cityOld.count = 0;
    } else {
        var cityOld = JSON.parse(localStorage.getItem("city-old"));
    }
    return cityOld;
}

function saveOldCityDistrict($, emailValue, cityOld, citySelector, cityIdSelector, districtSelector) {
    var data = {cityId: '', cityName: '', districtId: '', email: ''};
    data.email = emailValue;
    data.cityName = citySelector.val();
    if (cityIdSelector.val() != '') {
        data.cityId = cityIdSelector.val();
    }
    data.districtId = districtSelector.val();
    var insert = true;
    $.each(cityOld, function (key, item) {
        if (item.email == emailValue) {
            cityOld[key] = data;
            insert = false;
            return false;
        }

    });
    if (insert) {
        var next = cityOld.count + 1;
        cityOld['count'] = next;
        cityOld[next] = data;
    }
    localStorage.setItem("city-old", JSON.stringify(cityOld));
}
