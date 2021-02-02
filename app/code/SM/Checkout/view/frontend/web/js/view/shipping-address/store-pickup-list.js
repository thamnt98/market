/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'ko',
    'jquery',
    'uiComponent',
    'mage/translate',
    'Magento_Ui/js/modal/modal',
    'mage/storage',
    'SM_Checkout/js/view/shipping-address/current-pickup',
    'SM_Checkout/js/action/find-store',
    'SM_Checkout/js/view/cart-items/init-shipping-type'
], function (ko, $, Component, $t, modal, storage, pickup, findStoreAction, initShippingType) {
    'use strict';

    var sortSourceDefault = window.checkoutConfig.sortSource,
        sourceList = window.checkoutConfig.msi;

    return Component.extend({
        storeFullFill: findStoreAction.storeFullFill,
        sourceDistanceList: findStoreAction.sourceDistanceList,
        sourceShortestDistanceList: findStoreAction.sourceShortestDistanceList,
        hasCurrentStore: pickup.hasCurrentStore,
        currentStoreName: pickup.currentStoreName,
        currentStoreAddress: pickup.currentStoreAddress,
        currentPickupId: pickup.currentPickupId,
        searchStoreAddress: findStoreAction.searchStoreAddress,
        loadGoogleAPI: ko.observable(false),
        loadHtmlComplete: ko.observable(false),
        loadSearchBoxComplete: ko.observable(false),
        /** @inheritdoc */
        initialize: function () {
            this._super();
            this.initMap();
            this.preSelectStore();
            return this;
        },

        initMap: function () {
            var self = this,
                mapUrl = 'https://maps.googleapis.com/maps/api/js?key=' + window.checkoutConfig.apiKey + '&libraries=places';
            $.getScript( mapUrl, function( data, textStatus, jqxhr ) {
                self.loadGoogleAPI(true);
            });
            self.loadGoogleAPI.subscribe(function (value) {
                if (value) {
                    self.renderSearchBox('store-search', '0');
                }
            });
        },

        preSelectStore: function () {
            var self = this;
            if (sortSourceDefault.length > 0) {
                var distanceData = {};
                $.each(sortSourceDefault, function (index, source) {
                    self.sourceShortestDistanceList.push(sourceList[source.source_code]);
                    distanceData[source.source_code] = source.distance;
                });
                self.sourceDistanceList(distanceData);
                self.selectedStore(sourceList[sortSourceDefault[0].source_code].source_code);
            } else {
                $.each(sourceList, function (index, item) {
                    self.selectedStore(item.source_code);
                    return false;
                });
            }
        },

        onRenderComplete: function () {
            var self = this,
                selector = $('#store-pickup-list-popup'),
                options = {
                    type: 'popup',
                    responsive: true,
                    innerScroll: false,
                    title: '',
                    buttons: [],
                    modalClass: 'modal-popup-store-pickup-list-popup',
                    clickableOverlay: false,
                    keyEventHandlers: {
                        escapeKey: function () {
                            return;
                        }
                    }
                };
            modal(options, selector);
        },

        renderSearchBox: function (id, afterLoadHtml) {
            if (afterLoadHtml == '1') {
                this.loadHtmlComplete(true);
            }
            if (this.loadGoogleAPI() && this.loadHtmlComplete() && !this.loadSearchBoxComplete()) {
                const input = document.getElementById(id);
                var searchBox = new google.maps.places.SearchBox(input);
                searchBox.addListener('places_changed', function() {
                    var address = searchBox.getPlaces()[0].formatted_address;
                    $('#' + id).focus().val('').val(address);
                });
                this.loadSearchBoxComplete(true);
            }
        },

        onEnter:function (d,e) {
            if (e.keyCode === 13) {
                this.findStore();
            }
            return true;
        },

        getCurrentLocation: function() {
            var self = this;
            if (self.loadGoogleAPI()) {
                navigator.permissions.query({name:'geolocation'}).then(function(result) {
                    if (result.state === 'denied') {
                        alert($t("Geocode was not successful for the following reason."));
                    } else {
                        navigator.geolocation.getCurrentPosition (function (position){
                            var geocoder = new google.maps.Geocoder,
                                latlng = {lat: position.coords.latitude, lng: position.coords.longitude};
                            geocoder.geocode({'location': latlng}, function(results, status) {
                                if (status === 'OK') {
                                    self.searchStoreAddress(results[0].formatted_address);
                                    var storePickupItems = initShippingType.getRatesData().storePickupItems;
                                    if (Object.keys(storePickupItems).length !== 0) {
                                        findStoreAction.searchShortestStoreAction(latlng, storePickupItems);
                                    }
                                } else {
                                    alert($t("Geocode was not successful for the following reason: %1").replace('%1', status));
                                }
                            });
                        })
                    }
                });
            } else {
                alert($t("Geolocation is not supported by this browser."));
            }
        },

        /**
         * set current data selected
         * @param source_code
         */
        selectedStore: function (source_code){
            var source = sourceList[source_code],
                storeAddress = source.street;
            if (source.city && source.city != '') {
                storeAddress += ', ' + source.city;
            }
            if (source.region && source.region != '') {
                storeAddress += ', ' + source.region;
                if (source.postcode && source.postcode != '') {
                    storeAddress += ' ' + source.postcode;
                }
            }
            pickup.currentPickupId(source_code);
            pickup.currentStoreName(source.name);
            pickup.currentStoreAddress(storeAddress);
            pickup.hasCurrentStore(true);
            $('#store-pickup-list-popup').modal("closeModal");
        },

        getDistance: function (source_code) {
            if (typeof this.sourceDistanceList()[source_code] !== 'undefined' && this.sourceDistanceList()[source_code] !== 0) {
                return this.sourceDistanceList()[source_code]+ ' Km';
            }
            return '';
        },

        findStore: function (){
            var storePickupItems = initShippingType.getRatesData().storePickupItems;
            if (Object.keys(storePickupItems).length !== 0) {
                findStoreAction.findStore(storePickupItems);
            }
        },

        notFull: function () {
            return true;
        }
    });
});
