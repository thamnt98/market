define(
    [
        'jquery',
        'mage/translate'
    ],
    function ($) {
        'use strict';
        let mod = {};
        var triggerChangeTimeOut,
            firstLoad = false;

        mod.options = {
            region: "",
            city: "",
            district: "",
            cityRegion: "",
            formSelector: "#co-shipping-form",
            regionIdSelector: "#co-shipping-form [name=region_id]",
            cityBlockSelector: "#co-shipping-form .city-block",
            cityTextSelector: "#co-shipping-form [name=city_text]",
            citySelector: "#co-shipping-form input[name=city]",
            cityListSelector: "#co-shipping-form [selector=city-list]",
            districtSelector: "#co-shipping-form select[name=district]",
            currentCityValue: "",
            currentDistrictValue: ""
        };

        mod.getLocalStorage = function (item) {
            if (typeof (Storage) !== "undefined") {
                return JSON.parse(localStorage.getItem(item)).value;
            }

            return false;
        };

        mod.init = function () {
            mod.options.region = mod.getLocalStorage('region');
            mod.options.city = mod.getLocalStorage('city');
            mod.options.district = mod.getLocalStorage('district');
            mod.options.cityRegion = mod.getLocalStorage('city-region');
            setTimeout(function(){
                mod.regionChange();
                mod.cityChange();
                mod.inputChange();
                mod.fillOutCityListItem();
                $(mod.options.cityBlockSelector).mouseleave(function () {
                    $(mod.options.cityListSelector).hide(500);
                });
            }, 500);
        };

        mod.regionChange = function () {
            $(mod.options.regionIdSelector).on('change', $.proxy(function (e) {
                $(mod.options.cityTextSelector).val('');
                $(mod.options.citySelector).val('');
                $(mod.options.districtSelector).find('option').remove();
                var districtName = $.mage.__('District');
                $(mod.options.districtSelector).append(`<option value>${districtName}</option>`);
                $(mod.options.districtSelector).addClass('disable');
            }, this));
        };

        mod.fillOutCityListItem = function () {
            var self = mod;

            if (self.options.currentCityValue != '') {
                firstLoad = true;
                $(self.options.citySelector).val(self.options.currentCityValue).trigger('change');
            }
            $(self.options.cityTextSelector).focus(function(e) {
                self.fillOutCityListItemProcess($(self.options.regionIdSelector).val(), $(this).val())
            });
            $(self.options.cityTextSelector).keyup(function (event) {
                if (event.keyCode == 65 || event.keyCode == 67 || event.keyCode == 17) {
                    return false;
                }
                $(self.options.citySelector).val('').trigger('change');
                var currentCityName = this.value;
                clearTimeout(triggerChangeTimeOut);
                triggerChangeTimeOut = setTimeout(function () {
                    var cityId = self.fillOutCityListItemProcess($(self.options.regionIdSelector).val(), currentCityName);
                    $(self.options.citySelector).val(cityId).trigger('change');
                }, 700);
            });
        };

        mod.fillOutCityListItemProcess = function (regionValue, currentCityName) {
            var self = mod,
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
                var cityCollection = mod.options.city;
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
        };

        mod.cityChange = function () {
            var self = mod;
            $(self.options.citySelector).change(function () {
                var cityRegionList = self.options.cityRegion;
                if (cityRegionList[$(this).val()]) {
                    $(self.options.regionIdSelector).val(cityRegionList[$(this).val()]);
                }
                self.getDistrict();
            })
        };

        mod.getDistrict = function () {
            var self = mod,
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
        };

        mod.inputChange = function () {
            $(mod.options.districtSelector).change(function () {
                $.validator.validateSingleElement($(this));
            });
            $(mod.options.formSelector).find("input", "select", "radio", "checkbox").change(function () {
                $.validator.validateSingleElement($(this));
            });
        }

        return mod;
    }
);
