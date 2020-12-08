define([
    'jquery',
    'moment'
], function ($, moment) {
    'use strict';
    return {
        push: function (event, data) {
            if (typeof dataLayerSourceObjects !== 'undefined' && dataLayerSourceObjects.customer.loginType !== "null" && data) {
                window.dataLayer = window.dataLayer || [];
                switch (event) {
                    case "productClick": {
                        window.dataLayer.push({
                            'event': "productClick",
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
                            'product_size': data['product_size'],
                            'product_volume': data['product_volume'],
                            'product_weight': data['product_weight'],
                            'salePrice': data['salePrice'],
                            'discountRate': data['discountRate'],
                            'rating': data['rating'],
                            'initialPrice': data['initialPrice'],
                            'promo_ends_time' : dataLayerSourceObjects.promoTime ?? 'Not available',
                            'ecommerce': {
                                'click': {
                                    'actionField': {
                                        'list': data['list']
                                    },
                                    'products': [{
                                        'name': data['name'],
                                        'id': data['id'],
                                        'price': data['price'],
                                        'brand': data['brand'],
                                        'category': data['category'],
                                        'variant': data['variant'],
                                        'position': data['position']
                                    }]
                                }
                            },
                            'eventCallback': data['eventCallback'],
                            'eventTimeout': data['eventTimeout']
                        });
                    }
                        break;
                    case "product_view": {
                        window.dataLayer.push({
                            'event': "product_view",
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
                            'product_size': data['product_size'],
                            'product_volume': data['product_volume'],
                            'product_weight': data['product_weight'],
                            'salePrice': data['salePrice'],
                            'discountRate': data['discountRate'],
                            'rating': data['rating'],
                            'initialPrice': data['initialPrice'],
                            'promo_ends_time' : dataLayerSourceObjects.promoTime ?? 'Not available',
                            'ecommerce': {
                                'currencyCode': dataLayerSourceObjects.store.currency,
                                'impressions': [{
                                    'name': data['name'],
                                    'id': data['id'],
                                    'price': data['price'],
                                    'brand': data['brand'],
                                    'category': data['category'],
                                    'variant': data['variant'],
                                    'list': data['list'],
                                    'position': data['position']
                                }]
                            }
                        });
                    }
                        break;
                    case "add_to_favorite": {
                        window.dataLayer.push({
                            'event': "add_to_favorite",
                            'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                            'userID': dataLayerSourceObjects.customer.userID,
                            'customerID': dataLayerSourceObjects.customer.customerID,
                            'customerType': dataLayerSourceObjects.customer.customerType,
                            'loyalty': dataLayerSourceObjects.customer.loyalty,
                            'customerStatus': dataLayerSourceObjects.customer.customerStatus,
                            'loginType': dataLayerSourceObjects.customer.loginType,
                            'store_name': dataLayerSourceObjects.customer.storeName,
                            'store_ID': dataLayerSourceObjects.customer.storeID,
                            'product_name': data['name'],
                            'product_id': data['id'],
                            'product_price': data['price'],
                            'product_brand': data['brand'],
                            'product_category': data['category'],
                            'product_variant': data['variant'],
                            'product_list': data['list'],
                            'product_position': data['position']
                        });
                    }
                        break;
                    case "addToCart": {
                        if (data['price'] !== "Not available") {
                            window.dataLayer.push({
                                'event': "addToCart",
                                'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                                'userID': dataLayerSourceObjects.customer.userID,
                                'customerID': dataLayerSourceObjects.customer.customerID,
                                'customerType': dataLayerSourceObjects.customer.customerType,
                                'loyalty': dataLayerSourceObjects.customer.loyalty,
                                'customerStatus': dataLayerSourceObjects.customer.customerStatus,
                                'loginType': dataLayerSourceObjects.customer.loginType,
                                'timestamp': moment().format('DD\/MM\/YYYY HH:mm:ss'),
                                'product_size': data['product_size'],
                                'product_volume': data['product_volume'],
                                'product_weight': data['product_weight'],
                                'product_type': data['type'],
                                'salePrice': data['salePrice'],
                                'discountRate': data['discountRate'],
                                'rating': data['rating'],
                                'initialPrice': data['initialPrice'],
                                'email': dataLayerSourceObjects.quote.email,
                                'basket_id': dataLayerSourceObjects.customer.basketID,
                                'basket_value': data['price'],
                                'basket_quantity': 1,
                                'ecommerce': {
                                    'currencyCode': dataLayerSourceObjects.store.currency,
                                    'add': {
                                        'products': [{
                                            'name': data['name'],
                                            'id': data['id'],
                                            'price': data['price'],
                                            'brand': data['brand'],
                                            'category': data['category'],
                                            'variant': data['variant'],
                                            'quantity': 1
                                        }]
                                    }
                                }
                            });
                        }
                    }
                        break;
                    case "removeFromCart": {
                        if (data['price'] !== "Not available") {
                            window.dataLayer.push({
                                'event': "removeFromCart",
                                'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                                'userID': dataLayerSourceObjects.customer.userID,
                                'customerID': dataLayerSourceObjects.customer.customerID,
                                'customerType': dataLayerSourceObjects.customer.customerType,
                                'loyalty': dataLayerSourceObjects.customer.loyalty,
                                'customerStatus': dataLayerSourceObjects.customer.customerStatus,
                                'loginType': dataLayerSourceObjects.customer.loginType,
                                'timestamp': moment().format('DD\/MM\/YYYY HH:mm:ss'),
                                'product_size': data['product_size'],
                                'product_volume': data['product_volume'],
                                'product_weight': data['product_weight'],
                                'product_type': data['type'],
                                'salePrice': data['salePrice'],
                                'discountRate': data['discountRate'],
                                'rating': data['rating'],
                                'initialPrice': data['initialPrice'],
                                'email': dataLayerSourceObjects.quote.email,
                                'basket_id': dataLayerSourceObjects.customer.basketID,
                                'basket_value': data['price'],
                                'basket_quantity': 1,
                                'ecommerce': {
                                    'currencyCode': dataLayerSourceObjects.store.currency,
                                    'remove': {
                                        'products': [{
                                            'name': data['name'],
                                            'id': data['id'],
                                            'price': data['price'],
                                            'brand': data['brand'],
                                            'category': data['category'],
                                            'variant': data['variant'],
                                            'quantity': 1
                                        }]
                                    }
                                }
                            });
                        }
                    }
                        break;
                    case "see_all_category_page": {
                        window.dataLayer.push({
                            'event': "see_all_category_page",
                            'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                            'userID': dataLayerSourceObjects.customer.userID,
                            'customerID': dataLayerSourceObjects.customer.customerID,
                            'customerType': dataLayerSourceObjects.customer.customerType,
                            'loyalty': dataLayerSourceObjects.customer.loyalty,
                            'customerStatus': dataLayerSourceObjects.customer.customerStatus,
                            'loginType': dataLayerSourceObjects.customer.loginType,
                            'shop_category': dataLayerSourceObjects.page.pageCategory,
                            'list_name': data['shop_category'],
                            'store_name': dataLayerSourceObjects.customer.storeName,
                            'store_ID': dataLayerSourceObjects.customer.storeID
                        });
                    }
                        break;
                    case 'product_detail_view': {
                        window.dataLayer.push({
                            'event': "product_detail_view",
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
                            'product_size': data['product_size'],
                            'product_volume': data['product_volume'],
                            'product_weight': data['product_weight'],
                            'product_rating': data['rating'],
                            'product_type': data['type'],
                            'salePrice': data['salePrice'],
                            'discountRate': data['discountRate'],
                            'initialPrice': data['initialPrice'],
                            'promo_ends_time' : dataLayerSourceObjects.promoTime ?? 'Not available',
                            'ecommerce': {
                                'detail': {
                                    'actionField': {
                                        'list': data['list']
                                    },
                                    'products': [{
                                        'name': data['name'],
                                        'id': data['id'],
                                        'price': data['price'],
                                        'brand': data['brand'],
                                        'category': data['category'],
                                        'variant': data['variant']
                                    }]
                                }
                            }
                        });
                    }
                        break;
                    case 'click_picture_PDP': {
                        window.dataLayer.push({
                            'event': "click_picture_PDP",
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
                            'product_ID': data['id'],
                            'product_name': data['name'],
                            'product_price': data['price'],
                            'product_brand': data['brand'],
                            'product_category': data['category'],
                            'product_variant': data['variant'],
                            'product_size': data['product_size'],
                            'product_volume': data['product_volume'],
                            'product_weight': data['product_weight'],
                            'product_type': data['type'],
                            'product_rating': data['rating'],
                            'picture_position': data['picture_position'],
                            'salePrice': data['salePrice'],
                            'discountRate': data['discountRate'],
                            'initialPrice': data['initialPrice'],
                            'promo_ends_time' : dataLayerSourceObjects.promoTime ?? 'Not available'
                        });
                    }
                        break;
                    case 'share_product': {
                        window.dataLayer.push({
                            'event': "share_product",
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
                            'product_ID': data['id'],
                            'product_name': data['name'],
                            'product_price': data['price'],
                            'product_brand': data['brand'],
                            'product_category': data['category'],
                            'product_variant': data['variant'],
                            'product_size': data['product_size'],
                            'product_volume': data['product_volume'],
                            'product_weight': data['product_weight'],
                            'product_type': data['type'],
                            'product_rating': data['rating'],
                            'social_media': data['social_media'],
                            'salePrice': data['salePrice'],
                            'discountRate': data['discountRate'],
                            'initialPrice': data['initialPrice'],
                            'promo_ends_time' : dataLayerSourceObjects.promoTime ?? 'Not available'
                        });
                    }
                        break;
                    case 'view_review': {
                        window.dataLayer.push({
                            'event': "view_review",
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
                            'product_ID': data['id'],
                            'product_name': data['name'],
                            'product_price': data['price'],
                            'product_brand': data['brand'],
                            'product_category': data['category'],
                            'product_variant': data['variant'],
                            'product_size': data['product_size'],
                            'product_volume': data['product_volume'],
                            'product_weight': data['product_weight'],
                            'product_type': data['type'],
                            'product_rating': data['rating'],
                            'salePrice': data['salePrice'],
                            'discountRate': data['discountRate'],
                            'initialPrice': data['initialPrice'],
                            'promo_ends_time' : dataLayerSourceObjects.promoTime ?? 'Not available'
                        });
                    }
                        break;
                    case 'review_submitted': {
                        window.dataLayer.push({
                            'event': "review_submitted",
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
                            'product_ID': data['id'],
                            'product_name': data['name'],
                            'product_price': data['price'],
                            'product_brand': data['brand'],
                            'product_category': data['category'],
                            'product_variant': data['variant'],
                            'product_size': data['product_size'],
                            'product_volume': data['product_volume'],
                            'product_weight': data['product_weight'],
                            'product_type': data['type'],
                            'product_quantity': data['product_quantity'],
                            'product_rating': data['rating'],
                            'salePrice': data['salePrice'],
                            'discountRate': data['discountRate'],
                            'initialPrice': data['initialPrice'],
                            'promo_ends_time' : dataLayerSourceObjects.promoTime ?? 'Not available',
                            'review_title': data['review_title'],
                            'review_message': data['review_message'],
                            'review_photo': data['review_photo'],
                            'review_rating': data['review_rating']
                        });
                    }
                        break;
                    case 'delivery_info': {
                        window.dataLayer.push({
                            'event': "delivery_info",
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
                            'product_ID': data['id'],
                            'product_name': data['name'],
                            'product_price': data['price'],
                            'product_brand': data['brand'],
                            'product_category': data['category'],
                            'product_variant': data['variant'],
                            'product_size': data['product_size'],
                            'product_volume': data['product_volume'],
                            'product_weight': data['product_weight'],
                            'product_type': data['type'],
                            'product_rating': data['rating'],
                            'salePrice': data['salePrice'],
                            'discountRate': data['discountRate'],
                            'initialPrice': data['initialPrice'],
                            'promo_ends_time' : dataLayerSourceObjects.promoTime ?? 'Not available'
                        });
                    }
                        break;
                    case 'store_info': {
                        window.dataLayer.push({
                            'event': "store_info",
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
                            'product_ID': data['id'],
                            'product_name': data['name'],
                            'product_price': data['price'],
                            'product_brand': data['brand'],
                            'product_category': data['category'],
                            'product_variant': data['variant'],
                            'product_size': data['product_size'],
                            'product_volume': data['product_volume'],
                            'product_weight': data['product_weight'],
                            'product_type': data['type'],
                            'product_rating': data['rating'],
                            'salePrice': data['salePrice'],
                            'discountRate': data['discountRate'],
                            'initialPrice': data['initialPrice'],
                            'promo_ends_time' : dataLayerSourceObjects.promoTime ?? 'Not available'
                        });
                    }
                        break;
                    case 'buy_now_pdp': {
                        window.dataLayer.push({
                            'event': "buy_now_pdp",
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
                            'product_ID': data['id'],
                            'product_name': data['name'],
                            'product_price': data['price'],
                            'product_brand': data['brand'],
                            'product_category': data['category'],
                            'product_variant': data['variant'],
                            'product_size': data['product_size'],
                            'product_volume': data['product_volume'],
                            'product_weight': data['product_weight'],
                            'product_type': data['type'],
                            'product_quantity': data['quantity'],
                            'product_rating': data['rating'],
                            'salePrice': data['salePrice'],
                            'discountRate': data['discountRate'],
                            'initialPrice': data['initialPrice'],
                            'promo_ends_time': dataLayerSourceObjects.promoTime ?? 'Not available'
                        });
                    }
                        break;
                    case "addToCartPDP": {
                        $.each(data,function (key, value) {
                            window.dataLayer.push({
                                'event': "addToCart",
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
                                'product_size': value['product_size'],
                                'product_volume': value['product_volume'],
                                'product_weight': value['product_weight'],
                                'product_type': value['type'],
                                'product_rating': value['rating'],
                                'salePrice': value['salePrice'],
                                'discountRate': value['discountRate'],
                                'initialPrice': value['initialPrice'],
                                'promo_ends_time' : dataLayerSourceObjects.promoTime ?? 'Not available',
                                'basket_id': dataLayerSourceObjects.customer.basketID,
                                'basket_value': data['price'],
                                'basket_quantity': value['quantity'],
                                'ecommerce': {
                                    'currencyCode': dataLayerSourceObjects.store.currency,
                                    'add': {
                                        'products': [{
                                            'name': value['name'],
                                            'id': value['id'],
                                            'price': value['price'],
                                            'brand': value['brand'],
                                            'category': value['category'],
                                            'variant': value['variant'],
                                            'quantity':  value['quantity']
                                        }]
                                    }
                                }
                            });
                        });
                    }
                        break;
                    case "add_to_shopping_list": {
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
                            'product_ID': data['id'],
                            'product_name': data['name'],
                            'product_price': data['price'],
                            'product_brand': data['brand'],
                            'product_category': data['category'],
                            'product_variant': data['variant'],
                            'product_size': data['product_size'],
                            'product_volume': data['product_volume'],
                            'product_weight': data['product_weight'],
                            'product_type': data['type'],
                            'product_quantity': data['quantity'],
                            'product_rating': data['rating'],
                            'salePrice': data['salePrice'],
                            'discountRate': data['discountRate'],
                            'initialPrice': data['initialPrice'],
                            'promo_ends_time' : dataLayerSourceObjects.promoTime ?? 'Not available',
                            'shopping_listName' : data['shopping_listName']
                        });
                    }
                        break;
                    case 'pdp_info_tab': {
                        window.dataLayer.push({
                            'event': "pdp_info_tab",
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
                            'menu_name' : data['menu_name'],
                            'product_ID': data['id'],
                            'product_name': data['name'],
                            'product_price': data['price'],
                            'product_brand': data['brand'],
                            'product_category': data['category'],
                            'product_variant': data['variant'],
                            'product_size': data['product_size'],
                            'product_volume': data['product_volume'],
                            'product_weight': data['product_weight'],
                            'product_type': data['type'],
                            'product_quantity': data['quantity'],
                            'product_rating': data['rating'],
                            'salePrice': data['salePrice'],
                            'discountRate': data['discountRate'],
                            'initialPrice': data['initialPrice'],
                            'promo_ends_time': dataLayerSourceObjects.promoTime ?? 'Not available'
                        });
                    }
                        break;
                    case 'seeDetails_product_inspireMe': {
                        window.dataLayer.push({
                            'event': "seeDetails_product_inspireMe",
                            'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                            'userID': dataLayerSourceObjects.customer.userID,
                            'customerID': dataLayerSourceObjects.customer.customerID,
                            'customerType': dataLayerSourceObjects.customer.customerType,
                            'loyalty': dataLayerSourceObjects.customer.loyalty,
                            'customerStatus': dataLayerSourceObjects.customer.customerStatus,
                            'loginType': dataLayerSourceObjects.customer.loginType,
                            'product_name': data['name'],
                            'product_id': data['id'],
                            'product_size': data['product_size'],
                            'product_volume': data['product_volume'],
                            'product_weight': data['product_weight'],
                            'product_type': data['type'],
                            'product_price': data['price']
                        });
                    }
                        break;
                    case 'compare_product': {
                        window.dataLayer.push({
                            'event': "compare_product",
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
                            'product_ID': data['id'],
                            'product_name': data['name'],
                            'product_price': data['price'],
                            'product_brand': data['brand'],
                            'product_category': data['category'],
                            'product_variant': data['variant'],
                            'product_size': data['product_size'],
                            'product_volume': data['product_volume'],
                            'product_weight': data['product_weight'],
                            'salePrice': data['salePrice'],
                            'discountRate': data['discountRate'],
                            'initialPrice': data['initialPrice'],
                            'rating': data['rating']
                        });
                    }
                        break;
                    case 'compare_product_review':
                    case 'remove_compare_product': {
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
                            'product_name': data['name'],
                            'product_id': data['id'],
                            'product_price': data['price'],
                            'product_brand': data['brand'],
                            'product_category': data['category'],
                            'product_variant': data['variant'],
                            'product_size': data['product_size'],
                            'product_volume': data['product_volume'],
                            'product_weight': data['product_weight'],
                            'salePrice': data['salePrice'],
                            'discountRate': data['discountRate'],
                            'initialPrice': data['initialPrice'],
                            'rating': data['rating']
                        });
                    }
                        break;
                    case "add_to_shoppingList": {
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
                            'product_ID': data['id'],
                            'product_name': data['name'],
                            'product_price': data['price'],
                            'product_brand': data['brand'],
                            'product_category': data['category'],
                            'product_variant': data['variant'],
                            'product_size': data['product_size'],
                            'product_volume': data['product_volume'],
                            'product_weight': data['product_weight'],
                            'salePrice': data['salePrice'],
                            'discountRate': data['discountRate'],
                            'initialPrice': data['initialPrice'],
                            'rating': data['rating'],
                            'shopping_listName' : data['shopping_listName']
                        });
                    }
                        break;
                }
            }
        }
    };
});
