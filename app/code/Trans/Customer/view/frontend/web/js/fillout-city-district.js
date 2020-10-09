/**
 * @category Trans
 * @package Trans_Customer
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

        var triggerChangeTimeOut,
            firstLoad = false;

        $.widget(
            'trans.fill', {
                _create: function () {
                    var self = this;
                    self.options.region = self.getLocalStorage('region');
                    self.options.city = self.getLocalStorage('city');
                    self.options.district = self.getLocalStorage('district');
                    self.options.cityRegion = self.getLocalStorage('city-region');
                    self.regionChange();
                    self.cityChange();
                    self.inputChange();
                    self.fillOutCityListItem();
                    self.reset();
                    $(self.options.cityBlockSelector).mouseleave(function () {
                        $(self.options.cityListSelector).hide(500);
                    });
                },

                getLocalStorage: function (item) {
                    if (typeof (Storage) !== "undefined") {
                        return JSON.parse(localStorage.getItem(item)).value;
                    }

                    return false;
                },

                regionChange: function () {
                    this.element.on('change', $.proxy(function (e) {
                        $(this.options.cityTextSelector).val('');
                        $(this.options.citySelector).val('');
                        $(this.options.districtSelector).find('option').remove();
                        var districtName = $.mage.__('District');
                        $(this.options.districtSelector).append(`<option value>${districtName}</option>`);
                        $(this.options.districtSelector).addClass('disable');
                    }, this));
                },

                fillOutCityListItem: function () {
                    var self = this;

                    if (self.options.currentCityValue != '') {
                        firstLoad = true;
                        $(self.options.citySelector).val(self.options.currentCityValue).trigger('change');
                    }

                    $(self.options.cityTextSelector).focus(function(e) {
                        self.fillOutCityListItemProcess(self.element.val(), $(this).val())
                    });
                    $(self.options.cityTextSelector).keyup(function (event) {
                        if (event.keyCode == 65 || event.keyCode == 67 || event.keyCode == 17) {
                            return false;
                        }
                        $(self.options.citySelector).val('').trigger('change');
                        var currentCityName = this.value;
                        clearTimeout(triggerChangeTimeOut);
                        triggerChangeTimeOut = setTimeout(function () {
                            var cityId = self.fillOutCityListItemProcess(self.element.val(), currentCityName);
                            $(self.options.citySelector).val(cityId).trigger('change');
                        }, 700);
                    });
                },

                fillOutCityListItemProcess: function (regionValue, currentCityName) {
                    var self = this,
                        cityId = '',
                        regionCityList = this.options.region;
                    currentCityName = currentCityName.toLowerCase();
                    if (regionValue != '' && regionCityList[regionValue]) {
                        $(self.options.cityListSelector).find('li').remove();
                        $.each(regionCityList[regionValue], function(index, value) {
                            var cityLow = value.city.toLowerCase();
                            if (currentCityName == cityLow) {
                                cityId = value.id;
                            }
                            if (cityLow.indexOf(currentCityName) != -1) {
                                $(self.options.cityListSelector).append(`<li city-id="${value.id}">${value.city}</li>`);
                            }
                        });
                    } else {
                        var cityCollection = this.options.city;
                        $.each(cityCollection, function (index, value) {
                            $.each(value, function (id, city) {
                                var cityLow = city.toLowerCase();
                                if (currentCityName == cityLow) {
                                    cityId = id;
                                }
                                if (cityLow.indexOf(currentCityName) != -1) {
                                    $(self.options.cityListSelector).append(`<li city-id="${id}">${city}</li>`);
                                }
                            });
                        });
                    }
                    $(self.options.cityListSelector).show();
                    $(self.options.cityListSelector).find('li').click(function () {
                        var cityIdSelected = $(this).attr('city-id'),
                            cityNameSelected = $(this).text();
                        $(self.options.cityTextSelector).val(cityNameSelected).trigger('change');
                        $(self.options.citySelector).val(cityIdSelected).trigger('change');
                        $(this).parent().hide();
                    });
                    return cityId;
                },

                cityChange: function () {
                    var self = this;
                    $(self.options.citySelector).change(function () {
                        var cityRegionList = self.options.cityRegion;
                        if (cityRegionList[$(this).val()]) {
                            self.element.val(cityRegionList[$(this).val()]);
                        }
                        self.getDistrict();
                    })
                },

                getDistrict: function () {
                    var self = this,
                        cityId = $(self.options.citySelector).val(),
                        districtCollection = self.options.district;
                    $(self.options.districtSelector).find('option').remove();
                    if (cityId != '' && typeof districtCollection[cityId] !== "undefined") {
                        $(self.options.districtSelector).removeClass('disable');
                        var i = 0,
                            selectedId = 0;
                        $.each(districtCollection[cityId], function (index, value) {
                            $.each(value, function (id, districtName) {
                                i++;
                                if (i == 1) {
                                    selectedId = id;
                                }
                                if (id == self.options.currentDistrictValue && firstLoad) {
                                    firstLoad = false;
                                    selectedId = id;
                                }
                                $(self.options.districtSelector).append(`<option value="${id}">${districtName} </option>`);
                            });
                        });
                        if (selectedId !== 0) {
                            $(self.options.districtSelector).val(selectedId);
                        }
                        $(self.options.districtSelector).trigger('change');
                    } else {
                        var districtName = $.mage.__('District');
                        $(self.options.districtSelector).append(`<option value>${districtName}</option>`);
                        if (!$(self.options.districtSelector).hasClass('disable')) {
                            $(self.options.districtSelector).addClass('disable');
                        }
                    }
                },

                inputChange: function () {
                    $(this.options.districtSelector).change(function () {
                        $.validator.validateSingleElement($(this));
                    });
                    $(this.options.formSelector).find("input", "select", "radio", "checkbox").change(function () {
                        $.validator.validateSingleElement($(this));
                    });
                },

                reset: function () {
                    var self = this;
                    $(self.options.resetButton).click(function () {
                        $.each(self.options.data, function (key, value) {
                            if (key == 'city_name') {
                                $(self.options.cityTextSelector).val(value);
                            } else if (key == 'address') {
                                $(self.options.streetSelector).val(value);
                            } else {
                                $(self.options.formSelector).find("[name=" + key + "]").val(value).trigger('change');
                            }
                        });
                    })
                }
            }
        );
        return $.trans.fill;
    }
);
