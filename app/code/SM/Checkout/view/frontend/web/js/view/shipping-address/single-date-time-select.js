define(
    [
        'ko'
    ],
    function (ko) {
        'use strict';
        let timeSlotListStatus = ko.observable(false),
            singleScheduleDate = ko.observable(''),
            singleScheduleTime = ko.observable(''),
            timeSlot = ko.observableArray([]);
        return {
            timeSlotListStatus: timeSlotListStatus,
            singleScheduleDate: singleScheduleDate,
            singleScheduleTime: singleScheduleTime,
            timeSlot: timeSlot
        };
    }
);
