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
        'Magento_Ui/js/modal/modal',
        'mage/translate'
    ], function ($, modal) {
        'use strict';
        var locationLong = false,
            locationLat = false,
            locationAddress = false,
            allowLocation = false,
            markers = [],
            mainMap;

        $.widget(
            'trans.location', {
                _create: function () {
                    if (this.options.apiKey != '') {
                        var self = this;
                        var mapUrl = 'https://maps.googleapis.com/maps/api/js?key=' + this.options.apiKey + '&libraries=places';
                        $.getScript( mapUrl, function( data, textStatus, jqxhr ) {
                            self.initMap('init');
                            self.getLocation();
                            self.detected();
                        });
                        self.suggestTagName();
                        self.reset();
                    }

                },

                suggestTagName: function () {
                    var self = this;
                    $(self.options.tagSuggestSelector).click(function () {
                        $(self.options.addressTagSelector).val($(this).text())
                    });
                },

                initMap: function (type) {
                    var self = this,
                        latlng = {lat: Number($(self.options.latSelector).val()), lng: Number($(self.options.longSelector).val())};
                    if (type == 'init') {
                        if (self.options.statusLocation == 1) {
                            var zoom = 13;
                        } else {
                            var zoom = 8;
                        }
                        mainMap = new google.maps.Map(document.getElementById('map'), {
                            center: latlng,
                            zoom: zoom,
                            gestureHandling: 'none',
                            zoomControl: false,
                            disableDefaultUI: true
                        });
                        if (self.options.statusLocation == 1) {
                            var geocoder = new google.maps.Geocoder;
                            geocoder.geocode({'location': latlng}, function(results, status) {
                                if (status === 'OK') {
                                    if (results[0]) {
                                        new google.maps.Marker({
                                            map: mainMap,
                                            position: results[0].geometry.location,
                                            draggable: false,
                                            animation: google.maps.Animation.DROP
                                        });
                                        var bounds = new google.maps.LatLngBounds();
                                        bounds.extend(results[0].geometry.location);
                                        mainMap.setOptions({ maxZoom: 15 });
                                        mainMap.fitBounds(bounds);
                                        mainMap.setOptions({ maxZoom: null });
                                    }
                                }
                            });
                        }
                    } else if (type == 'location' || type == 'pinpoint' || type == 'reset') {
                        var geocoder = new google.maps.Geocoder,
                            latlng = {lat: Number($(self.options.latSelector).val()), lng: Number($(self.options.longSelector).val())};
                        geocoder.geocode({'location': latlng}, function(results, status) {
                            if (status === 'OK') {
                                if (results[0]) {
                                    if (type == 'reset' && !allowLocation && self.options.statusLocation == 0) {
                                        self.initMap('init');
                                    } else {
                                        $(self.options.pinpointSelector).val(results[0].formatted_address);
                                        if ($(self.options.streetSelector).val() == '') {
                                            $(self.options.streetSelector).val(results[0].formatted_address);
                                        }
                                        if (type == 'location') {
                                            locationAddress = results[0].formatted_address;
                                        }
                                        new google.maps.Marker({
                                            map: mainMap,
                                            position: results[0].geometry.location,
                                            draggable: false,
                                            animation: google.maps.Animation.DROP
                                        });
                                        var bounds = new google.maps.LatLngBounds();
                                        bounds.extend(results[0].geometry.location);
                                        mainMap.setOptions({ maxZoom: 15 });
                                        mainMap.fitBounds(bounds);
                                        mainMap.setOptions({ maxZoom: null });
                                    }
                                }
                            }
                        });

                    }
                },

                getLocation: function () {
                    var self = this;
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function (position) {
                            if (!locationLong && self.options.statusLocation == 0) {
                                locationLong = position.coords.longitude;
                                locationLat = position.coords.latitude;
                                $(self.options.latSelector).val(locationLat);
                                $(self.options.longSelector).val(locationLong);
                                allowLocation = true;
                                self.initMap('location');
                            }
                        }, function (error) {

                        });
                    }
                },

                detected: function () {
                    var self = this;
                    $(self.options.pinpointDetectedButton).click(function () {
                        var latlng = {lat: Number($(self.options.latSelector).val()), lng: Number($(self.options.longSelector).val())},
                            zoom = 13;

                        var map = new google.maps.Map(document.getElementById(self.options.pinpointMapId), {
                            center: latlng,
                            zoom: zoom,
                            panControl: false,
                            mapTypeControl: false,
                            scaleControl: false,
                            streetViewControl: false,
                            overviewMapControl: false,
                            rotateControl: false
                        });
                        var options = {
                            type: 'popup',
                            responsive: true,
                            innerScroll: true,
                            modalClass: 'pinpoint-detected',
                            title: $.mage.__('Location'),
                            buttons: [
                                {
                                    text: $.mage.__('Exit'),
                                    class: 'exit',
                                    click: function () {
                                        this.closeModal();
                                    }
                                },
                                {
                                    text: $.mage.__('Set Location'),
                                    class: 'set-location',
                                    click: function () {
                                        self.markerDragEvent();
                                        this.closeModal();
                                    }
                                }
                            ]
                        };
                        var popup = modal(options, $(self.options.mapDetectedSelector));

                        $(self.options.mapDetectedSelector).modal('openModal').show();

                        // Init default marker
                        var geocoder = new google.maps.Geocoder;

                        geocoder.geocode({'location': latlng}, function(results, status) {
                            if (status === 'OK') {
                                if (results[0]) {
                                    markers = [];
                                    markers[0] = new google.maps.Marker({
                                        map: map,
                                        address: results[0].formatted_address,
                                        position: results[0].geometry.location,
                                        draggable: true,
                                        animation: google.maps.Animation.DROP
                                    });
                                    $('#'+self.options.mapSearchId).val(results[0].formatted_address);
                                    $(self.options.pinpointAddressSelector).text(results[0].formatted_address);
                                } else {
                                    //window.alert('No results found');
                                }
                            } else {
                                //window.alert('Geocoder failed due to: ' + status);
                            }
                        });


                        // Init search box
                        var searchBox = new google.maps.places.SearchBox(document.getElementById(self.options.mapSearchId));

                        google.maps.event.addListener(searchBox, 'places_changed', function(){
                            var places = searchBox.getPlaces();

                            if (places.length == 0) {
                                return;
                            }

                            for (var i = 0, marker; marker = markers[i]; i++) {
                                marker.setMap(null);
                            }
                            //map.zoom = 8;
                            markers = [];
                            var bounds = new google.maps.LatLngBounds();
                            for (var i = 0, place; place = places[i]; i++) {
                                var marker = new google.maps.Marker({
                                    map: map,
                                    address: place.formatted_address,
                                    position: place.geometry.location,
                                    draggable: true,
                                    animation: google.maps.Animation.DROP
                                });
                                $(self.options.pinpointAddressSelector).text(place.formatted_address);
                                markers.push(marker);
                                bounds.extend(place.geometry.location);
                            }
                            map.setOptions({ maxZoom: 15 });
                            map.fitBounds(bounds);
                            map.setOptions({ maxZoom: null });
                        });

                        // Add marker when click on map
                        google.maps.event.addListener(map, 'click', function(e) {
                            for (var i = 0, marker; marker = markers[i]; i++) {
                                marker.setMap(null);
                            }
                            //map.zoom = 13;
                            var geocoder = new google.maps.Geocoder,
                                latlng = {lat: e.latLng.lat(), lng: e.latLng.lng()};

                            geocoder.geocode({'location': latlng}, function(results, status) {
                                if (status === 'OK') {
                                    if (results[0]) {
                                        markers = [];
                                        markers[0] = new google.maps.Marker({
                                            map: map,
                                            address: results[0].formatted_address,
                                            position: results[0].geometry.location,
                                            draggable: true,
                                            animation: google.maps.Animation.DROP
                                        });
                                        $('#'+self.options.mapSearchId).val(results[0].formatted_address);
                                        $(self.options.pinpointAddressSelector).text(results[0].formatted_address);
                                    } else {
                                        //window.alert('No results found');
                                    }
                                } else {
                                    //window.alert('Geocoder failed due to: ' + status);
                                }
                            });
                        });
                    })
                },

                markerDragEvent: function () {
                    var self = this;
                    for (var i = 0, marker; marker = markers[i]; i++) {
                        $(self.options.latSelector).val(marker.position.lat());
                        $(self.options.longSelector).val(marker.position.lng());
                        $(self.options.pinpointSelector).val(marker.address);
                        if ($(self.options.streetSelector).val() == '') {
                            $(self.options.streetSelector).val(marker.address);
                        }
                        self.initMap('pinpoint');
                    }
                },

                reset: function () {
                    var self = this;
                    $(self.options.resetButton).click(function () {
                        if (self.options.statusLocation == 1) {
                            $.each(self.options.data, function (key, value) {
                                $(self.options.formSelector).find("input[name=" + key + "]").val(value);
                            });
                        } else {
                            if (!allowLocation) {
                                $.each(self.options.data, function (key, value) {
                                    if (key == 'latitude') {
                                        $(self.options.latSelector).val(value);
                                    }
                                    if (key == 'longitude') {
                                        $(self.options.longSelector).val(value);
                                    }
                                });
                            } else {
                                $(self.options.latSelector).val(locationLat);
                                $(self.options.longSelector).val(locationLong);
                                $(self.options.pinpointSelector).val(locationAddress);
                                if ($(self.options.streetSelector).val() == '') {
                                    $(self.options.streetSelector).val(locationAddress);
                                }
                            }
                        }

                        self.initMap('reset');
                    });
                }
            }
        );
        return $.trans.location;
    }
);
