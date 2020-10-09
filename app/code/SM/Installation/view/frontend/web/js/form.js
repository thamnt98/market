/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Installation
 *
 * Date: April, 21 2020
 * Time: 10:26 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

define([
    'uiComponent',
    'jquery',
    'ko',
    'underscore',
    'mage/translate',
    'Magento_Ui/js/modal/modal',
    'mage/url'
], function (Component, $, ko, _, $t, modal, urlBuilder) {
    'use strict';

    return Component.extend({
        defaults: {
            template       : 'SM_Installation/form',
            itemId         : null,
            productId      : null,
            tooltip        : '',
            tooltipUrl     : '',
            hasRemovePopup : true,
            removePopup    : null,
            usedFieldName  : 'is_installation',
            noteFieldName  : 'installation_note',
            feeFieldName   : 'installation_fee',
            defaultNote    : '',
            notePlaceholder: 'Example: Preferred time is in the afternoon, please ask the security for access, etc.',
            defaultChecked : false,
            checked        : ko.observableArray([]),
            fee            : 0
        },

        initialize: function () {
            this._super();

            let currentChecked = this.checked();

            currentChecked.push({'itemId' : this.itemId, 'value': this.defaultChecked});
            this.checked(currentChecked);

            return this;
        },

        isChecked: function () {
            let self = this,
                result = false;

            _.each(this.checked(), function (data) {
                if (data.itemId === self.itemId) {
                    result = data.value;

                    return false;
                }
            });

            return result;
        },

        updateChecked: function (newData) {
            let self = this,
                current = self.checked();

            _.each(current, function (data, key) {
                if (data.itemId === self.itemId && data.value !== newData) {
                    current[key].value = newData;
                    self.checked(current);

                    return false;
                }
            });
        },

        /**
         * Get Installation Title.
         *
         * @returns {observable|*}
         */
        getTitle: function () {
            if (this.title) {
                return $t(this.title);
            }

            return ko.observable(false);
        },

        /**
         * Event click checkbox
         *
         * @param event
         */
        changeUsed: function (event) {
            let self    = this,
                input   = $('input[name="' + self.usedFieldName + self.itemId + '"]'),
                isCheck = self.isChecked();

            if (isCheck && self.hasRemovePopup) {
                if (!self.removePopup) {
                    self.initRemovePopup();
                }

                self.removePopup.openModal();
            } else {
                self.updateChecked(true);
                $(input).prop('checked', true);
            }
        },

        /**
         * Send Ajax save installation note.
         */
        saveNote: function () {
            if (!this.itemId) {
                return;
            }

            let self   = this,
                params = {isAjax: 1, item_id: self.itemId, product_id: self.productId, action: 'update'};

            params[self.usedFieldName] = self.isChecked() ? 1 : 0;
            params[self.noteFieldName] = $('textarea[name="' + self.noteFieldName + self.itemId + '"]').val();
            params[self.feeFieldName] = self.fee;
            $.ajax({
                type    : 'POST',
                url     : urlBuilder.build('installation/actions/index'),
                data    : params,
                dataType: "json"
            });
        },

        /**
         * Get Tooltip Icon Url
         *
         * @returns {string}
         */
        getTooltipImg: function () {
            return this.tooltipUrl;
        },

        getInstallationFeeText: function () {
            return $t('Add %1 Installation').replace('%1', this.getFeeText());
        },

        /**
         * Convert Fee to Text.
         *
         * @returns {string}
         */
        getFeeText: function () {
            let feeTxt = this.fee + '';

            if (!feeTxt || feeTxt === '0') {
                feeTxt = $t('Free')
            }

            return feeTxt;
        },

        /**
         * Get Placeholder for Note field.
         *
         * @returns {*}
         */
        getNotePlaceholder: function () {
            return $t(this.notePlaceholder);
        },

        /**
         * Send Ajax remove installation.
         */
        removeInstallation: function () {
            let self   = this,
                params = {isAjax: 1, item_id: self.itemId, product_id: self.productId, action: 'remove'};

            $.ajax({
                type    : 'POST',
                url     : urlBuilder.build('installation/actions/index'),
                data    : params,
                dataType: "json"
            });
        },

        /**
         * Init popup remove installation.
         */
        initRemovePopup: function () {
            let self = this;

            if (this.hasRemovePopup) {
                let options = {
                    type       : 'popup',
                    responsive : true,
                    innerScroll: false,
                    modalClass : 'installation-remove-modal',
                    title      : $t('Remove Installation'),
                    buttons    : [{
                        text : $t('Cancel'),
                        click: function () {
                            self.updateChecked(true);
                            $('input[name="' + self.usedFieldName + self.itemId + '"]').prop('checked', true);
                            this.closeModal();
                        }
                    }, {
                        text : $t('Remove'),
                        class: 'primary action',
                        click: function () {
                            $('textarea[name="' + self.noteFieldName + self.itemId + '"]').val('');
                            self.updateChecked(false);
                            $('input[name="' + self.usedFieldName + self.itemId + '"]').prop('checked', false);
                            this.closeModal();
                            self.removeInstallation();
                        }
                    }]
                };

                self.removePopup = modal(options, $('.installation-modal-remove'));
                $('.installation-remove-modal [data-role="closeBtn"]').click(function () {
                    self.updateChecked(true);
                    $('input[name="' + self.usedFieldName + self.itemId + '"]').prop('checked', true);
                });
            }
        }
    });
});
