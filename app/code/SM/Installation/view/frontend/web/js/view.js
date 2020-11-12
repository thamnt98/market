/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Installation
 *
 * Date: May, 15 2020
 * Time: 3:01 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */
define([
    'uiComponent',
    'jquery',
    'mage/translate',
], function (Component, $, $t,) {
    'use strict';

    return Component.extend({
        defaults: {
            template  : 'SM_Installation/view',
            data      : {},
            tooltip   : "",
            tooltipUrl: "",
            showNote  : false
        },

        initialize: function () {
            this._super();
            let checkoutConfig = window.checkoutConfig;

            if (checkoutConfig && checkoutConfig.installationConfig) {
                this.tooltip = checkoutConfig.installationConfig.tooltip;
                this.tooltipUrl = checkoutConfig.installationConfig.tooltipUrl;
                this.showNote = checkoutConfig.installationConfig.showNote;
            }
        },

        getDataByItem: function (itemId) {
            let result = {},
                items  = window.checkoutConfig.quoteItemData;
            $.each(items, function (key, data) {
                if (data.item_id == itemId) {
                    result.data = data.installation_service;

                    return false;
                }
            });

            return result;
        },

        /**
         * Is show installation block.
         *
         * @returns {boolean}
         */
        isShow: function (itemId) {
            let data = itemId ? this.getDataByItem(itemId).data : this.data;

            if (!data || !data['is_installation']) {
                return false;
            }

            return parseInt(data['is_installation']) === 1;
        },

        /**
         * Is show installation note.
         *
         * @returns {boolean}
         */
        isShowNote: function (itemId) {
            return this.getNote(itemId) && this.showNote;
        },

        /**
         * Get installation title.
         *
         * @returns {*}
         */
        getTitle: function (itemId) {
            return $t('Free installation included');
        },

        getNote: function (itemId) {
            return itemId ? this.getDataByItem(itemId).data['installation_note'] : this.data['installation_note'] ;
        }
    });
});

