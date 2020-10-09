/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define(
    [
        'jquery',
        'SM_Coachmarks/js/view/maincoachmarks-flag'
    ], function ($, maincoachmarks) {
        'use strict';

        $.widget(
            'sm.co', {
                _create: function () {
                    maincoachmarks.coachMarks(true);
                }
            }
        );
        return $.sm.co;
    });
