define(
    [
        'ko',
        'jquery',
        'Magento_Ui/js/modal/modal',
        'mage/translate'
    ],
    function (ko, $, modal) {
        'use strict';
        let mod = {};
        var initLoad = ko.observable(true),
            locationLong = false,
            locationLat = false,
            allowLocation = false,
            markers = [],
            mainMap;

        mod.options = {
            formSelector: "#co-shipping-form",
            addressTagSelector: "#co-shipping-form input[name=address_tag]",
            tagSuggestSelector: ".tag-suggest span",
            streetSelector: "#co-shipping-form fieldset.street input",
            apiKey: window.checkoutConfig.apiKey,
            latSelector: "#co-shipping-form input[name=latitude]",
            longSelector: "#co-shipping-form input[name=longitude]",
            pinpointSelector: "#co-shipping-form input[name=pinpoint_location]",
            pinpointDetectedButton: "#co-shipping-form [selector=pinpoint-detected-button]",
            mapDetectedSelector: "#map-detected",
            mapSearchId: "pinpoint-search",
            pinpointMapId: "pinpoint-map",
            pinpointAddressSelector: "[selector=pinpoint-address]",
            markLocationDetail: "[selector=mark-location-detail]"
        };

        mod.loadAPIMap = function () {
            if (mod.options.apiKey != '') {
                var self = mod;
                var mapUrl = 'https://maps.googleapis.com/maps/api/js?key=' + mod.options.apiKey + '&libraries=places';
                $.getScript( mapUrl, function( data, textStatus, jqxhr ) {
                    self.getLocation();
                    self.detected();
                });
            }

        };

        mod.initMap = function (type) {
            var self = mod,
                latlng = {lat: Number($(self.options.latSelector).val()), lng: Number($(self.options.longSelector).val())};
            if (type == 'init' && initLoad()) {
                var zoom = 13;
                mainMap = new google.maps.Map(document.getElementById('map'), {
                    center: latlng,
                    zoom: zoom,
                    gestureHandling: 'none',
                    zoomControl: false,
                    disableDefaultUI: true
                });
            }
            if ((type == 'init' && initLoad()) || type == 'pinpoint') {
                initLoad(false);
                var geocoder = new google.maps.Geocoder,
                    latlng = {lat: Number($(self.options.latSelector).val()), lng: Number($(self.options.longSelector).val())};
                geocoder.geocode({'location': latlng}, function(results, status) {
                    if (status === 'OK') {
                        if (results[0]) {
                            $(self.options.pinpointSelector).val(results[0].formatted_address).trigger('change');
                            $(self.options.markLocationDetail).text(results[0].formatted_address);
                            if ($(self.options.streetSelector).val() == '') {
                                $(self.options.streetSelector).val(results[0].formatted_address).trigger('change');
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
                });

            }
        };

        mod.getLocation = function () {
            var self = mod;
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    if (!locationLong) {
                        locationLong = position.coords.longitude;
                        locationLat = position.coords.latitude;
                        $(self.options.latSelector).val(locationLat);
                        $(self.options.longSelector).val(locationLong);
                        allowLocation = true;
                    }
                }, function (error) {

                });
            }
        };

        mod.detected = function () {
            var self = mod;
            $(self.options.pinpointDetectedButton).click(function () {
                //$('#opc-new-shipping-address').modal('closeModal');
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

                $(self.options.mapDetectedSelector).modal('openModal').show().on('modalclosed', function() {
                    //$('#opc-new-shipping-address').modal('openModal');
                });

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
        };

        mod.markerDragEvent = function () {
            var self = mod;
            for (var i = 0, marker; marker = markers[i]; i++) {
                $(self.options.latSelector).val(marker.position.lat());
                $(self.options.longSelector).val(marker.position.lng());
                $(self.options.pinpointSelector).val(marker.address).trigger('change');
                $(self.options.markLocationDetail).text(marker.address);
                if ($(self.options.streetSelector).val() == '') {
                    $(self.options.streetSelector).val(marker.address).trigger('change');
                }
                self.initMap('pinpoint');
            }
        };

        return mod;
    }
);
