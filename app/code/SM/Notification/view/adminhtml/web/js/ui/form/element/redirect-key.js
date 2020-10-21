/**
 * SMCommerce
 *
 * @category    SM
 * @package     SM_Notification
 *
 * Date: October, 15 2020
 * Time: 2:17 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */
define([
    'jquery',
    'ko',
    'Magento_Ui/js/form/element/ui-select',
    'uiRegistry'
], function ($, ko, Component, registry) {
    'use strict';

    return Component.extend({
        defaults: {
            allOptions       : null,
            optionCallBackUrl: null,
            resetValue       : false
        },

        initialize: function () {
            this._super();

            let self                  = this,
                redirectTypeComponent = registry.get('view_detail_form.view_detail_form.web.redirect_type');

            if (self.allOptions && typeof self.allOptions === 'string') {
                self.allOptions = JSON.parse(self.allOptions);
            }

            if (redirectTypeComponent) {
                switch (redirectTypeComponent.value()) {
                    case 'order':
                        self.filterOrderOptions();
                        break;
                    case 'product':
                        self.filterProductOptions();
                        break;
                    case 'voucher':
                        self.filterVoucherOptions();
                        break;
                    case 'help':
                        self.filterHelpOptions();
                        break;
                    case 'brand':
                        self.filterBrandOptions();
                        break;
                    case 'campaign':
                        self.filterCampaignOptions();
                        break;
                }
            }
        },

        changeTypeReset: function () {
            this.options([]);
            if (this.resetValue) {
                this.value('');
            } else {
                this.resetValue = true;
            }
        },

        filterOrderOptions: function () {
            this.changeTypeReset();
            if (this.allOptions && this.allOptions['order']) {
                this.options(this.allOptions['order']);
                this.cacheOptions = {
                    plain: this.allOptions['order'],
                    tree : this.allOptions['order']
                };
            }
        },

        filterProductOptions: function () {
            this.changeTypeReset();
            if (this.allOptions && this.allOptions['product']) {
                this.options(this.allOptions['product']);
                this.cacheOptions = {
                    plain: this.allOptions['product'],
                    tree : this.allOptions['product']
                };
            }
        },

        filterHelpOptions: function () {
            this.changeTypeReset();
            if (this.allOptions && this.allOptions['help']) {
                this.options(this.allOptions['help']);
                this.cacheOptions = {
                    plain: this.allOptions['help'],
                    tree : this.allOptions['help']
                };
            }
        },

        filterBrandOptions: function () {
            this.changeTypeReset();
            if (this.allOptions && this.allOptions['brand']) {
                this.options(this.allOptions['brand']);
                this.cacheOptions = {
                    plain: this.allOptions['brand'],
                    tree : this.allOptions['brand']
                };
            }
        },

        filterCampaignOptions: function () {
            this.changeTypeReset();
            if (this.allOptions && this.allOptions['campaign']) {
                this.options(this.allOptions['campaign']);
                this.cacheOptions = {
                    plain: this.allOptions['campaign'],
                    tree : this.allOptions['campaign']
                };
            }
        },

        filterVoucherOptions: function () {
            this.changeTypeReset();
            if (this.allOptions && this.allOptions['voucher']) {
                this.options(this.allOptions['voucher']);
                this.cacheOptions = {
                    plain: this.allOptions['voucher'],
                    tree : this.allOptions['voucher']
                };
            }
        }
    });
});