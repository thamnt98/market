define([
    'ko',
    'uiComponent',
    'mage/url',
    'mage/storage',
    'jquery',
    'moment'
], function (ko, Component, urlBuilder,storage, $, moment) {
    'use strict';

    var currentLongitude = $('#store-location-longitude').val()||0;
    var currentLatitude = $('#store-location-latitude').val()||0;


    var autocompleteLongitude = $('#autocomplete-longitude').val()||0;
    var autocompleteLatitude = $('#autocomplete-latitude').val()||0;

    return Component.extend({
        defaults: {
            template: 'SM_StoreLocator/store-locator-listing',
        },

        storeList: ko.observableArray([]),
        location: {},

        /** @inheritdoc */
        initialize: function () {
            $('.store-list-group').removeClass('no-store-locator');
            this._super();
            this.getLocation();

            var self = this;
            $(window).load(function () {
                let serviceUrl = urlBuilder.build('rest/V1/store-locator?searchCriteria[sortOrders][0][field]=distance&searchCriteria[sortOrders][0][direction]=ASC&searchCriteria[sortOrders][0][lat]='+ currentLatitude +'&searchCriteria[sortOrders][0][long]='+ currentLongitude);
                self.searchStore(serviceUrl);
            });
        },

        getLocation: function () {
            var self = this;
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    currentLongitude = position.coords.longitude;
                    currentLatitude = position.coords.latitude;
                })
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        },

        getStoreList: function () {
            let keyWord = $('#input-search-location').val();
            let serviceUrl;
            //GTM Event
            this.__actionPushGTM("search_query_store", keyWord);

            if (keyWord.length > 0) {
                serviceUrl = urlBuilder.build('rest/V1/store-locator?searchCriteria[sortOrders][0][field]=distance&searchCriteria[sortOrders][0][direction]=ASC&searchCriteria[sortOrders][0][lat]='+ $('#autocomplete-latitude').val() +'&searchCriteria[sortOrders][0][long]='+ $('#autocomplete-longitude').val());
            } else {
                serviceUrl = urlBuilder.build('rest/V1/store-locator?searchCriteria[sortOrders][0][field]=distance&searchCriteria[sortOrders][0][direction]=ASC&searchCriteria[sortOrders][0][lat]='+ currentLatitude +'&searchCriteria[sortOrders][0][long]='+ currentLongitude);
            }
            this.searchStore(serviceUrl);
        },

        getStoreListByLocation: function () {
            let inputSearch = $('#input-search-location');
            inputSearch.val("");
            let keyWord = inputSearch.val();
            this.__actionPushGTM("use_current_location", keyWord);
            let serviceUrl = urlBuilder.build('rest/V1/store-locator?searchCriteria[sortOrders][0][field]=distance&searchCriteria[sortOrders][0][direction]=ASC&searchCriteria[sortOrders][0][lat]='+ currentLatitude +'&searchCriteria[sortOrders][0][long]='+ currentLongitude);
            this.searchStore(serviceUrl);
        },

        searchStoreByAutocomplete : function () {
            let serviceUrl = urlBuilder.build('rest/V1/store-locator?searchCriteria[sortOrders][0][field]=distance&searchCriteria[sortOrders][0][direction]=ASC&searchCriteria[sortOrders][0][lat]='+ $('#autocomplete-latitude').val() +'&searchCriteria[sortOrders][0][long]='+ $('#autocomplete-longitude').val());
            this.searchStore(serviceUrl, true);
        },

        searchStore: function (serviceUrl, isAutocomplete = false) {
            let self = this;
            return storage.get(
                serviceUrl,
                ''
            ).done(
                function (response) {
                    $('.find-store').find('.store-listing').html('');
                    let now = new Date();
                    let weekday = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
                    $.each(response.items, function () {
                        let openingHours = this.extension_attributes.opening_hours;
                        let closeTime = '';
                        $.each(openingHours, function () {
                            if (weekday[now.getDay()] === this.day) {
                                let hourEnd = this.close.indexOf(":");
                                let H = +this.close.substr(0, hourEnd);
                                let h = H % 12 || 12;
                                let ampm = (H < 12 || H === 24) ? "AM" : "PM";
                                closeTime = h + this.close.substr(hourEnd, 3) + ' ' + ampm;
                            }
                        });

                        let startLat, startLon;
                        if (isAutocomplete) {
                            startLat = autocompleteLatitude;
                            startLon = autocompleteLongitude;
                        } else {
                            startLat = currentLatitude;
                            startLon = currentLongitude;
                        }

                        self.storeList.push({
                            name: this.name,
                            store_code : this.store_code,
                            address_line_1: this.address.address_line1,
                            address_line_2: this.address.address_line2,
                            close: closeTime,
                            distance: this.extension_attributes.distance,
                            stopLat: this.address.latitude,
                            stopLon: this.address.longitude,
                            startLat: startLat,
                            startLon: startLon
                        });
                    });
                    if (response.items.length === 0) {
                        $('.store-list-title').css("display", "none");
                        $(".store-list-group").css("display", "none");
                        $(".no-store-locator").css("display", "block");
                        let keyWord = $('#input-search-location').val();
                        self.__actionPushGTM("store_not_found_page", keyWord);
                        return;
                    }
                    $(".store-list-group").css("display", "block");
                    $(".no-store-locator").css("display", "none");
                    $('.store-list-title').show();
                }
            ).fail(
                function (response) {
                    $('.store-list-title').css("display", "none");
                    $(".store-list-group").css("display", "none");
                    $(".no-store-locator").css("display", "block");
                    let keyWord = $('#input-search-location').val();
                    self.__actionPushGTM("store_not_found_page", keyWord);
                }
            );
        },

        getDirection : function ($data) {
            function averageGeolocation(coords)
            {
                if (coords.length === 1) {
                    return coords[0];
                }

                let x = 0.0;
                let y = 0.0;
                let z = 0.0;

                for (let coord of coords) {
                    let latitude = coord.latitude * Math.PI / 180;
                    let longitude = coord.longitude * Math.PI / 180;

                    x += Math.cos(latitude) * Math.cos(longitude);
                    y += Math.cos(latitude) * Math.sin(longitude);
                    z += Math.sin(latitude);
                }

                let total = coords.length;

                x = x / total;
                y = y / total;
                z = z / total;

                let centralLongitude = Math.atan2(y, x);
                let centralSquareRoot = Math.sqrt(x * x + y * y);
                let centralLatitude = Math.atan2(z, centralSquareRoot);

                return {
                    latitude: centralLatitude * 180 / Math.PI,
                    longitude: centralLongitude * 180 / Math.PI
                };
            }

            //This function takes in latitude and longitude of two location and returns the distance between them as the crow flies (in km)
            function calcDistance(lat1, lon1, lat2, lon2)
            {
                let R = 6371; // km
                let dLat = (lat2-lat1) * Math.PI / 180;
                let dLon = (lon2-lon1) * Math.PI / 180;
                lat1 = lat1 * Math.PI / 180;
                lat2 = lat2 * Math.PI / 180;

                let a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                    Math.sin(dLon/2) * Math.sin(dLon/2) * Math.cos(lat1) * Math.cos(lat2);
                let c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                return R * c;
            }

            function getBaseLog(x, y)
            {
                return Math.log(y) / Math.log(x);
            }


            let startLat = parseFloat($data.startLat);
            let startLon = parseFloat($data.startLon);

            let stopLat = parseFloat($data.stopLat);
            let stopLon = parseFloat($data.stopLon);

            let centerPoint = averageGeolocation([{
                latitude: startLat,
                longitude: startLon
            }, {
                latitude: stopLat,
                longitude: stopLon
            }]);
            let zoom = getBaseLog(2, 37316 / calcDistance(startLat, startLon, stopLat, stopLon)) + 1;

            let url = "https://maps.google.com/";
            let origin = "?saddr=" + startLat + "," + startLon;
            let destination = "&daddr=" + stopLat + "," + stopLon;
            let center = "&ll=" + centerPoint.latitude + "," + centerPoint.longitude;
            zoom = "&z=" + zoom;
            let newUrl = new URL(url + origin + destination + center + zoom);

            let win = window.open(newUrl, '_blank');
            win.focus();
        },

        selectStore : function ($data) {
            if (typeof dataLayerSourceObjects !== 'undefined' && dataLayerSourceObjects.customer.loginType !== "null") {
                let data = {
                    store_name: $data.name,
                    store_ID: $data.store_code
                };
                $.ajax({
                    type: 'POST',
                    url: urlBuilder.build('sm_gtm/gtm/changestore'),
                    data: {isAjax: 1, storeInfo: data},
                    dataType: "json",
                    async: true,
                    success: function () {
                        dataLayerSourceObjects.customer.storeName = data['store_name'];
                        dataLayerSourceObjects.customer.storeID = data['store_ID'];
                    }
                });
                this.__actionPushGTM("select_store", data);
            }
        },

        //GTM Event
        __actionPushGTM: function (eventName, data) {
            if (typeof dataLayerSourceObjects !== 'undefined' && dataLayerSourceObjects.customer.loginType !== "null") {
                window.dataLayer = window.dataLayer || [];
                if (eventName === "search_query_store" || eventName === "use_current_location") {
                    if (data === '') {
                        data = "Not available";
                    }
                    window.dataLayer.push({
                        'event': eventName,
                        'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                        'userID': dataLayerSourceObjects.customer.userID,
                        'customerID': dataLayerSourceObjects.customer.customerID,
                        'customerType': dataLayerSourceObjects.customer.customerType,
                        'loyalty': dataLayerSourceObjects.customer.loyalty,
                        'customerStatus': dataLayerSourceObjects.customer.customerStatus,
                        'loginType': dataLayerSourceObjects.customer.loginType,
                        'store_name': dataLayerSourceObjects.customer.storeName,
                        'store_ID': dataLayerSourceObjects.customer.storeID,
                        'query': data
                    });
                } else if (eventName === "store_not_found_page") {
                    window.dataLayer.push({
                        'event': eventName,
                        'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                        'userID': dataLayerSourceObjects.customer.userID,
                        'customerID': dataLayerSourceObjects.customer.customerID,
                        'customerType': dataLayerSourceObjects.customer.customerType,
                        'loyalty': dataLayerSourceObjects.customer.loyalty,
                        'customerStatus': dataLayerSourceObjects.customer.customerStatus,
                        'loginType': dataLayerSourceObjects.customer.loginType,
                        'store_name': dataLayerSourceObjects.customer.storeName,
                        'store_ID': dataLayerSourceObjects.customer.storeID,
                        'timestamp': moment().format('DD\/MM\/YYYY HH:mm:ss'),
                        'query': data
                    });
                } else {
                    window.dataLayer.push({
                        'event': eventName,
                        'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                        'userID': dataLayerSourceObjects.customer.userID,
                        'customerID': dataLayerSourceObjects.customer.customerID,
                        'customerType': dataLayerSourceObjects.customer.customerType,
                        'loyalty': dataLayerSourceObjects.customer.loyalty,
                        'customerStatus': dataLayerSourceObjects.customer.customerStatus,
                        'loginType': dataLayerSourceObjects.customer.loginType,
                        'store_name': data['store_name'],
                        'store_ID': data['store_ID']
                    });
                }
            }
        }
    });
});
