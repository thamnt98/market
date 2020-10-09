/**
 * SMCommerce
 *
 * @category    SM
 * @package     SM_GTM
 *
 * Date: March, 26 2020
 * Time: 2:17 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */
define([
    'jquery',
    'mage/url',
    'SM_GTM/js/gtm/model/device',
    'gtmSha256',
    'jquery/jquery-storageapi'
], function ($, urlBuilder, device, sha256) {
    device = device.init();
    const CUSTOMER_IDENTIFIER_STORAGE_KEY = 'customer_identifier';
    let customerRelatedEvents = ['login', 'register', 'logout', 'forgot-password', 'otp'];
    return {
        refresh: function () {
            let identifier = {identifier: $.localStorage.get(CUSTOMER_IDENTIFIER_STORAGE_KEY)};
            $.localStorage.remove(CUSTOMER_IDENTIFIER_STORAGE_KEY);
            return $.ajax({
                type: 'GET',
                url: urlBuilder.build('sm_gtm/gtm/refresh'),
                data: {isAjax: 1},
                dataType: "json",
                async: true,
                success: function (result) {
                    for (let key in result) {
                        dataLayerSourceObjects[key] = result[key];
                        let CID = "null";
                        const value = `; ${document.cookie}`;
                        const parts = value.split(`; ${'_ga'}=`);
                        if (parts.length >= 2) {
                            let partLength = parts.length;
                            for (let i = 1; i < partLength; i++) {
                                CID = parts.pop().split(';').shift();
                            }
                        }
                        dataLayerSourceObjects.customer.customerID = sha256(CID);
                        if (dataLayerSourceObjects.customer.loginType === "Phone Number")
                            dataLayerSourceObjects.customer.userID = dataLayerSourceObjects.customer.phoneNumber;
                        else
                            dataLayerSourceObjects.customer.userID = dataLayerSourceObjects.customer.email;
                        if(dataLayerSourceObjects.category.menu_category != null) {
                            if(dataLayerSourceObjects.category.menu_category == dataLayerSourceObjects.category.submenu_name)
                            {
                                dataLayerSourceObjects.category.submenu_name = "Not available";
                            }
                        }else {
                            dataLayerSourceObjects.category.menu_category = "Not available";
                            dataLayerSourceObjects.category.submenu_name = "Not available";
                        }
                        if (typeof dataLayerSourceObjects.promo == "undefined") {
                            let data = [];
                            data["articleId"] = "Not available";
                            data["articleTitle"] = "Not available";
                            data["articleCategory"] = "Not available";
                            data["articleSource"] = "Not available";
                            data["articlePresent"] = "Not available";
                            data["publishedDate"] = "Not available";
                            dataLayerSourceObjects['promo'] = data;
                        }
                        $(window).load(function () {
                            if (!customerRelatedEvents) {
                                this.customerRelatedEvents = customerRelatedEvents;
                            }
                            for (let customerEvent in customerRelatedEvents) {
                                const customerStorageFlagKey = customerRelatedEvents[customerEvent] + '-success';
                                if ($.localStorage.get(customerStorageFlagKey) === true) {
                                    $(document).trigger('customer:' + customerStorageFlagKey);
                                    $.localStorage.remove(customerStorageFlagKey);
                                }
                            }
                        });
                    }
                }
            });
        }
    };
});
