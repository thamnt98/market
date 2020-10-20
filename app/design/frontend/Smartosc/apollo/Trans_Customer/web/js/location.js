/**
 * SMCommerce
 *
 * @category
 * @package   _${MODULE}
 *
 * Date: May, 07 2020
 * Time: 3:32 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */
define(
    [
        'jquery',
        'underscore',
        'Magento_Ui/js/modal/modal',
        'mage/translate'
    ],
    function ($, _, modal, $t) {
        'use strict';
        let locationLong    = false,
            locationLat     = false,
            locationAddress = false,
            allowLocation   = false,
            defaultLat      = -6.175392,
            defaultLng      = 106.827153,
            markers         = [],
            mainMap;

        $.widget(
            'trans.location',
            {
                _create: function () {
                    if (this.options.apiKey !== '') {
                        let self   = this,
                            mapUrl = 'https://maps.googleapis.com/maps/api/js?key='
                                + this.options.apiKey
                                + '&libraries=places';

                        if (self.options.statusLocation != 1) {
                            $(self.options.latSelector).val('');
                            $(self.options.longSelector).val('');
                        }

                        $.getScript(mapUrl, function (data, textStatus, jqxhr) {
                            self.initMap('init');
                            self.detected();
                        });
                        self.suggestTagName();
                        self.validatePhone();
                    }
                },

                suggestTagName: function () {
                    let self = this;

                    $(self.options.tagSuggestSelector).click(function () {
                        $(self.options.addressTagSelector).val($(this).text())
                    });
                },

                validatePhone: function () {
                    let self = this;

                    $(self.options.phoneSelector).keyup(function(e) {
                        if (this.value.length < 2) {
                            this.value = '08';
                        } else if (this.value.indexOf('08') !== 0) {
                            this.value = '08' + String.fromCharCode(e.which);
                        }
                    });
                },

                initMap: function (type) {
                    let self     = this,
                        geocoder = new google.maps.Geocoder,
                        latlng   = {
                            lat: Number($(self.options.latSelector).val()),
                            lng: Number($(self.options.longSelector).val())
                        };

                    if ($(self.options.latSelector).val() == '' || $(self.options.longSelector).val() == '') {
                        latlng   = {
                            lat: defaultLat,
                            lng: defaultLng
                        };
                    }

                    if (type === 'init') {
                        mainMap = new google.maps.Map(document.getElementById('map'), {
                            center          : latlng,
                            zoom            : self.options.statusLocation == 1 ? 13 : 8,
                            gestureHandling : 'none',
                            zoomControl     : false,
                            disableDefaultUI: true
                        });

                        if (self.options.statusLocation == 1) {
                            geocoder.geocode({'location': latlng}, function (results, status) {
                                if (status === 'OK') {
                                    if (results[0]) {
                                        new google.maps.Marker({
                                            map      : mainMap,
                                            position : results[0].geometry.location,
                                            draggable: false,
                                            animation: google.maps.Animation.DROP
                                        });

                                        let bounds = new google.maps.LatLngBounds();

                                        bounds.extend(results[0].geometry.location);
                                        mainMap.setOptions({maxZoom: 15});
                                        mainMap.fitBounds(bounds);
                                        mainMap.setOptions({maxZoom: null});
                                        
                                        if ($(self.options.latSelector).val() && $(self.options.longSelector).val()) {
                                            $(self.options.locationTxtSelector).text(results[0].formatted_address);
                                            $(self.options.pinpointDetectedButton).attr("data-action", "remove");
                                            $(self.options.pinpointDetectedButton).text($.mage.__("Remove pinpoint"));
                                        }
                                    }
                                }
                            });
                        }
                    } else if (type === 'location' || type === 'pinpoint' || type === 'reset') {
                        geocoder.geocode({'location': latlng}, function (results, status) {
                            if (status === 'OK') {
                                if (results[0]) {
                                    if (type === 'reset' && !allowLocation && self.options.statusLocation == 0) {
                                        self.initMap('init');
                                    } else {
                                        $(self.options.pinpointSelector).val(results[0].formatted_address);

                                        if ($(self.options.zipcode).val() === '') {
                                            let postCode = _.find(results[0].address_components, function (ac) {
                                                return ac.types[0] == 'postal_code'
                                            });

                                            if (typeof postCode !== 'undefined') {
                                                if (postCode.hasOwnProperty('long_name')) {
                                                    $(self.options.zipcode).val(postCode.long_name);
                                                } else if (postCode.hasOwnProperty('short_name')) {
                                                    $(self.options.zipcode).val(postCode.short_name);
                                                }
                                            }

                                        }

                                        if (results[0].formatted_address.length < 57) {
                                            $(self.options.locationTxtSelector).text(results[0].formatted_address);
                                        } else {
                                            $(self.options.locationTxtSelector).text(results[0].formatted_address.substr(0, 54) + ' ...');
                                        }

                                        if ($(self.options.streetSelector).val() === '') {
                                            $(self.options.streetSelector).val(results[0].formatted_address);
                                        }

                                        if (type === 'location') {
                                            locationAddress = results[0].formatted_address;
                                        }

                                        new google.maps.Marker({
                                            map      : mainMap,
                                            position : results[0].geometry.location,
                                            draggable: false,
                                            animation: google.maps.Animation.DROP
                                        });

                                        let bounds = new google.maps.LatLngBounds();

                                        bounds.extend(results[0].geometry.location);
                                        mainMap.setOptions({maxZoom: 15});
                                        mainMap.fitBounds(bounds);
                                        mainMap.setOptions({maxZoom: null});
                                    }
                                }
                            }
                        });
                    }
                },

                getLocation: function () {
                    let self = this;
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
                    let self = this;

                    $(self.options.pinpointDetectedButton).click(function () {
                        if ($(this).data('action') === 'remove') {
                            self.remove();
                            $(this).data('action', 'add');
                            $(this).text($.mage.__('Add pinpoint'));

                            return;
                        }

                        let latlng    = {
                                lat: Number($(self.options.latSelector).val()),
                                lng: Number($(self.options.longSelector).val())
                            },
                            zoom      = 13,
                            map       = '',
                            options   = {
                                type       : 'popup',
                                responsive : true,
                                innerScroll: true,
                                modalClass : 'pinpoint-detected',
                                title      : $.mage.__('Location'),
                                buttons    : [
                                    {
                                        text : $.mage.__('Set Location'),
                                        class: 'set-location',
                                        click: function () {
                                            self.markerDragEvent();
                                            this.closeModal();
                                        }
                                    }
                                ]
                            },
                            geocoder  = new google.maps.Geocoder,
                            searchBox = new google.maps.places.SearchBox(
                                document.getElementById(self.options.mapSearchId)
                            ),
                            popup     = modal(options, $(self.options.mapDetectedSelector));

                        if ($(self.options.latSelector).val() == '' || $(self.options.longSelector).val() == '') {
                            map = new google.maps.Map(document.getElementById(self.options.pinpointMapId), {
                                center            : {lat: defaultLat, lng: defaultLng},
                                zoom              : zoom,
                                panControl        : false,
                                mapTypeControl    : false,
                                scaleControl      : false,
                                streetViewControl : false,
                                overviewMapControl: false,
                                rotateControl     : false
                            })
                        } else {
                            new google.maps.Map(document.getElementById(self.options.pinpointMapId), {
                                center            : latlng,
                                zoom              : zoom,
                                panControl        : false,
                                mapTypeControl    : false,
                                scaleControl      : false,
                                streetViewControl : false,
                                overviewMapControl: false,
                                rotateControl     : false
                            })
                        }

                        $(self.options.mapDetectedSelector).modal('openModal').show();
                        $(self.options.mapDetectedSelector).modal('openModal').on('modalclosed', function () {
                            $('#' + self.options.mapSearchId).val('');
                            $(self.options.pinpointAddressSelector).text('');
                            markers = [];
                        });
                        geocoder.geocode({'location': latlng}, function (results, status) {
                            if (status === 'OK') {
                                if (results[0]) {
                                    markers = [];
                                    markers[0] = new google.maps.Marker({
                                        map      : map,
                                        address  : results[0].formatted_address,
                                        position : results[0].geometry.location,
                                        draggable: true,
                                        animation: google.maps.Animation.DROP
                                    });
                                    $('#' + self.options.mapSearchId).val(results[0].formatted_address);
                                    $(self.options.pinpointAddressSelector).text(results[0].formatted_address);
                                }
                            }
                        });

                        google.maps.event.addListener(searchBox, 'places_changed', function () {
                            let places = searchBox.getPlaces(),
                                bounds = new google.maps.LatLngBounds();

                            if (places.length === 0) {
                                return;
                            }

                            for (let i = 0, marker; marker = markers[i]; i++) {
                                marker.setMap(null);
                            }

                            markers = [];
                            for (let i = 0, place; place = places[i]; i++) {
                                let marker = new google.maps.Marker({
                                    map      : map,
                                    address  : place.formatted_address,
                                    position : place.geometry.location,
                                    draggable: true,
                                    animation: google.maps.Animation.DROP
                                });

                                $(self.options.pinpointAddressSelector).text(place.formatted_address);
                                markers.push(marker);
                                bounds.extend(place.geometry.location);
                            }

                            if (markers.length > 1) {
                                $('.set-location').addClass('disabled');
                            }

                            map.setOptions({maxZoom: 15});
                            map.fitBounds(bounds);
                            map.setOptions({maxZoom: null});
                        });

                        // Add marker when click on map
                        google.maps.event.addListener(map, 'click', function (e) {
                            $('.set-location').removeClass('disabled');
                            for (let i = 0, marker; marker = markers[i]; i++) {
                                marker.setMap(null);
                            }

                            let geocoder = new google.maps.Geocoder,
                                latlng   = {lat: e.latLng.lat(), lng: e.latLng.lng()};

                            geocoder.geocode({'location': latlng}, function (results, status) {
                                if (status === 'OK') {
                                    if (results[0]) {
                                        markers = [];
                                        markers[0] = new google.maps.Marker({
                                            map      : map,
                                            address  : results[0].formatted_address,
                                            position : results[0].geometry.location,
                                            draggable: true,
                                            animation: google.maps.Animation.DROP
                                        });
                                        $('#' + self.options.mapSearchId).val(results[0].formatted_address);
                                        $(self.options.pinpointAddressSelector).text(results[0].formatted_address);
                                    }
                                }
                            });
                        });
                    })
                },

                markerDragEvent: function () {
                    let self = this,
                        pinned = false;

                    for (let i = 0, marker; marker = markers[i]; i++) {
                        pinned = true;
                        $(self.options.latSelector).val(marker.position.lat());
                        $(self.options.longSelector).val(marker.position.lng());
                        console.log(marker);
                        $(self.options.pinpointSelector).val(marker.address);
                        self.initMap('pinpoint');
                    }

                    if (pinned === true) {
                        $(self.options.pinpointAlert).remove();
                        $(self.options.pinpointDetectedButton).text($.mage.__('Remove pinpoint'));
                        $(self.options.pinpointDetectedButton).data('action', 'remove');
                    }
                },

                remove: function () {
                    $(this.options.latSelector).val('');
                    $(this.options.longSelector).val('');
                    $(this.options.pinpointSelector).val('');
                    $('#' + this.options.mapSearchId).val('');
                    $(this.options.locationTxtSelector).text($.mage.__('Mark location in the map'));
                    $(this.options.pinpointAddressSelector).text('');
                    markers = [];
                    this.initMap('init');
                }
            }
        );

        return $.trans.location;
    }
);
