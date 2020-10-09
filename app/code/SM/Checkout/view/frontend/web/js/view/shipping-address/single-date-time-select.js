define(
    [
        'ko'
    ],
    function (ko) {
        'use strict';
        let mod = {};

        mod.timeSlotListStatus = ko.observable(false);
        mod.singleScheduleDate = ko.observable('');
        mod.singleScheduleTime = ko.observable('');
        mod.timeSlot = ko.observableArray([]);
        return mod;
    }
);
