require(
    ['jquery', 'mage/url'],
    function ($, urlBuilder) {
        loadLocation();
        $('html').on('click', '[data-index="city"] select', function () {
            loadLocation();
            var cityId = $(this).val();
            var districtSelector = $('[data-index="district"] select');
            districtSelector.find('option').remove();
            var districtCollection = $.parseJSON(localStorage.getItem('district')).value;
            if (cityId && typeof districtCollection[cityId] !== "undefined") {
                var i = 0,
                    selectedId = 0;
                $.each(districtCollection[cityId], function (index, value) {
                    $.each(value, function (id, districtName) {
                        i++;
                        if (i == 1) {
                            selectedId = id;
                        }
                        if ($(this).attr('district') == id) {
                            selectedId = id;
                        }
                        districtSelector.append(`<option value="${id}">${districtName} </option>`);
                    });
                });
            }
        });

        function loadLocation() {
            if (typeof localStorage.getItem("city") === "undefined"
                || localStorage.getItem("city") === "null"
                || $.isEmptyObject(localStorage.getItem("city"))
            ) {
                $.ajax(
                    {
                        url: "/customer/account/citydistrict",
                        type: "post",
                        success: function (result) {
                            console.log(result);
                            if (!$.isEmptyObject(result)) {
                                var city = {
                                        value: result.city
                                    },
                                    district = {
                                        value: result.district
                                    },
                                    region = {
                                        value: result.region
                                    },
                                    cityRegion = {
                                        value: result.cityRegion
                                    };
                                if (typeof (Storage) !== "undefined") {
                                    localStorage.setItem("city", JSON.stringify(city));
                                    localStorage.setItem("district", JSON.stringify(district));
                                    localStorage.setItem("region", JSON.stringify(region));
                                    localStorage.setItem("city-region", JSON.stringify(cityRegion));
                                }
                            }
                        }
                    }
                );
            }
        }
    });


