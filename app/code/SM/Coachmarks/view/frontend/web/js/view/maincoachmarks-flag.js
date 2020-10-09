/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'ko'
], function (ko) {
    'use strict';

    let coachMarks = ko.observable(false);

    return {
        coachMarks: coachMarks
    };
});

