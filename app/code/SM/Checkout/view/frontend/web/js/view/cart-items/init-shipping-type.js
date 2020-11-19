/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'jquery',
    'ko',
    'mage/url',
    'Magento_Checkout/js/model/totals',
    'Magento_Customer/js/model/address-list',
    'SM_Checkout/js/action/get-shipping-method',
    'SM_Checkout/js/action/set-shipping-rates',
    'SM_Checkout/js/view/shipping-address/update-delivery-address-status',
    'SM_Checkout/js/view/cart-items/set-shipping-type',
    'SM_Checkout/js/view/shipping-address/current-pickup',
    'SM_Checkout/js/view/default-shipping-method',
    'SM_Checkout/js/view/global-observable',
    'SM_Checkout/js/action/find-store',
    'SM_Checkout/js/view/shipping-address/single-date-time-select',
    'SM_Checkout/js/action/reset',
    'SM_Checkout/js/action/digital-detail',
    'SM_Checkout/js/view/cart-items/current-items'
], function (
    $,
    ko,
    urlBuilder,
    totals,
    addressList,
    getShippingMethod,
    setShippingRates,
    updateStatus,
    setShippingType,
    pickup,
    defaultShipping,
    globalVar,
    findStoreAction,
    singleDateTime,
    reset,
    digitalDetail,
    currentItemsData
) {
    'use strict';

    let mod = {};
    var notAvailableMethod = 'transshipping_transshipping0',
        imageData = window.checkoutConfig.imageData,
        preSelectItems = window.checkoutConfig.pre_select_items,
        singleOrderIsFresh = false,
        processing = false,
        first = true,
        updateShortestStore = ko.observable(false),
        singleDeliveryMethodError = ko.observable(false),
        singleDeliveryMethodValid = ko.observableArray(['transshipping_transshipping0', 'transshipping_transshipping1', 'transshipping_transshipping2', 'transshipping_transshipping3', 'transshipping_transshipping4']),
        singleStorePickUpError = ko.observable(false),
        firstOrderSelectAddressList = true,
        deliveryMethodListSingle = ko.observableArray([]),
        addressListData = {},
        paymentFail = window.checkoutConfig.payment_fail,
        orderVirtual = window.checkoutConfig.is_virtual,
        storePickupAddressId = window.checkoutConfig.defaultShippingAddressId,
        defaultBillingId = window.checkoutConfig.defaultShippingAddressId,
        selectSingleShippingMethod = ko.observable(window.checkoutConfig.pre_select_single_method),
        selectSingleStorePickup = ko.observable(),
        itemsDataSingleAddress = ko.observable({}),
        itemsData = ko.observable({}),
        deliveryMethodList = {},
        deliveryMethodListError = {},
        storePickUpListError = {},
        addressTagList = ko.observableArray([]),
        itemList = ko.observableArray([]),
        selectShippingType = {},
        disableDeliveryList = {},
        addressSelectedList = {},
        storeSelectedList = {},
        shippingMethodSelectList = {},
        shippingMethodListValid = {},
        data = {},
        dataSingleAddress = {},
        timeSlotListStatus = {},
        addressListDate = {},
        addressListTime = {},
        customerAddressData = {},
        itemsDataList = {};

    $.each(addressList(), function (index, address) {
        customerAddressData[address.customerAddressId] = address;
        var customAttr = address.customAttributes;
        if (customAttr) {
            var tagAttr = customAttr.address_tag;
            if (tagAttr) {
                addressListData[address.customerAddressId] = tagAttr.value;
            }
        }
        addressListDate[address.customerAddressId] = ko.observable('');
        addressListTime[address.customerAddressId] = ko.observable('');
    });
    addressList.subscribe(function (changes) {
            $.each(addressList(), function (index, address) {
                customerAddressData[address.customerAddressId] = address;
                var customAttr = address.customAttributes;
                if (customAttr) {
                    var tagAttr = customAttr.address_tag;
                    if (tagAttr) {
                        addressListData[address.customerAddressId] = tagAttr.value;
                    }
                }
            });
        },
        this,
        'arrayChange'
    );
    var itemsCount = totals.getItems()().length,
        i = 0;
    $.each(totals.getItems()(), function (index, item) {
        i++;
        var preSelectThisItem = preSelectItems[item.item_id];
        itemsDataList[item.item_id] = ko.observable(item);
        itemList.push(item.item_id);
        /* selectShippingType init each item id*/
        selectShippingType[item.item_id] = ko.observable(preSelectThisItem.type);
        selectShippingType[item.item_id].subscribe(function (value) {
            if (value == '0') {
                disableDeliveryList[item.item_id](false);
            } else {
                disableDeliveryList[item.item_id](true);
            }
        });
        /* disableDeliveryList init each item id*/
        timeSlotListStatus[item.item_id] = ko.observable(true);
        shippingMethodListValid[item.item_id] = ko.observableArray(['transshipping_transshipping0', 'transshipping_transshipping1', 'transshipping_transshipping2', 'transshipping_transshipping3', 'transshipping_transshipping4']);
        storePickUpListError[item.item_id] = ko.observable(true);
        deliveryMethodListError[item.item_id] = ko.observable(false);
        if (preSelectThisItem.type == '1') {
            disableDeliveryList[item.item_id] = ko.observable(true);
        } else {
            disableDeliveryList[item.item_id] = ko.observable(false);
        }

        disableDeliveryList[item.item_id].subscribe(function (value) {
            console.log('disableDeliveryList');
            if (typeof value !== 'undefined') {
                mod.getShippingMethod();
            }
        });

        /* addressSelectedList init each item id*/
        addressSelectedList[item.item_id] = ko.observable(preSelectThisItem.address.toString());
        addressSelectedList[item.item_id].subscribe(function (value) {
            console.log('addressSelectedList');
            console.log('address per item change');
            if (!processing) {
                mod.getShippingMethod();
            }
        });

        /* storeSelectedList init each item id*/
        storeSelectedList[item.item_id] = ko.observable();

        /* create items data*/
        data[item.item_id] = defaultBillingId;
        dataSingleAddress[item.item_id] = defaultBillingId;
        if (preSelectThisItem.shipping_method == 'store_pickup_store_pickup') {
            var shippingMethod = 'transshipping_transshipping1';
        } else {
            var shippingMethod = preSelectThisItem.shipping_method;
        }
        shippingMethodSelectList[item.item_id] = ko.observable(shippingMethod);
        shippingMethodSelectList[item.item_id].subscribe(function (value) {
            if (typeof value !== 'undefined' && !processing && value != notAvailableMethod) {
                console.log('shipping-method-change');
                mod.getShippingMethod();
            }
        });
    });
    itemsData(data);
    itemsDataSingleAddress(dataSingleAddress);

    updateStatus.getOrderSelectAddressList().subscribe(function (changes) {
            console.log('getOrderSelectAddressList.subscribe => estimate => set shipping');
            mod.getShippingMethod();
        },
        this,
        'arrayChange'
    );

    addressTagList = ko.computed(function() {
        var orderSelectAddressList = updateStatus.getOrderSelectAddressList()(),
            addressTagListRender = [],
            i = 0;
        $.each(orderSelectAddressList, function (index, customerAddressId) {
            addressTagListRender[i] = {label: addressListData[customerAddressId], value: customerAddressId};
            i++;
        });
        return addressTagListRender;
    }, this);

    selectSingleShippingMethod.subscribe(function (value) {
        if (typeof value !== 'undefined' && value != notAvailableMethod) {
            console.log('selectSingleShippingMethod => set shipping');
            mod.getShippingMethod();
        }
    });
    selectSingleStorePickup.subscribe(function (value) {
        if (typeof value !== 'undefined') {
            console.log('selectSingleStorePickup');
            mod.getShippingMethod();
        }
    });

    // order delivery type change
    setShippingType.getValue().subscribe(function (value) {
        console.log('order delivery type change');
        mod.getShippingMethod();
    });

    findStoreAction.storeFullFill.subscribe(function (newValue) {
        $.each(disableDeliveryList, function (itemId, data) {
            if (!data()) {
                storePickUpListError[itemId](true);
            } else {
                storePickUpListError[itemId](newValue);
            }
        });
        singleStorePickUpError(newValue);
    });
    ko.subscribable.fn.subscribeChanged = function (callback) {
        var oldValue;
        this.subscribe(function (_oldValue) {
            oldValue = _oldValue;
        }, this, 'beforeChange');

        this.subscribe(function (newValue) {
            callback(newValue, oldValue);
        });
    };
    pickup.currentPickupId.subscribeChanged(function (newValue, oldValue) {
        var sourceShortestDistanceList = findStoreAction.sourceShortestDistanceList;
        $.each(sourceShortestDistanceList(), function (index, source) {
            if (source.source_code == newValue) {
                findStoreAction.storeFullFill(true);
                return false;
            }
        });
        console.log(oldValue);
        console.log(newValue);
        if (typeof oldValue !== "undefined" && setShippingType.getValue()() == '1') {
            console.log('store-pick-up-change');
            mod.getShippingMethod(true);
        } else if (typeof oldValue !== "undefined" && setShippingType.getValue()() == '2') {
            $.each(disableDeliveryList, function (itemId, disable) {
                if (disable()) {
                    console.log('store-pick-up-change');
                    mod.getShippingMethod(true);
                    return false;
                }
            });

        }

    });

    globalVar.splitOrder.subscribe(function (newValue) {
        console.log('slit-order-change-status');
        if (setShippingType.getValue()() == '0' && addressTagList().length == 1) {
            if (newValue) {
                // one address, delivery shipping, split order
                if (!processing) {
                    console.log('slit-order-change-status get shipping method');
                    mod.getShippingMethod();
                }
            } else {
                // one address, delivery shipping, not split order
                let currentItemsListId = currentItemsData.getCurrentItemsListId()().split(',');
                if (currentItemsListId.length > 0) {
                    let shippingMethod = shippingMethodSelectList[currentItemsListId[0]]();
                    $('select[name="order-delivery-method"]  option[value="' + shippingMethod + '"]').prop("selected", true);
                    selectSingleShippingMethod(shippingMethod);
                    if (!processing) {
                        $('select[name="order-delivery-method"]').trigger('change');
                    }
                }
            }
        }
    });

    /*=====================================================================*/
    mod.getSelectShippingType = function () {
        return selectShippingType;
    };

    mod.getDisableDeliveryList = function () {
        return disableDeliveryList;
    };

    mod.getAddressSelectedList = function () {
        return addressSelectedList;
    };

    mod.getStoreSelectedList = function () {
        return storeSelectedList;
    };

    mod.getDeliveryMethodList = function () {
        return deliveryMethodList;
    };

    mod.getAddressTagList = function () {
        return addressTagList;
    };

    mod.getShippingMethodSelectList = function () {
        return shippingMethodSelectList;
    };

    mod.setSelectSingleShippingMethod = function () {
        return selectSingleShippingMethod;
    };

    mod.setSelectSingleStorePickup = function () {
        return selectSingleStorePickup;
    };

    mod.getDeliveryMethodListSingle = function () {
        return deliveryMethodListSingle;
    };

    mod.getDeliveryMethodListError = function () {
        return deliveryMethodListError;
    };

    mod.getAddressNameList = function () {
        return addressListData;
    };

    mod.getSingleDeliveryMethodError = function () {
        return singleDeliveryMethodError;
    };

    mod.getStorePickUpListError = function () {
        return storePickUpListError;
    };

    mod.geShippingMethodListValid = function () {
        return shippingMethodListValid;
    };

    mod.getSingleDeliveryMethodValid = function () {
        return singleDeliveryMethodValid;
    };

    mod.getSelectSingleShippingMethod = function () {
        return selectSingleShippingMethod;
    };

    mod.getTimeSlotListStatus =  function () {
        return timeSlotListStatus;
    };

    mod.getAddressListDate =  function () {
        return addressListDate;
    };

    mod.getAddressListTime =  function () {
        return addressListTime;
    };

    mod.getCustomerAddressData =  function () {
        return customerAddressData;
    };

    mod.getItemsDataList =  function () {
        return itemsDataList;
    };

    /*==============================================*/
    mod.getShippingMethod = function (changeStore = false) {
        console.log('getShippingMethod');
        mod.repareItemsData(changeStore);
    };

    mod.repareItemsData = function (changeStore) {
        var orderSelectAddressList = updateStatus.getOrderSelectAddressList()(),
            orderDeliveryType = setShippingType.getValue()(),
            newItemsDataSingleAddress = {},
            newItemsData = {},
            update = false;
        if (orderSelectAddressList.length == 1) { // single address
            if (orderDeliveryType == '0') { // delivery
                $.each(itemsDataSingleAddress(), function (itemId, addressId) {
                    if (addressId != orderSelectAddressList[0]) {
                        newItemsDataSingleAddress[itemId] = orderSelectAddressList[0];
                        update = true;
                    } else {
                        newItemsDataSingleAddress[itemId] = addressId;
                    }
                });

            } else if(orderDeliveryType == '1') { // store pickup
                $.each(itemsDataSingleAddress(), function (itemId, addressId) {
                    if (addressId != storePickupAddressId) {
                        newItemsDataSingleAddress[itemId] = storePickupAddressId;
                        update = true;
                    } else {
                        newItemsDataSingleAddress[itemId] = addressId;
                    }
                });
            } else { // both
                $.each(itemsDataSingleAddress(), function (itemId, addressId) {
                    if (disableDeliveryList[itemId]() && addressId != storePickupAddressId) {
                        newItemsDataSingleAddress[itemId] = storePickupAddressId;
                        update = true;
                    } else if (!disableDeliveryList[itemId]() && addressId != orderSelectAddressList[0]) {
                        newItemsDataSingleAddress[itemId] = orderSelectAddressList[0];
                        update = true;
                    } else {
                        newItemsDataSingleAddress[itemId] = addressId;
                    }
                });
            }
            if (Object.keys(newItemsDataSingleAddress).length > 0 && update) {
                itemsDataSingleAddress(newItemsDataSingleAddress);
            }

        } else { //two address
            if (orderDeliveryType == '0') { // delivery
                console.log(itemsData());
                $.each(itemsData(), function (itemId, addressId) {
                    if (typeof addressSelectedList[itemId]() !== 'undefined' && addressId != addressSelectedList[itemId]()) {
                        newItemsData[itemId] = addressSelectedList[itemId]();
                        update = true;
                    } else {
                        newItemsData[itemId] = addressId;
                    }
                });
            } else if(orderDeliveryType == '1') { // store pickup
                $.each(itemsData(), function (itemId, addressId) {
                    if (addressId != storePickupAddressId) {
                        newItemsData[itemId] = storePickupAddressId;
                        update = true;
                    } else {
                        newItemsData[itemId] = addressId;
                    }
                });
            } else { // both
                $.each(itemsData(), function (itemId, addressId) {
                    if (disableDeliveryList[itemId]() && addressId != storePickupAddressId) {
                        newItemsData[itemId] = storePickupAddressId;
                        update = true;
                    } else if (!disableDeliveryList[itemId]() && addressId != addressSelectedList[itemId]()) {
                        newItemsData[itemId] = addressSelectedList[itemId]();
                        update = true;
                    } else {
                        newItemsData[itemId] = addressId;
                    }
                });
            }
            if (Object.keys(newItemsData).length > 0 && update) {
                itemsData(newItemsData);
            }
        }
        mod.setShippingAction(changeStore);
    };

    mod.setShippingAction = function (changeStore) {
        var ratesData = mod.getRatesData(changeStore),
            data = {'items': ratesData.items, 'additional_info': ratesData.additionalInfo, 'type': setShippingType.getValue()(), 'address': updateStatus.getOrderSelectAddressList()().toString()};
        if (changeStore && Object.keys(ratesData.storePickupItems).length === 0) {
            return;
        }
        mod.getShippingMethodAction(data, ratesData.storePickupItems);
         //Get Shipping Method For GTM Event
        window.itemsCheckoutGTM = window.itemsCheckoutGTM || {};
        window.itemsCheckoutGTM = ratesData.items;
    };

    mod.getRatesData = function () {
        var items = [],
            storePickupItems = [],
            additionalInfo = {
                "store_pick_up": {
                    "store_code": null,
                    "date": null,
                    "time": null
                }
            },
            orderSelectAddressList = updateStatus.getOrderSelectAddressList()(),
            orderDeliveryType = setShippingType.getValue()(),
            currentItemsListId = currentItemsData.getCurrentItemsListId()().split(',');
        if (orderSelectAddressList.length == 1) { // single address
            if (orderDeliveryType == '0') { // delivery
                if (globalVar.splitOrder()) {
                    $.each(shippingMethodSelectList, function( itemId, rateSelected ) {
                        if (typeof rateSelected() === 'undefined') {
                            items = [];
                            return false;
                        } else {
                            if($.inArray(itemId.toString(), currentItemsListId) === -1) {
                                return true;
                            }
                            let currentItem = currentItemsData.getCurrentItemsData(itemId),
                                qty = itemsDataList[itemId]().qty;
                            if (typeof currentItem !== "undefined") {
                                if (parseInt(currentItem().qty) != parseInt(itemsDataList[itemId]().qty)) {
                                    qty = currentItem().qty;
                                }
                            }
                            items.push({"item_id": itemId, "qty": qty, "shipping_address_id": addressSelectedList[itemId](), "shipping_method_selected": rateSelected()});
                        }
                    });
                } else {
                    if (typeof selectSingleShippingMethod() !== 'undefined') {
                        $.each(itemsDataSingleAddress(), function( itemId, addressId ) {
                            if($.inArray(itemId.toString(), currentItemsListId) === -1) {
                                return true;
                            }
                            let currentItem = currentItemsData.getCurrentItemsData(itemId),
                                qty = itemsDataList[itemId]().qty;
                            if (typeof currentItem !== "undefined") {
                                if (parseInt(currentItem().qty) != parseInt(itemsDataList[itemId]().qty)) {
                                    qty = currentItem().qty;
                                }
                            }
                            items.push({"item_id": itemId, "qty": qty, "shipping_address_id": addressId, "shipping_method_selected": selectSingleShippingMethod()});
                        });
                    }
                }
            } else if(orderDeliveryType == '1') { // store pickup
                $.each(itemsDataSingleAddress(), function( itemId, addressId ) {
                    if($.inArray(itemId.toString(), currentItemsListId) === -1) {
                        return true;
                    }
                    let currentItem = currentItemsData.getCurrentItemsData(itemId),
                        qty = itemsDataList[itemId]().qty;
                    if (typeof currentItem !== "undefined") {
                        if (parseInt(currentItem().qty) != parseInt(itemsDataList[itemId]().qty)) {
                            qty = currentItem().qty;
                        }
                    }
                    items.push({"item_id": itemId, "qty": qty, "shipping_address_id": storePickupAddressId, "shipping_method_selected": "store_pickup_store_pickup"});
                    additionalInfo.store_pick_up.store_code = pickup.currentPickupId();
                    additionalInfo.store_pick_up.date = pickup.storePickUpDate();
                    additionalInfo.store_pick_up.time = pickup.storePickUpTime();
                    storePickupItems.push(itemId);
                });
            } else { // both
                $.each(shippingMethodSelectList, function( itemId, rateSelected ) {
                    if (disableDeliveryList[itemId]()) {
                        if($.inArray(itemId.toString(), currentItemsListId) === -1) {
                            return true;
                        }
                        let currentItem = currentItemsData.getCurrentItemsData(itemId),
                            qty = itemsDataList[itemId]().qty;
                        if (typeof currentItem !== "undefined") {
                            if (parseInt(currentItem().qty) != parseInt(itemsDataList[itemId]().qty)) {
                                qty = currentItem().qty;
                            }
                        }
                        items.push({"item_id": itemId, "qty": qty, "shipping_address_id": storePickupAddressId, "shipping_method_selected": "store_pickup_store_pickup"});
                        additionalInfo.store_pick_up.store_code = pickup.currentPickupId();
                        additionalInfo.store_pick_up.date = pickup.storePickUpDate();
                        additionalInfo.store_pick_up.time = pickup.storePickUpTime();
                        storePickupItems.push(itemId);
                    } else {
                        if (typeof rateSelected() === 'undefined') {
                            items = [];
                            return false;
                        } else {
                            if($.inArray(itemId.toString(), currentItemsListId) === -1) {
                                return true;
                            }
                            let currentItem = currentItemsData.getCurrentItemsData(itemId),
                                qty = itemsDataList[itemId]().qty;
                            if (typeof currentItem !== "undefined") {
                                if (parseInt(currentItem().qty) != parseInt(itemsDataList[itemId]().qty)) {
                                    qty = currentItem().qty;
                                }
                            }
                            items.push({"item_id": itemId, "qty": qty, "shipping_address_id": itemsDataSingleAddress()[itemId], "shipping_method_selected": rateSelected()});
                        }
                    }
                });
            }
        } else { //two address
            if (orderDeliveryType == '0') { // delivery
                console.log('two address delivery');
                $.each(shippingMethodSelectList, function( itemId, rateSelected ) {
                    if (typeof rateSelected() === 'undefined') {
                        items = [];
                        return false;
                    } else {
                        if($.inArray(itemId.toString(), currentItemsListId) === -1) {
                            return true;
                        }
                        let currentItem = currentItemsData.getCurrentItemsData(itemId),
                            qty = itemsDataList[itemId]().qty;
                        if (typeof currentItem !== "undefined") {
                            if (parseInt(currentItem().qty) != parseInt(itemsDataList[itemId]().qty)) {
                                qty = currentItem().qty;
                            }
                        }
                        items.push({"item_id": itemId, "qty": qty, "shipping_address_id": addressSelectedList[itemId](), "shipping_method_selected": rateSelected()});
                    }
                });
            } else if(orderDeliveryType == '1') { // store pickup
                console.log('two address store pickup');
                $.each(itemsData(), function( itemId, addressId ) {
                    if($.inArray(itemId.toString(), currentItemsListId) === -1) {
                        return true;
                    }
                    let currentItem = currentItemsData.getCurrentItemsData(itemId),
                        qty = itemsDataList[itemId]().qty;
                    if (typeof currentItem !== "undefined") {
                        if (parseInt(currentItem().qty) != parseInt(itemsDataList[itemId]().qty)) {
                            qty = currentItem().qty;
                        }
                    }
                    items.push({"item_id": itemId, "qty": qty, "shipping_address_id": storePickupAddressId, "shipping_method_selected": "store_pickup_store_pickup"});
                    additionalInfo.store_pick_up.store_code = pickup.currentPickupId();
                    additionalInfo.store_pick_up.date = pickup.storePickUpDate();
                    additionalInfo.store_pick_up.time = pickup.storePickUpTime();
                    storePickupItems.push(itemId);
                });
            } else { // both
                console.log('two address both');
                $.each(shippingMethodSelectList, function( itemId, rateSelected ) {
                    if (disableDeliveryList[itemId]()) {
                        if($.inArray(itemId.toString(), currentItemsListId) === -1) {
                            return true;
                        }
                        let currentItem = currentItemsData.getCurrentItemsData(itemId),
                            qty = itemsDataList[itemId]().qty;
                        if (typeof currentItem !== "undefined") {
                            if (parseInt(currentItem().qty) != parseInt(itemsDataList[itemId]().qty)) {
                                qty = currentItem().qty;
                            }
                        }
                        items.push({"item_id": itemId, "qty": qty, "shipping_address_id": storePickupAddressId, "shipping_method_selected": "store_pickup_store_pickup"});
                        additionalInfo.store_pick_up.store_code = pickup.currentPickupId();
                        additionalInfo.store_pick_up.date = pickup.storePickUpDate();
                        additionalInfo.store_pick_up.time = pickup.storePickUpTime();
                        storePickupItems.push(itemId);
                    } else {
                        if (typeof rateSelected() === 'undefined') {
                            items = {};
                            return false;
                        } else {
                            if($.inArray(itemId.toString(), currentItemsListId) === -1) {
                                return true;
                            }
                            let currentItem = currentItemsData.getCurrentItemsData(itemId),
                                qty = itemsDataList[itemId]().qty;
                            if (typeof currentItem !== "undefined") {
                                if (parseInt(currentItem().qty) != parseInt(itemsDataList[itemId]().qty)) {
                                    qty = currentItem().qty;
                                }
                            }
                            items.push({"item_id": itemId, "qty": qty, "shipping_address_id": addressSelectedList[itemId](), "shipping_method_selected": rateSelected()});
                        }
                    }
                });
            }
        }
        return {items: items, additionalInfo: additionalInfo, storePickupItems: storePickupItems};
    };

    // get Shipping method List Action
    mod.getShippingMethodAction = function (data, storePickupItems) {
        if (paymentFail) {
            reset();
            if (orderVirtual) {
                digitalDetail();
            }
            return;
        }
        if (orderVirtual) {
            digitalDetail();
            return;
        }
        if (!updateShortestStore() && Object.keys(storePickupItems).length !== 0) {
            mod.updateSortestStore(data, storePickupItems);
        } else if (!first && Object.keys(storePickupItems).length !== 0) {
            mod.updateStore(data, storePickupItems);
        } else {
            mod.getShippingMethodHandleAction(JSON.stringify(data));
        }
    };

    mod.updateSortestStore = function (data, storePickupItems) {
        console.log('update-shortest-store');
        updateShortestStore(true);
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                var latlng = {lat: Number(position.coords.latitude), lng: Number(position.coords.longitude)};
                mod.searchStoreHandle(data, latlng, storePickupItems, false);
            }, function (error) {
                mod.getShippingMethodHandleAction(JSON.stringify(data));
            });
        } else {
            mod.getShippingMethodHandleAction(JSON.stringify(data));updateCurrentStore
        }
    };

    mod.updateStore = function (data, storePickupItems) {
        var defaultLatlng = window.checkoutConfig.latlng;
        if (typeof findStoreAction.searchStoreAddress() !== 'undefined' && findStoreAction.searchStoreAddress() != '') {
            var geocoder = new google.maps.Geocoder;
            geocoder.geocode( { 'address' : findStoreAction.searchStoreAddress() }, function( results, status ) {
                if (status === 'OK') {
                    var latlng = {lat: results[0].geometry.location.lat(), lng: results[0].geometry.location.lng()};
                    mod.searchStoreHandle(data, latlng, storePickupItems, update)
                } else {
                    alert($t("Geocode was not successful for the following reason: %1").replace('%1', status));
                }
            });
        } else if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                var latlng = {lat: Number(position.coords.latitude), lng: Number(position.coords.longitude)};
                mod.searchStoreHandle(data, latlng, storePickupItems, false, true);
            }, function (error) {
                var latlng = {lat: Number(defaultLatlng.lat), lng: Number(defaultLatlng.lng)};
                mod.searchStoreHandle(data, latlng, storePickupItems, false, true);
            });
        } else {
            var latlng = {lat: Number(defaultLatlng.lat), lng: Number(defaultLatlng.lng)};
            mod.searchStoreHandle(data, latlng, storePickupItems, false, true);
        }
    };

    mod.searchStoreHandle = function (data, latlng, storePickupItems, updateCurrentStore, update) {
        var currentPickupId = pickup.currentPickupId();
        findStoreAction.searchShortestStoreAction(latlng, storePickupItems, updateCurrentStore, update).done(
            function (response) {
                if (currentPickupId == pickup.currentPickupId()) {
                    data.additional_info.store_pick_up.store_code = currentPickupId;
                    mod.getShippingMethodHandleAction(JSON.stringify(data));
                }
            }
        ).fail(
            function (response) {
                mod.getShippingMethodHandleAction(JSON.stringify(data));
            }
        );
    };

    mod.getShippingMethodHandleAction = function (data) {
        first = false;
        singleDeliveryMethodError(false);
        singleDeliveryMethodValid.removeAll();
        singleDeliveryMethodValid.push('transshipping_transshipping1');
        singleDeliveryMethodValid.push('transshipping_transshipping2');
        singleDeliveryMethodValid.push('transshipping_transshipping3');
        singleDeliveryMethodValid.push('transshipping_transshipping4');
        $.each(itemsData(), function( itemId, addressId ) {
            shippingMethodListValid[itemId].removeAll();
            shippingMethodListValid[itemId].push('transshipping_transshipping1');
            shippingMethodListValid[itemId].push('transshipping_transshipping2');
            shippingMethodListValid[itemId].push('transshipping_transshipping3');
            shippingMethodListValid[itemId].push('transshipping_transshipping4');
            deliveryMethodListError[itemId](false);
        });
        processing = true;
        getShippingMethod(data).done(
            function (response) {
                if (response.reload) {
                    window.location.href = urlBuilder.build("transcheckout");
                    return;
                }
                if (updateStatus.getOrderSelectAddressList()().length && setShippingType.getValue()() == 0) {
                    var currentItems = '';
                    $.each(response.items_valid_method, function (index, data) {
                        if (currentItems != '') {
                            currentItems += ',';
                        }
                        currentItems += data.item_id;
                    });
                    currentItemsData.setCurrentItemsListId(currentItems);
                }
                var updateShippingMethod = false;
                globalVar.disableGoPaymentButton(true);
                if (updateStatus.getOrderSelectAddressList()().length == 1) { // single address
                    if (setShippingType.getValue()() == '2' || (setShippingType.getValue()() == 0 && response.show_each_items)) { // both
                        $.each(response.items_valid_method, function (index, data) {
                            var itemId = data.item_id;
                            if (shippingMethodSelectList[itemId]() == 'store_pickup_store_pickup') {
                                return true;
                            }
                            shippingMethodListValid[itemId].removeAll();
                            var firsValidShippingMethod = notAvailableMethod,
                                i = 0;
                            $.each(data.valid_method, function (key, method) {
                                i++;
                                if (i == 1) {
                                    firsValidShippingMethod = method.method_code;
                                }
                                shippingMethodListValid[itemId].push(method.method_code);
                            });
                            if (setShippingType.getValue()() == 0 && response.show_each_items) {
                                if (shippingMethodListValid[itemId].indexOf(selectSingleShippingMethod()) !== -1) {
                                    processing = true;
                                    shippingMethodSelectList[itemId](selectSingleShippingMethod());
                                    return true;
                                }
                            }
                            if (shippingMethodListValid[itemId].indexOf(shippingMethodSelectList[itemId]()) === -1) {
                                processing = true;
                                deliveryMethodListError[itemId](true);
                                shippingMethodSelectList[itemId](firsValidShippingMethod);
                            }
                        });
                    } else if (setShippingType.getValue()() == '0') { // delivery
                        singleDeliveryMethodValid.removeAll();
                        var firsValidShippingMethod = notAvailableMethod,
                            i = 0;
                        $.each(response.items_valid_method, function (index, data) {
                            $.each(data.valid_method, function (key, method) {
                                i++;
                                if (i == 1) {
                                    firsValidShippingMethod = method.method_code;
                                }
                                if (singleDeliveryMethodValid.indexOf(method.method_code) === -1) {
                                    singleDeliveryMethodValid.push(method.method_code);
                                }
                            });
                            if (singleDeliveryMethodValid.indexOf(selectSingleShippingMethod()) === -1) {
                                processing = false;
                                singleDeliveryMethodError(true);
                                selectSingleShippingMethod(firsValidShippingMethod);
                            }
                            return false;
                        });
                    }
                } else { // multiple address
                    $.each(response.items_valid_method, function (index, data) {
                        var itemId = data.item_id;
                        if (shippingMethodSelectList[itemId]() == 'store_pickup_store_pickup') {
                            return true;
                        }
                        shippingMethodListValid[itemId].removeAll();
                        var firsValidShippingMethod = notAvailableMethod,
                            i = 0;
                        $.each(data.valid_method, function (key, method) {
                            i++;
                            if (i == 1) {
                                firsValidShippingMethod = method.method_code;
                            }
                            shippingMethodListValid[itemId].push(method.method_code);
                        });
                        if (shippingMethodListValid[itemId].indexOf(shippingMethodSelectList[itemId]()) === -1) {
                            processing = true;
                            deliveryMethodListError[itemId](true);
                            shippingMethodSelectList[itemId](firsValidShippingMethod);
                            updateShippingMethod = true;
                        }
                    });
                }
                if (!response.error) {
                    globalVar.disableGoPaymentButton(false);
                }
                if (response.stock_message != '') {
                    $('body').append("<p id='stock-message'>" + response.stock_message + "</p>");
                    setTimeout(function()
                    {
                        $('#stock-message').remove();
                    }, 6000);
                }
                processing = false;
                firstOrderSelectAddressList = false;
                var split = response.show_each_items,
                    showOrderSummary = response.is_split_order;
                setShippingRates.refreshTotal().done(function (res) {
                    globalVar.splitOrder(split);
                    globalVar.showOrderSummary(showOrderSummary);
                    if (updateShippingMethod) {
                        mod.getShippingMethod();
                    }
                }).error(function (res) {
                    globalVar.splitOrder(split);
                    globalVar.showOrderSummary(showOrderSummary);
                    if (updateShippingMethod) {
                        mod.getShippingMethod();
                    }
                }).always(function () {
                    globalVar.splitOrder(split);
                    globalVar.showOrderSummary(showOrderSummary);
                    if (updateShippingMethod) {
                        mod.getShippingMethod();
                    }
                });
            }
        ).fail(
            function (response) {
                processing = false;
                globalVar.disableGoPaymentButton(true);
            }
        );
    };

    mod.getDateTime = function () {
        var date = new Date(),
            hour = (date.getHours().toString().length === 1) ? '0' + date.getHours().toString() : date.getHours().toString(),
            minutes = (date.getMinutes().toString().length === 1) ? '0' + date.getMinutes().toString() : date.getMinutes().toString(),
            seconds = (date.getSeconds().toString().length === 1) ? '0' + date.getSeconds().toString() : date.getSeconds().toString(),
            time = hour + ':' + minutes + ':' + seconds;
        var orderSelectAddressList = updateStatus.getOrderSelectAddressList()(),
            orderDeliveryType = setShippingType.getValue()(),
            addressListDateTime = [];
        if (orderSelectAddressList.length == 1 && orderDeliveryType == '0') {
            if (singleDateTime.singleScheduleDate() != '') {
                addressListDateTime.push({'address': orderSelectAddressList[0], 'date': singleDateTime.singleScheduleDate() + ' ' + time, 'time': singleDateTime.singleScheduleTime()});
            }
        } else {
            $.each(addressListDate, function (addressId, date) {
                if (date() && date() !='') {
                    addressListDateTime.push({'address': addressId, 'date': date() + ' ' + time, 'time': addressListTime[addressId]()});
                }
            });
        }
        return {
            'store_date_time': {'date': (pickup.storePickUpDate() == '') ? '': pickup.storePickUpDate() + ' ' + time, 'time': pickup.storePickUpTime()},
            'delivery_date_time': addressListDateTime,
            'is_split_order': globalVar.showOrderSummary()
        };
    };

    return mod;
});
