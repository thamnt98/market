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
        'mage/translate',
        'mage/validation'
    ],
    function ($) {
        let mod = {};
        var triggerChangeTimeOut,
            citySelector,
            cityIdSelector,
            cityListSelector,
            cityBlockSelector,
            districtSelector,
            emailSelector,
            cityCollection,
            districtCollection,
            districtFake,
            districtFakeCurrent,
            emailReg = /^([a-z0-9,!\#\$%&'\* \+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*@([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*\.(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]){2,})$/;
        ;

        mod.create = function (formSelector) {
            citySelector = formSelector.find("input[selector='city']");
            cityIdSelector = formSelector.find("input[name='city']");
            cityListSelector = formSelector.find("ul[selector='city-list']");
            cityBlockSelector = formSelector.find('.dropdown-city');
            districtSelector = formSelector.find("select[name='district']");
            emailSelector = formSelector.find("input[name='email']");
            cityCollection = mod.getLocalStorage('city');
            districtCollection = mod.getLocalStorage('district');
            districtFake = formSelector.find('ul.district-fake');
            districtFakeCurrent = formSelector.find('.district-fake-current');

            //function fill out city value to input
            mod.fillOutCityListItem(true, '');

            citySelector.click(function () {
                if (!cityCollection) {
                    cityCollection = mod.getLocalStorage('city');
                    districtCollection = mod.getLocalStorage('district');
                    mod.fillOutCityListItem(true, '');
                }
                mod.setTopToCityList(formSelector, cityListSelector, citySelector);
                $(window).resize(function () {
                    mod.setTopToCityList(formSelector, cityListSelector, citySelector);
                });
                formSelector.find('.create.info').scroll(function () {
                    mod.setTopToCityList(formSelector, cityListSelector, citySelector);
                });
                cityListSelector.show();
            });
            citySelector.keyup(function (event) {
                if (!cityCollection) {
                    cityCollection = mod.getLocalStorage('city');
                    districtCollection = mod.getLocalStorage('district');
                }
                //Todo Remove Comment Code
                // if (event.keyCode == 65 || event.keyCode == 67 || event.keyCode == 17) {
                //     return false;
                // }
                cityIdSelector.val('');
                cityIdSelector.trigger('change');
                var currentCityName = this.value;
                clearTimeout(triggerChangeTimeOut);
                triggerChangeTimeOut = setTimeout(function () {
                    var cityId = mod.fillOutCityListItem(false, currentCityName);
                    if (cityId != '') {
                        cityIdSelector.val(cityId).trigger('change');
                    }
                }, 700);
            });
            citySelector.change(function () {
                $.validator.validateSingleElement($(this));
            });
            cityIdSelector.change(function (event) {
                cityListSelector.hide();
                districtSelector.find('option').remove();
                mod.getDistrict();
            });
            districtSelector.mousedown(function (event) {
                if ($(this).hasClass('disable')) {
                    event.preventDefault();
                }
            });
            emailSelector.change(function () {
                var cityOld = mod.getOldCityDistrict(),
                    emailValue = $(this).val();
                cityIdSelector.attr('district', '');
                if (emailValue !== '' && emailReg.test(emailValue)) {
                    if (citySelector.val() != '') {
                        mod.saveOldCityDistrict(emailValue);
                    } else {
                        // $.each(cityOld, function (key, item) {
                        //     if (item.email == emailValue) {
                        //         if (item.cityId != '') {
                        //             citySelector.val(item.cityName).trigger('change');
                        //             cityIdSelector.attr('district', item.districtId);
                        //             cityIdSelector.val(item.cityId).trigger('change');
                        //         } else {
                        //             citySelector.val(item.cityName).trigger('change');
                        //             ;
                        //             mod.fillOutCityListItem(false, item.cityName);
                        //         }
                        //     }
                        //
                        // });
                    }
                }
            });
            cityBlockSelector.mouseleave(function () {
                cityListSelector.hide();
            });
            mod.cityListCss(formSelector, cityListSelector, citySelector);
            mod.cityListCss(formSelector, districtFake, districtFakeCurrent);
            districtFake.hide();
            districtFakeCurrent.on('click', function () {
                mod.setTopToCityList(formSelector, districtFake, districtFakeCurrent);
                $(window).resize(function () {
                    mod.setTopToCityList(formSelector, districtFake, districtFakeCurrent);
                });
                formSelector.find('.create.info').scroll(function () {
                    mod.setTopToCityList(formSelector, districtFake, districtFakeCurrent);
                });
                districtFake.show();
            });
            districtFakeCurrent.on('mouseleave', function () {
                districtFake.trigger('mouseleave');
            });
            districtFake.on('mouseover',function () {
                $(this).show();
            });
            districtFake.on('mouseleave', function () {
                districtFake.hide();
            });
            districtSelector.css('height', '0px');
            var delay = (function(){
                var timer = 0;
                return function(callback, ms){
                    clearTimeout (timer);
                    timer = setTimeout(callback, ms);
                };
            })();
            citySelector.on('keyup', function () {
                delay(function(){
                    if(typeof $(citySelector).val() !== "undefined") {
                        var keyword = $(citySelector).val().toLowerCase();
                        cityListSelector.find('li').each(function () {
                            if(typeof $(this).text() !== "undefined") {
                                if (!$(this).text().toLowerCase().includes(keyword)) {
                                    $(this).css('display', 'none');
                                } else {
                                    $(this).css('display', 'block');
                                }
                            }
                        });
                        cityListSelector.show();
                    }
                }, 1000 );
            });
        };

        mod.getLocalStorage = function (item) {
            if (typeof (Storage) !== "undefined") {
                return JSON.parse(localStorage.getItem(item)).value;
            }

            return false;
        };

        mod.fillOutCityListItem = function (firstLoad, currentCityName) {
            var cityId = '';
            cityIdSelector.attr('district', '');
            if (firstLoad) {
                districtSelector.addClass('disable');
            }
            if (!$.isEmptyObject(cityCollection)) {
                if (!firstLoad) {
                    cityListSelector.find('li').remove();
                }
                currentCityName = currentCityName.toLowerCase();
                $.each(cityCollection, function (index, value) {
                    $.each(value, function (id, city) {
                        var cityLow = city.toLowerCase();
                        if (currentCityName == cityLow) {
                            cityId = id;
                        }
                        if (cityLow.indexOf(currentCityName) != -1) {
                            cityListSelector.append(`<li city-id="${id}">${city}</li>`);
                        }
                    });
                });
                if (!firstLoad) {
                    cityListSelector.show();
                }
                cityListSelector.find('li').click(function () {
                    var cityIdSelected = $(this).attr('city-id'),
                        cityNameSelected = $(this).text();
                    citySelector.val(cityNameSelected).trigger('change');
                    cityIdSelector.val(cityIdSelected).trigger('change');
                });
                var emailValue = emailSelector.val();
                if (emailValue !== '' && emailReg.test(emailValue)) {
                    mod.saveOldCityDistrict(emailValue);
                }
                return cityId;
            }
        };

        mod.getDistrict = function () {
            var cityId = cityIdSelector.val();
            districtFake.find('li').each(function () {
                $(this).remove();
            });
            if (cityId != '' && typeof districtCollection[cityId] !== "undefined") {
                districtSelector.removeClass('disable');
                var i = 0,
                    selectedId = 0;
                $.each(districtCollection[cityId], function (index, value) {
                    $.each(value, function (id, districtName) {
                        i++;
                        if (i == 1) {
                            selectedId = id;
                        }
                        if (cityIdSelector.attr('district') == id) {
                            selectedId = id;
                        }
                        if (id) {
                            districtSelector.append(`<option value="${id}" >${districtName} </option>`);
                            districtFake.append(`<li class="checkbox-custom" value="${id}" >
                            <span class="checkbox-label">${districtName}</span></li>`);
                        }
                    });
                });
                if (selectedId !== 0) {
                    districtSelector.val(selectedId);
                    districtSelector.trigger('change');
                    districtFakeCurrent.find('span').text(districtSelector.find('option:selected').text());
                }
                districtSelector.change(function () {
                    var emailValue = emailSelector.val();
                    if (emailValue !== '' && emailReg.test(emailValue)) {
                        mod.saveOldCityDistrict(emailValue);
                    }
                })
            } else {
                if (districtSelector.find('option').length == 0) {
                    var districtName = $.mage.__('District');
                    districtSelector.addClass('disable');
                }
            }
            mod.validateDistrict(districtSelector);
            districtSelector.trigger('change');
            districtFake.find('li').on('click', function () {
                let self = $(this);
                districtSelector.val(self.val());
                districtSelector.trigger('change');
                districtFakeCurrent.find('span').text($(this).find('span').text());
                districtFake.css('display', 'none');
            });
        };

        mod.getOldCityDistrict = function () {
            if (typeof localStorage.getItem("city-old") === "undefined"
                || typeof localStorage.getItem("city-old") === "null"
                || $.isEmptyObject(localStorage.getItem("city-old"))
            ) {
                var cityOld = {};
                cityOld.count = 0;
            } else {
                var cityOld = JSON.parse(localStorage.getItem("city-old"));
            }
            return cityOld;
        };

        mod.saveOldCityDistrict = function (emailValue) {
            var data = {cityId: '', cityName: '', districtId: '', email: ''},
                cityOld = mod.getOldCityDistrict(),
                insert = true;
            data.email = emailValue;
            data.cityName = citySelector.val();
            if (cityIdSelector.val() != '') {
                data.cityId = cityIdSelector.val();
            }
            data.districtId = districtSelector.val();
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
        };

        mod.validateDistrict = function (districtSelector) {
            districtSelector.change(function () {
                $.validator.validateSingleElement(districtSelector);
            });
        };

        mod.cityListCss = function (formSelector, cityListSelector, citySelector) {
            setTimeout(function () {
                mod.setTopToCityList(formSelector, cityListSelector, citySelector);
                $(window).resize(function () {
                    mod.setTopToCityList(formSelector, cityListSelector, citySelector);
                });
                formSelector.find('.create.info').scroll(function () {
                    mod.setTopToCityList(formSelector, cityListSelector, citySelector);
                });
            }, 1000);

        };

        mod.setTopToCityList = function (formSelector, cityListSelector, citySelector) {
            var top = citySelector.offset().top - formSelector.find('.modal-content').offset().top +
                citySelector.outerHeight(true);
            var width = citySelector.width();
            cityListSelector.css({
                "top": top + "px",
                "width": (width + 10) + "px",
            });
        };

        return mod;
    }
);
