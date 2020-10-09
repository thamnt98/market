define(
    [
        'jquery',
        'Magento_Ui/js/modal/modal'
    ],
    function ($, modal) {
        var currentLongitude = $('#store-location-longitude');
        var currentLatitude = $('#store-location-latitude');
        return function (config) {
            let mapUrl = config.apiKey;

            let findAStore = $('#store-locator'),
                options = {
                    type: 'popup',
                    responsive: true,
                    innerScroll: false,
                    title: '',
                    buttons: [],
                    clickableOverlay: false,
                    modalClass: 'find-a-store'
            },
            findAStoreModal = modal(options, findAStore);

            findAStore.modal({
                opened: function (e) {
                    $.getScript(mapUrl, function (data, textStatus, jqxhr) {
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(function (position) {
                                let coords = {
                                    lat: position.coords.latitude,
                                    long: position.coords.longitude
                                };
                                console.log(coords);
                                currentLongitude.val(coords.long);
                                currentLatitude.val(coords.lat);
                            })
                            let input = $('.input-location-stores')[0];
                            let autocomplete = new google.maps.places.Autocomplete(input);

                            autocomplete.setComponentRestrictions({'country': ['id']});
                            google.maps.event.addListener(autocomplete, 'place_changed', function () {
                                let place = autocomplete.getPlace();

                                $('#autocomplete-longitude').val(place.geometry.location.lng());
                                $('#autocomplete-latitude').val(place.geometry.location.lat());
                                $("#btn-search-store").click();
                            });

                            //on keyup, start the countdown
                            $(input).on("keyup", function () {
                                if ($(input).val().length == 0) {
                                    $('#autocomplete-longitude').val($('#store-location-longitude').val());
                                    $('#autocomplete-latitude').val($('#store-location-latitude').val());
                                    $("#btn-search-store").click();
                                }
                            });
                        } else {
                            alert("Geolocation is not supported by this browser.");
                        }
                    });
                }
            });

            $('#navbar-findStore').on('click', function () {
                findAStore.modal('openModal');
            });

            // Link Footer findStore
            setTimeout(function () {
                $('.navbar-link-findStore').on('click', function (e) {
                    $('#navbar-findStore').trigger('click');
                    e.preventDefault();
                });
            },3000);
        }
    }
);
