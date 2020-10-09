/**
 * @category Magento
 * @package Magento_Catalog
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author Logan Ta <phuongtt2@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

define(
    [
        'jquery',
        'Magento_Ui/js/modal/modal',
        'mage/translate'
    ],
    function ($, modal) {
        "use strict";

        var reviewForm = $('#write-review-pdp'),
            openReviewForm = $('#open-review-form');

        var options = {
            type: 'popup',
            responsive: true,
            innerScroll: false,
            buttons: [],
            modalClass:'modal-popup-small',
            clickableOverlay: false
        };
        modal(options, reviewForm);

        openReviewForm.on('click', function(){
            reviewForm.modal('openModal');
            reviewForm.show();
        });
    }
);

