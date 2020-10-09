define([
    'jquery',
    'moment',
    'gtmSha256'
], function ($, moment, sha256) {
    'use strict';
    return {
        push: function (event, data) {
            if (typeof dataLayerSourceObjects !== 'undefined' && dataLayerSourceObjects.customer.loginType !== "null" && data) {
                window.dataLayer = window.dataLayer || [];
                switch (event) {
                    case 'personal_information_update': {
                        window.dataLayer.push({
                            'event': "personal_information_update",
                            'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                            'userID': dataLayerSourceObjects.customer.userID,
                            'customerID': dataLayerSourceObjects.customer.customerID,
                            'fullName': sha256(data['fullName']),
                            'DOB': sha256(data['DOB']),
                            'Gender': data['Gender'],
                            'marital_status': data['marital_status'],
                            'ktp_no': sha256(data['ktp_no'])
                        });
                    }
                        break;
                    case 'email_update': {
                        window.dataLayer.push({
                            'event': "email_update",
                            'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                            'userID': dataLayerSourceObjects.customer.userID,
                            'customerID': dataLayerSourceObjects.customer.customerID,
                            'email': dataLayerSourceObjects.customer.email
                        });
                    }
                        break;
                    case 'phoneNumber_update': {
                        window.dataLayer.push({
                            'event': "phoneNumber_update",
                            'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                            'userID': dataLayerSourceObjects.customer.userID,
                            'customerID': dataLayerSourceObjects.customer.customerID,
                            'phoneNumber': dataLayerSourceObjects.customer.phoneNumber
                        });
                    }
                        break;
                    case 'update_myAddress':
                    case 'create_new_address':
                    case 'set_mainaddress':
                    case 'delete_address': {
                        window.dataLayer.push({
                            'event': event,
                            'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                            'userID': dataLayerSourceObjects.customer.userID,
                            'customerID': dataLayerSourceObjects.customer.customerID,
                            'address_title': data['address_title'],
                            'recepient_name': data['recepient_name'],
                            'phoneNumber': sha256(data['phoneNumber']),
                            'province': data['province'],
                            'district': data['district'],
                            'city': data['city'],
                            'address': sha256(data['address']),
                            'pinpoint': sha256(data['pinpoint'])
                        });
                    }
                        break;
                    case 'search_order_list':
                    case 'fail_search_order_list': {
                        window.dataLayer.push({
                            'event': event,
                            'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                            'userID': dataLayerSourceObjects.customer.userID,
                            'customerID': dataLayerSourceObjects.customer.customerID,
                            'customerType': dataLayerSourceObjects.customer.customerType,
                            'loyalty': dataLayerSourceObjects.customer.loyalty,
                            'customerStatus': dataLayerSourceObjects.customer.customerStatus,
                            'loginType': dataLayerSourceObjects.customer.loginType,
                            'store_name': dataLayerSourceObjects.customer.storeName,
                            'store_ID': dataLayerSourceObjects.customer.storeID,
                            'query': data['key']
                        });
                    }
                        break;
                    case 'filter_order_list': {
                        window.dataLayer.push({
                            'event': 'filter_order_list',
                            'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                            'userID': dataLayerSourceObjects.customer.userID,
                            'customerID': dataLayerSourceObjects.customer.customerID,
                            'customerType': dataLayerSourceObjects.customer.customerType,
                            'loyalty': dataLayerSourceObjects.customer.loyalty,
                            'customerStatus': dataLayerSourceObjects.customer.customerStatus,
                            'loginType': dataLayerSourceObjects.customer.loginType,
                            'store_name': dataLayerSourceObjects.customer.storeName,
                            'store_ID': dataLayerSourceObjects.customer.storeID,
                            'filter_category': data['filter_category'],
                            'filter_name': data['filter_name']
                        });
                    }
                        break;
                    case 'suggested_search_plp': {
                        window.dataLayer.push({
                            'event': 'suggested_search_plp',
                            'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                            'userID': dataLayerSourceObjects.customer.userID,
                            'customerID': dataLayerSourceObjects.customer.customerID,
                            'customerType': dataLayerSourceObjects.customer.customerType,
                            'loyalty': dataLayerSourceObjects.customer.loyalty,
                            'customerStatus': dataLayerSourceObjects.customer.customerStatus,
                            'loginType': dataLayerSourceObjects.customer.loginType,
                            'store_name': dataLayerSourceObjects.customer.storeName,
                            'store_ID': dataLayerSourceObjects.customer.storeID,
                            'query': data['query'],
                            'suggested_query': data['suggested_query']
                        });
                    }
                        break;
                    case 'see_order_details':
                    case 'see_invoice_click': {
                        window.dataLayer.push({
                            'event': event,
                            'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                            'userID': dataLayerSourceObjects.customer.userID,
                            'customerID': dataLayerSourceObjects.customer.customerID,
                            'customerType': dataLayerSourceObjects.customer.customerType,
                            'loyalty': dataLayerSourceObjects.customer.loyalty,
                            'customerStatus': dataLayerSourceObjects.customer.customerStatus,
                            'loginType': dataLayerSourceObjects.customer.loginType,
                            'store_name': dataLayerSourceObjects.customer.storeName,
                            'store_ID': dataLayerSourceObjects.customer.storeID,
                            'referenceNumber': data['referenceNumber'],
                            'basket_value': data['basket_value']
                        });
                    }
                        break;
                    case 'back_to_order_list': {
                        window.dataLayer.push({
                            'event': event,
                            'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                            'userID': dataLayerSourceObjects.customer.userID,
                            'customerID': dataLayerSourceObjects.customer.customerID,
                            'customerType': dataLayerSourceObjects.customer.customerType,
                            'loyalty': dataLayerSourceObjects.customer.loyalty,
                            'customerStatus': dataLayerSourceObjects.customer.customerStatus,
                            'loginType': dataLayerSourceObjects.customer.loginType,
                            'store_name': dataLayerSourceObjects.customer.storeName,
                            'store_ID': dataLayerSourceObjects.customer.storeID
                        });
                    }
                        break;
                    case 'reorder_all_button': {
                        window.dataLayer.push({
                            'event': event,
                            'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                            'userID': dataLayerSourceObjects.customer.userID,
                            'customerID': dataLayerSourceObjects.customer.customerID,
                            'customerType': dataLayerSourceObjects.customer.customerType,
                            'loyalty': dataLayerSourceObjects.customer.loyalty,
                            'customerStatus': dataLayerSourceObjects.customer.customerStatus,
                            'loginType': dataLayerSourceObjects.customer.loginType,
                            'store_name': dataLayerSourceObjects.customer.storeName,
                            'store_ID': dataLayerSourceObjects.customer.storeID,
                            'orderID': data['orderID'],
                            'order_date': data['order_date'],
                            'referenceNumber': data['referenceNumber'],
                            'basket_value': data['basket_value'],
                            'timestamp': moment().format('DD\/MM\/YYYY HH:mm:ss')
                        });
                    }
                        break;
                    case 'track_order_click': {
                        window.dataLayer.push({
                            'event': event,
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
                            'orderID': data['orderID'],
                            'referenceNumber': data['referenceNumber'],
                            'basket_value': data['basket_value'],
                            'tracking_number': data['tracking_number']
                        });
                    }
                        break;
                    case 'reorder_click': {
                        $.each(data['product'],function (key, value) {
                            window.dataLayer.push({
                                'event': "reorder_button",
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
                                'orderID': data['orderID'],
                                'referenceNumber': data['referenceNumber'],
                                'basket_value': data['basket_value'],
                                'product_ID': value['id'],
                                'product_name': value['name'],
                                'product_price': value['price'],
                                'product_brand': value['brand'],
                                'product_category': value['category'],
                                'product_variant': value['variant'],
                                'product_size': value['product_size'],
                                'product_volume': value['product_volume'],
                                'product_weight': value['product_weight'],
                                'delivery_method': data['delivery_method'],
                                'delivery_fee': data['delivery_fee']
                            });
                        });
                    }
                        break;
                    case 'complete_order': {
                        let dataProduct = [];
                        $.each(data['product'],function (key, value) {
                            window.dataLayer.push({
                                'event': "complete_order",
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
                                'orderID': data['orderID'],
                                'referenceNumber': data['referenceNumber'],
                                'basket_value': data['basket_value'],
                                'product_name': value['name'],
                                'product_id' : value['id'],
                                'product_price': value['price'],
                                'product_brand': value['brand'],
                                'product_category': value['category'],
                                'product_variant': value['variant'],
                                'product_size': value['product_size'],
                                'product_volume': value['product_volume'],
                                'product_weight': value['product_weight'],
                                'delivery_method': data['delivery_method'],
                                'delivery_fee': data['delivery_fee'],
                                'orderStatus': data['orderStatus']
                            });
                        });
                    }
                        break;
                    case 'digital_product_buy_button':{
                        window.dataLayer.push({
                            'event': "digital_product_buy_button",
                            'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                            'userID': dataLayerSourceObjects.customer.userID,
                            'customerID': dataLayerSourceObjects.customer.customerID,
                            'customerType': dataLayerSourceObjects.customer.customerType,
                            'loyalty': dataLayerSourceObjects.customer.loyalty,
                            'customerStatus': dataLayerSourceObjects.customer.customerStatus,
                            'loginType': dataLayerSourceObjects.customer.loginType,
                            'store_name': dataLayerSourceObjects.customer.storeName,
                            'store_ID': dataLayerSourceObjects.customer.storeID,
                            'product_id': data['product_id'],
                            'product_name': data['product_name'],
                            'product_price': data['product_price'],
                            'product_brand': data['product_brand'],
                            'product_category': data['product_category'],
                            'product_variant': data['product_variant'],
                            'product_position': data['product_position']
                        });
                    }
                        break;
                    case 'remove_shoppingList': {
                        window.dataLayer.push({
                            'event': event,
                            'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                            'userID': dataLayerSourceObjects.customer.userID,
                            'customerID': dataLayerSourceObjects.customer.customerID,
                            'customerType': dataLayerSourceObjects.customer.customerType,
                            'loyalty': dataLayerSourceObjects.customer.loyalty,
                            'customerStatus': dataLayerSourceObjects.customer.customerStatus,
                            'loginType': dataLayerSourceObjects.customer.loginType,
                            'timestamp': moment().format('DD\/MM\/YYYY HH:mm:ss'),
                            'shoppingList_name': data['shoppingList_name']
                        });
                    }
                        break;
                }
            }
        }
    };
});
