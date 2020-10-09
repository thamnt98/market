/**
 * @category SM
 * @package SM_Customer
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
        'mage/url'
    ], function ($, urlBuilder) {
        'use strict';

        let mod = {};

        mod.init = function () {
            const now = new Date();
            if (typeof localStorage.getItem("city") === "undefined"
                || localStorage.getItem("city") === "null"
                || $.isEmptyObject(localStorage.getItem("city"))
            ) {
                mod.getData();
            } else {
                const itemStr = localStorage.getItem("city");
                const item = JSON.parse(itemStr);
                if (now.getTime() > item.expiry || typeof item.expiry === "undefined") {
                    mod.getData();
                }
            }
        };

        mod.getData = function () {
            const now = new Date(),
                ttl = 12*60*60*1000;
            $.ajax(
                {
                    url : urlBuilder.build("customer/account/citydistrict"),
                    type : "post",
                    dataType:"json",
                    success : function (result) {
                        if (!$.isEmptyObject(result)) {
                            var city = {
                                    value: result.city,
                                    expiry: now.getTime() + ttl
                                },
                                district = {
                                    value: result.district,
                                    expiry: now.getTime() + ttl
                                },
                                region = {
                                    value: result.region,
                                    expiry: now.getTime() + ttl
                                },
                                cityRegion = {
                                    value: result.cityRegion,
                                    expiry: now.getTime() + ttl
                                };
                            if (typeof(Storage) !== "undefined") {
                                localStorage.setItem("city", JSON.stringify(city));
                                localStorage.setItem("district", JSON.stringify(district));
                                localStorage.setItem("region", JSON.stringify(region));
                                localStorage.setItem("city-region", JSON.stringify(cityRegion));
                            }
                        }
                    }
                }
            );
        };

        return mod.init();
    }
);
