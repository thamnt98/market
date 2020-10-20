/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'underscore',
    'ko',
    'mage/translate',
    'mageUtils',
    'uiComponent',
    'uiLayout',
    'Magento_Customer/js/model/address-list',
    'SM_Checkout/js/view/cart-items/set-shipping-type',
    'SM_Checkout/js/view/shipping-address/single-date-time-select'
], function (_, ko, $t, utils, Component, layout, addressList, setShippingType, singleDateTime) {
    'use strict';

    var defaultRendererTemplate = {
        parent: '${ $.$data.parentName }',
        name: '${ $.$data.name }',
        component: 'SM_Checkout/js/view/shipping-address/address-renderer/default',
        provider: 'checkoutProvider'
    };

    return Component.extend({
        defaults: {
            template: 'SM_Checkout/shipping-address/list',
            visible: addressList().length > 0,
            rendererTemplates: []
        },

        /** @inheritdoc */
        initialize: function () {
            this._super()
                .initChildren();

            addressList.subscribe(function (changes) {
                    var self = this;

                    changes.forEach(function (change) {
                        if (change.status === 'added') {
                            self.createRendererComponent(change.value, change.index);
                        }
                    });
                },
                this,
                'arrayChange'
            );
            this.renderTimeSlot();
            return this;
        },

        /** @inheritdoc */
        initConfig: function () {
            this._super();
            // the list of child components that are responsible for address rendering
            this.rendererComponents = [];

            return this;
        },

        /** @inheritdoc */
        initChildren: function () {
            _.each(addressList(), this.createRendererComponent, this);

            return this;
        },
        /**
         * Create new component that will render given address in the address list
         *
         * @param {Object} address
         * @param {*} index
         */
        createRendererComponent: function (address, index) {
            var rendererTemplate, templateData, rendererComponent;
            if (index in this.rendererComponents) {
                this.rendererComponents[index].address(address);
            } else {
                // rendererTemplates are provided via layout
                rendererTemplate = defaultRendererTemplate;
                templateData = {
                    parentName: this.name,
                    name: index
                };
                rendererComponent = utils.template(rendererTemplate, templateData);
                utils.extend(rendererComponent, {
                    address: ko.observable(address)
                });
                layout([rendererComponent]);
                this.rendererComponents[index] = rendererComponent;
            }
        },

        isShow: function () {
            if (setShippingType.getValue()() == '1') {
                return false;
            }
            return true;
        },

        isAddressNotComplete: function () {
            return !window.checkoutConfig.address_complete;
        },

        getAddressNotCompleteMessage: function () {
            return $t("Please update your address using the %1 button.").replace("%1", "<span>" + $t("edit") + "</span>");
        },

        renderTimeSlot: function () {
            var startHour = 10,
                endHour = 19,
                i;
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
                singleDateTime.timeSlot.push(slot);
            }
        },

        hasSupportShippingText: function ()
        {
            if (typeof window.checkoutConfig.shipping_support === "undefined") {
                return false
            }
            return true;
        }
    });
});
