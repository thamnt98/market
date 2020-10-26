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
                    case 'apply_voucher':
                    case 'remove_voucher':{
                        window.dataLayer.push({
                            'event': event,
                            'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                            'userID': dataLayerSourceObjects.customer.userID,
                            'customerID': dataLayerSourceObjects.customer.customerID,
                            'customerType': dataLayerSourceObjects.customer.customerType,
                            'loyalty': dataLayerSourceObjects.customer.loyalty,
                            'customerStatus': dataLayerSourceObjects.customer.customerStatus,
                            'loginType': dataLayerSourceObjects.customer.loginType,
                            'voucher_id': data['voucher_id'],
                            'voucher_name': data['voucher_name'],
                            'voucher_description': data['voucher_description'],
                            'voucher_validation': data['voucher_validation'],
                            'voucher_status': data['voucher_status']
                        });
                    }
                        break;
                    case "checkout": {
                        let dataProduct = [];
                        $.each(data,function (key, value) {
                            let product = {
                                'delivery_option': value['delivery_option'],
                                'product_size': value['product_size'],
                                'product_volume': value['product_volume'],
                                'product_weight': value['product_weight'],
                                'product_type': value['type'],
                                'salePrice': value['salePrice'],
                                'discountRate': value['discountRate'],
                                'rating': value['rating'],
                                'initialPrice': value['initialPrice'],
                                'name': value['name'],
                                'id': value['id'],
                                'price': value['price'],
                                'brand': value['brand'],
                                'category': value['category'],
                                'variant': value['variant'],
                                'quantity': value['quantity']
                            };
                            dataProduct.push(product);
                        });
                        window.dataLayer.push({
                            'event': "checkout",
                            'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                            'userID': dataLayerSourceObjects.customer.userID,
                            'customerID': dataLayerSourceObjects.customer.customerID,
                            'customerType': dataLayerSourceObjects.customer.customerType,
                            'loyalty': dataLayerSourceObjects.customer.loyalty,
                            'customerStatus': dataLayerSourceObjects.customer.customerStatus,
                            'loginType': dataLayerSourceObjects.customer.loginType,
                            'timestamp': moment().format('DD\/MM\/YYYY HH:mm:ss'),
                            'email': dataLayerSourceObjects.quote.email,
                            'basket_id': dataLayerSourceObjects.customer.basketID,
                            'basket_value': data['basket_value'],
                            'basket_quantity': data['basket_quantity'],
                            'ecommerce': {
                                'checkout': {
                                    'actionField': {
                                        'step': data['step'],
                                        'option': data['option']
                                    },
                                    'products': dataProduct
                                }
                            },
                            'eventCallback': data['eventCallback'],
                            'eventTimeout': data['eventTimeout']
                        });
                    }
                        break;
                    case "purchase": {
                        let dataProduct = [];
                        let revenue = 0;
                        $.each(data['product'],function (key, value) {
                            revenue += value['price'] * value['quantity'];
                            let product = {
                                'delivery_option': value['delivery_option'],
                                'product_size': value['product_size'],
                                'product_volume': value['product_volume'],
                                'product_weight': value['product_weight'],
                                'product_type': value['type'],
                                'salePrice': value['salePrice'],
                                'discountRate': value['discountRate'],
                                'rating': value['rating'],
                                'initialPrice': value['initialPrice'],
                                'name': value['name'],
                                'id': value['id'],
                                'price': value['price'],
                                'brand': value['brand'],
                                'category': value['category'],
                                'variant': value['variant'],
                                'quantity': value['quantity']
                            };
                            dataProduct.push(product);
                        });
                        window.dataLayer.push({
                            'event': "purchase",
                            'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                            'userID': dataLayerSourceObjects.customer.userID,
                            'customerID': dataLayerSourceObjects.customer.customerID,
                            'customerType': dataLayerSourceObjects.customer.customerType,
                            'loyalty': dataLayerSourceObjects.customer.loyalty,
                            'customerStatus': dataLayerSourceObjects.customer.customerStatus,
                            'loginType': dataLayerSourceObjects.customer.loginType,
                            'timestamp': moment().format('DD\/MM\/YYYY HH:mm:ss'),
                            'email': dataLayerSourceObjects.quote.email,
                            'basket_id': dataLayerSourceObjects.customer.basketID,
                            'basket_value': data['basket_value'],
                            'basket_quantity': data['basket_quantity'],
                            'payment_method': data['payment_method'],
                            'ecommerce': {
                                'purchase': {
                                    'actionField': {
                                        'id': data['id'],
                                        'revenue': revenue,
                                        'shipping': data['shipping'],
                                        'coupon': data['coupon']
                                    },
                                    'products': dataProduct
                                }
                            },
                            'eventTimeout': data['eventTimeout']
                        });
                    }
                        break;
                    case "checkout_success": {
                        let dataProduct = [];
                        $.each(data['product'],function (key, value) {
                            let product = {
                                'delivery_option': value['delivery_option'],
                                'product_size': value['product_size'],
                                'product_volume': value['product_volume'],
                                'product_weight': value['product_weight'],
                                'product_type': value['type'],
                                'salePrice': value['salePrice'],
                                'discountRate': value['discountRate'],
                                'rating': value['rating'],
                                'initialPrice': value['initialPrice'],
                                'name': value['name'],
                                'id': value['id'],
                                'price': value['price'],
                                'brand': value['brand'],
                                'category': value['category'],
                                'variant': value['variant'],
                                'quantity': value['quantity']
                            };
                            dataProduct.push(product);
                        });
                        window.dataLayer.push({
                            'event': "checkout",
                            'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                            'userID': dataLayerSourceObjects.customer.userID,
                            'customerID': dataLayerSourceObjects.customer.customerID,
                            'customerType': dataLayerSourceObjects.customer.customerType,
                            'loyalty': dataLayerSourceObjects.customer.loyalty,
                            'customerStatus': dataLayerSourceObjects.customer.customerStatus,
                            'loginType': dataLayerSourceObjects.customer.loginType,
                            'timestamp': moment().format('DD\/MM\/YYYY HH:mm:ss'),
                            'email': dataLayerSourceObjects.customer.email,
                            'basket_id': dataLayerSourceObjects.customer.basketID,
                            'basket_value': data['basket_value'],
                            'basket_quantity': data['basket_quantity'],
                            'payment_method': data['payment_method'],
                            'ecommerce': {
                                'checkout': {
                                    'actionField': {
                                        'step': data['step'],
                                        'option': data['option']
                                    },
                                    'products': dataProduct
                                }
                            },
                            'eventTimeout': data['eventTimeout']
                        });
                    }
                        break;
                    case 'use_voucher':
                    case 'view_voucher':
                    case 'click_voucher': {
                        window.dataLayer.push({
                            'event': event,
                            'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                            'userID': dataLayerSourceObjects.customer.userID,
                            'customerID': dataLayerSourceObjects.customer.customerID,
                            'voucher_id': data['voucher_id'],
                            'voucher_name': data['voucher_name'],
                            'voucher_description': data['voucher_description'],
                            'voucher_validation': data['voucher_validation'],
                            'voucher_status': data['voucher_status']
                        });
                    }
                        break;
                    case "removeFromCart": {
                        let dataProduct = [];
                        $.each(data,function (key, value) {
                            let product = {
                                'product_size': value['product_size'],
                                'product_volume': value['product_volume'],
                                'product_weight': value['product_weight'],
                                'product_type': value['type'],
                                'salePrice': value['salePrice'],
                                'discountRate': value['discountRate'],
                                'rating': value['rating'],
                                'initialPrice': value['initialPrice'],
                                'name': value['name'],
                                'id': value['id'],
                                'price': value['price'],
                                'brand': value['brand'],
                                'category': value['category'],
                                'variant': value['variant'],
                                'quantity': value['quantity']
                            };
                            dataProduct.push(product);
                        });
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
                            'email': dataLayerSourceObjects.quote.email,
                            'basket_id': dataLayerSourceObjects.customer.basketID,
                            'basket_value': data['basket_value'],
                            'basket_quantity': data['basket_quantity'],
                            'ecommerce': {
                                'remove': {
                                    'products': dataProduct
                                }
                            }
                        });
                    }
                        break;
                    case "addToCart": {
                        let dataProduct = [];
                        $.each(data,function (key, value) {
                            let product = {
                                'product_size': value['product_size'],
                                'product_volume': value['product_volume'],
                                'product_weight': value['product_weight'],
                                'product_type': value['type'],
                                'salePrice': value['salePrice'],
                                'discountRate': value['discountRate'],
                                'rating': value['rating'],
                                'initialPrice': value['initialPrice'],
                                'name': value['name'],
                                'id': value['id'],
                                'price': value['price'],
                                'brand': value['brand'],
                                'category': value['category'],
                                'variant': value['variant'],
                                'quantity': value['quantity']
                            };
                            dataProduct.push(product);
                        });
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
                            'email': dataLayerSourceObjects.quote.email,
                            'basket_id': dataLayerSourceObjects.customer.basketID,
                            'basket_value': data['basket_value'],
                            'basket_quantity': data['basket_quantity'],
                            'ecommerce': {
                                'remove': {
                                    'products': dataProduct
                                }
                            }
                        });
                    }
                        break;
                }
            }
        },
        minusDay: function (dateOne, dateTwo) {
            var dates = {
                convert: function (d) {
                    // Converts the date in d to a date-object. The input can be:
                    //   a date object: returned without modification
                    //  an array      : Interpreted as [year,month,day]. NOTE: month is 0-11.
                    //   a number     : Interpreted as number of milliseconds
                    //                  since 1 Jan 1970 (a timestamp)
                    //   a string     : Any format supported by the javascript engine, like
                    //                  "YYYY/MM/DD", "MM/DD/YYYY", "Jan 31 2009" etc.
                    //  an object     : Interpreted as an object with year, month and date
                    //                  attributes.  **NOTE** month is 0-11.
                    return (
                        d.constructor === Date ? d :
                            d.constructor === Array ? new Date(d[0], d[1], d[2]) :
                                d.constructor === Number ? new Date(d) :
                                    d.constructor === String ? new Date(d) :
                                        typeof d === "object" ? new Date(d.year, d.month, d.date) :
                                            NaN
                    );
                },
                compare: function (a, b) {
                    // Compare two dates (could be of any type supported by the convert
                    // function above) and returns:
                    //  -1 : if a < b
                    //   0 : if a = b
                    //   1 : if a > b
                    // NaN : if a or b is an illegal date
                    // NOTE: The code inside isFinite does an assignment (=).
                    return (
                        isFinite(a = this.convert(a).valueOf()) &&
                        isFinite(b = this.convert(b).valueOf()) ?
                            (a > b) - (a < b) :
                            NaN
                    );
                },
                inRange: function (d, start, end) {
                    // Checks if date in d is between dates in start and end.
                    // Returns a boolean or NaN:
                    //    true  : if d is between start and end (inclusive)
                    //    false : if d is before start or after end
                    //    NaN   : if one or more of the dates is illegal.
                    // NOTE: The code inside isFinite does an assignment (=).
                    return (
                        isFinite(d = this.convert(d).valueOf()) &&
                        isFinite(start = this.convert(start).valueOf()) &&
                        isFinite(end = this.convert(end).valueOf()) ?
                            start <= d && d <= end :
                            NaN
                    );
                }
            }
            var date1 = new Date(dateOne);
            var date2 = new Date(dateTwo);
            if ((date1 || date2) && dates.compare(dateOne, dateTwo) === -1) {
                const diffTime = Math.abs(date2 - date1);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                return diffDays + " days";
            }
            return 'Expired';
        }
    };
});
