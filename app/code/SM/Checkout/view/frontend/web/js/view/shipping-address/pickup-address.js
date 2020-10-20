/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'ko',
    'uiComponent',
    'jquery',
    'Magento_Ui/js/modal/modal',
    'SM_Checkout/js/view/shipping-address/current-pickup',
    'SM_Checkout/js/view/cart-items/set-shipping-type',
    'SM_Checkout/js/action/find-store',
    'mage/calendar'
], function (ko, Component, $, modal, pickup, setShippingType, findStoreAction) {
    'use strict';

    var dateTimeConfig = window.checkoutConfig.dateTime;

    return Component.extend({
        currentStoreName: pickup.currentStoreName,
        currentStoreAddress: pickup.currentStoreAddress,
        currentPickupId: pickup.currentPickupId,
        storePickUpDate: pickup.storePickUpDate,
        storePickUpTime: pickup.storePickUpTime,
        isFullFill: findStoreAction.storeFullFill,
        timeSlot: ko.observableArray([]),
        timeSlotListStatus: ko.observable(false),

        /** @inheritdoc */
        initialize: function () {
            this._super();
            this.renderTime();
            return this;
        },

        renderDate: function(id)
        {
            var self = this,
                startDate = new Date(),
                endDate = new Date(),
                startHour = startDate.getHours() + dateTimeConfig.today_time_start,
                endHour = dateTimeConfig.time_end;
            endDate.setDate(endDate.getDate() + Number(dateTimeConfig.date_limit));
            if ((startHour > endHour) || (startHour == endHour && startDate.getMinutes() > 0)) {
                //set default is next day
                startDate.setDate(startDate.getDate() + 1);
            }

            $('#' + id).calendar({
                dateFormat: "dd MMM yy",
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                currentText: 'Go Today',
                closeText: 'Close',
                hideIfNoPrevNext: true,
                minDate: startDate,
                maxDate: endDate
            }).datepicker("setDate", startDate).trigger('change');
        },

        renderTime: function()
        {
            var self = this,
                startDate = new Date(),
                startHour = startDate.getHours() + dateTimeConfig.today_time_start,
                endHour = dateTimeConfig.time_end;
            if ((startHour > endHour) || (startHour == endHour && startDate.getMinutes() > 0)) {
                startHour = dateTimeConfig.nextday_time_start;
            }
            this.updateTimeSlotList(startHour, endHour);
            this.storePickUpDate.subscribe(function (value) {
                self.updateTime(value);
            });
        },

        updateTime: function(date)
        {
            var selectDate = new Date(date),
                currentDate = new Date(),
                startDate = new Date(),
                startHour = startDate.getHours() + dateTimeConfig.today_time_start,
                endHour = dateTimeConfig.time_end;
            currentDate.setHours(0);
            currentDate.setMinutes(0);
            currentDate.setSeconds(0);
            if (selectDate > currentDate) {
                startHour = dateTimeConfig.nextday_time_start;
            } else {
                if ((startHour > endHour)
                    || (startHour == endHour && startDate.getMinutes() > 0)
                    || (startHour < dateTimeConfig.nextday_time_star)) {
                    startHour = dateTimeConfig.nextday_time_start;
                }
            }
            this.updateTimeSlotList(startHour, endHour);
        },

        updateTimeSlotList: function (startHour, endHour) {
            var self = this,
                i;
            self.timeSlot.removeAll();
            for (i = startHour; i < endHour; i++) {
                var slot = '';
                if (i < 12) {
                    slot += i + ':00 AM - ' + (i + 1);
                    if ((i + 1) < 12) {
                        slot += ':00 AM';
                    } else {
                        slot += ':00 PM';
                    }
                } else if (i > 12) {
                    slot += (i - 12) + ':00 PM - ' + (i - 11) + ':00 PM';
                } else {
                    slot += i + ':00 PM - 1:00 PM';
                }
                self.timeSlot.push(slot);
            }
            if (self.timeSlot.indexOf(self.storePickUpTime()) === -1) {
                self.storePickUpTime(self.timeSlot()[0]);
            }
        },

        selectTimeSlot: function (value) {
            this.storePickUpTime(value);
            this.timeSlotListStatus(false);
        },

        preSelectTimeSlot: function () {
            this.storePickUpTime(this.timeSlot()[0]);
        },

        openTimeSlotList: function () {
            this.timeSlotListStatus(true);
        },

        closeTimeSlotList: function () {
            this.timeSlotListStatus(false);
        },

        timeSlotSelected: function (value) {
            return (value == this.storePickUpTime()) ? true : false;
        },

        hasAddress: function(){
            return pickup.hasCurrentStore();
        },

        addStore: function(){
            $('#store-pickup-list-popup').modal('openModal');
        },

        getToday: function() {
            var currentDate = new Date();
            var day = currentDate.getDate();
            var month = currentDate.getMonth() + 1;
            var year = currentDate.getFullYear();
            var today = day + "/" + month + "/" + year;
            return today;
        },

        isShow: function () {
            if (setShippingType.getValue()() == '0') {
                return false;
            }
            return true;
        }
    });
});
