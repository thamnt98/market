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

        var poupPDPDelivery = $('#pdp-delivery-connect'),
            triggerEl = $('#pdp-delivery-info');
        var options = {
            type: 'popup',
            responsive: true,
            innerScroll: false,
            buttons: [],
            modalClass:'block-container-delivery',
            clickableOverlay: false
        };
        var openModalPDPDelivery = modal(options, poupPDPDelivery);

        triggerEl.on('click', function(){
            poupPDPDelivery.modal('openModal');
            poupPDPDelivery.show();
        });
    }
);

