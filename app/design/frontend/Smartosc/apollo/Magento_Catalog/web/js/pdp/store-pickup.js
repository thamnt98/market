/**
 * @category SM
 * @package Magento_Catalog
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author Logan Ta <phuongtt2@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'uiComponent',
    'mage/translate',
], function ($, modal, Component) {
    'use strict';

    return Component.extend({
        /**
         * init function
         */
        initialize: function (config) {
            var self = this,
                availablePickupEl = $('.available-pickup'),
                hasSourceListAvailable = config.hasSourceListAvailable,
                countListTypeSimple = config.countListTypeSimple,
                countListTypeConfig = config.countListTypeConfig,
                countListTypeBundle = config.countListTypeBundle,
                productType = config.productType;

            //default auto hide pickup info
            availablePickupEl.hide();
            // if has source list available for product
            if (hasSourceListAvailable) {
                self._openStorePickupList(
                    availablePickupEl,
                    countListTypeSimple,
                    countListTypeConfig,
                    countListTypeBundle,
                    productType
                );
            }
        },

        /**
         *  open store pickup list
         */
        _openStorePickupList: function (
            availablePickupEl,
            countListTypeSimple,
            countListTypeConfig,
            countListTypeBundle,
            productType
        ) {
            var self = this,
                poupPDPStorePickup = $('#pdp-pickup-connect'),
                triggerEl = $('.pdp-store-info'),
                options = {
                    type: 'popup',
                    responsive: true,
                    innerScroll: false,
                    buttons: [],
                    modalClass: 'block-container-delivery',
                    clickableOverlay: false
                };
            var openModalPDPPickup = modal(options, poupPDPStorePickup);

            //simple product
            if (parseInt(countListTypeSimple) > 0) {
                availablePickupEl.show();
                //notify number store
                triggerEl.e
                if (countListTypeSimple > 1) {
                    triggerEl.text($.mage.__(' Store info (%1 Stores)').replace('%1', countListTypeSimple));
                } else {
                    triggerEl.text($.mage.__(' Store info (%1 Store)').replace('%1', countListTypeSimple));
                }
            }
            //config product
            if (parseInt(countListTypeConfig) > 0) {
                availablePickupEl.show();
                triggerEl.empty();
                if (countListTypeConfig > 1) {
                    triggerEl.text($.mage.__(' Store info (%1 Stores)').replace('%1', countListTypeConfig));
                } else {
                    triggerEl.text($.mage.__(' Store info (%1 Store)').replace('%1', countListTypeConfig));
                }
            }
            //bundle product
            if (parseInt(countListTypeBundle) > 0) {
                availablePickupEl.show();
                triggerEl.empty();
                if (countListTypeBundle > 1) {
                    triggerEl.text($.mage.__(' Store info (%1 Stores)').replace('%1', countListTypeBundle));
                } else {
                    triggerEl.text($.mage.__(' Store info (%1 Store)').replace('%1', countListTypeBundle));
                }
            }
            if (productType === "grouped") {
                availablePickupEl.show();
            }
            triggerEl.on('click', function () {
                poupPDPStorePickup.modal('openModal');
                poupPDPStorePickup.show();
            });
        },

    })
});

