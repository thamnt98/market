define(
    [
        'ko',
        'jquery',
        'mage/url',
        'mage/storage',
        'mage/translate',
        'Magento_Checkout/js/model/full-screen-loader',
        'SM_Checkout/js/view/shipping-address/current-pickup'
    ],
    function (ko, $, urlManager, storage, $t, fullScreenLoader, pickup) {
        'use strict';
        var sourceList = window.checkoutConfig.msi,
            defaultLatlng = window.checkoutConfig.latlng;
        let mod = {};

        mod.defaultLat = ko.observable();
        mod.defaultLng = ko.observable();
        mod.storeFullFill = ko.observable(false);
        mod.sourceDistanceList = ko.observable({});
        mod.sourceShortestDistanceList = ko.observableArray();
        mod.searchStoreAddress = ko.observable();

        mod.findStore = function (storePickupItems, updateCurrentStore = false, update = false) {
            if (typeof mod.searchStoreAddress() !== 'undefined' && mod.searchStoreAddress() != '') {
                var geocoder = new google.maps.Geocoder;
                geocoder.geocode( { 'address' : mod.searchStoreAddress() }, function( results, status ) {
                    if (status === 'OK') {
                        var latlng = {lat: results[0].geometry.location.lat(), lng: results[0].geometry.location.lng()};
                        mod.searchShortestStoreAction(latlng, storePickupItems, updateCurrentStore, update);
                    } else {
                        alert($t("Geocode was not successful for the following reason: %1").replace('%1', status));
                    }
                });
            } else if (updateCurrentStore) {
                navigator.permissions.query({name:'geolocation'}).then(function(result) {
                    if (result.state === 'denied') {
                        var latlng = {lat: Number(defaultLatlng.lat), lng: Number(defaultLatlng.lng)};
                        mod.searchShortestStoreAction(latlng, storePickupItems, updateCurrentStore, update);
                    } else {
                        navigator.geolocation.getCurrentPosition(function (position) {
                            var latlng = {lat: Number(position.coords.latitude), lng: Number(position.coords.longitude)};
                            mod.searchShortestStoreAction(latlng, storePickupItems, updateCurrentStore, update);
                        }, function (error) {
                            var latlng = {lat: Number(defaultLatlng.lat), lng: Number(defaultLatlng.lng)};
                            mod.searchShortestStoreAction(latlng, storePickupItems, updateCurrentStore, update);
                        });
                    }
                });
            } else {
                var latlng = {lat: Number(defaultLatlng.lat), lng: Number(defaultLatlng.lng)};
                mod.searchShortestStoreAction(latlng, storePickupItems, updateCurrentStore, update);
            }
        };

        mod.searchShortestStoreAction = function (latlng, storePickupItems, updateCurrentStore, update = false) {
            var url = urlManager.build('rest/V1/trans-checkout/searchStore'),
                currentStoreCode = pickup.currentPickupId(),
                updateTrigger = true,
                updateStoreCode = '';
            latlng.storePickupItems = storePickupItems;
            latlng.currentStoreCode = currentStoreCode;
            fullScreenLoader.startLoader();
            return storage.post(
                url,
                JSON.stringify(latlng),
                false
            ).done(
                function (response) {
                    fullScreenLoader.stopLoader();
                    var distanceData = {},
                        i = 0;
                    mod.sourceShortestDistanceList.removeAll();
                    $.each(response.shortest_store_list, function (index, source) {
                        i++;
                        var sourceStore = sourceList[source.source_code];
                        if (i == 1) {
                            updateStoreCode = sourceStore;
                        }
                        if (update && source.source_code == pickup.currentPickupId()) {
                            updateTrigger = false;
                        }
                        mod.sourceShortestDistanceList.push(sourceStore);
                        distanceData[source.source_code] = source.distance;
                    });
                    if (response.current_store) {
                        distanceData[response.current_store.source_code] = response.current_store.distance;
                    }
                    if (updateCurrentStore || updateTrigger) {
                        mod.updateCurrentStore(updateStoreCode);
                    }
                    mod.sourceDistanceList(distanceData);
                }
            ).fail(
                function (response) {
                    fullScreenLoader.stopLoader();
                }
            );
        };

        mod.updateCurrentStore = function (sourceStore) {
            var storeAddress = sourceStore.street;
            if (sourceStore.city && sourceStore.city != '') {
                storeAddress += ', ' + sourceStore.city;
            }
            if (sourceStore.region && sourceStore.region != '') {
                storeAddress += ', ' + sourceStore.region;
                if (sourceStore.postcode && sourceStore.postcode != '') {
                    storeAddress += ' ' + sourceStore.postcode;
                }
            }
            pickup.currentPickupId(sourceStore.source_code);
            pickup.currentStoreName(sourceStore.name);
            pickup.currentStoreAddress(storeAddress);
            pickup.hasCurrentStore(true);
        }
        return mod;
    }
);
