/**
 * @category SM
 * @package SM_Brand
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author      Chinhvd <chinhvd@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */
/*global setLocation:true*/
/*@api*/
define([
    'jquery',
    'Magento_Ui/js/core/app',
    'uiRegistry',
    'Magento_Ui/js/modal/modal',
    'mage/translate'
], function ($, bootstrap, registry) {
    'use strict';

    $.widget('mage.addCategoryBrandList', {
        options          : {
            dialogUrl   : null,
            dialogButton: null,
            inputField  : null
        },
        isGridLoaded     : false,
        registry         : null,
        massAssign       : 'brand_list_mass_assign',
        saveButtonName   : 'add_brand_save_button',

        /**
         * @private
         */
        _create: function () {
            this.registry = registry;
            this.bootstrap = bootstrap;

            this.element.modal(this._getConfig());
            this._bind();
        },

        /**
         * @private
         */
        _bind: function () {
            this._on({
                requestUpdate: this.updateGrid,
                requestReload: this.reloadGrid
            });

            $(document).on('click', this.options.dialogButton, $.proxy(this.openDialog, this));
        },

        /**
         * Open the dialog
         */
        openDialog: function () {
            this.element.modal('openModal');
        },

        /**
         * Close the dialog
         */
        closeDialog: function () {
            this.element.modal('closeModal');
        },

        /**
         * Update the grid with changes
         */
        updateGrid: function () {
            this.registry.get('sm_brand_add_brand_listing.sm_brand_add_brand_listing_data_source').reload({
                refresh: true
            });
        },

        /**
         * Toggle state of submit button.
         *
         * @param {Boolean} disabled
         */
        toggleSaveButton: function (disabled) {
            $(document).find('button[name="' + this.saveButtonName + '"]')
                .attr('disabled', disabled)
                .text(disabled ? $.mage.__('Wait loading...') : $.mage.__('Save and Close'));
        },

        /**
         * Grid load handler.
         */
        onGridLoad: function () {
            let self = this;

            this.isGridLoaded = true;
            this.registry.get(
                'sm_brand_add_brand_listing.sm_brand_add_brand_listing_data_source',
                function (listingDataSource) {
                    listingDataSource.on('reload', function () {
                        self.toggleSaveButton(true);
                    });
                    listingDataSource.on('reloaded', function () {
                        self.toggleSaveButton(false);
                    });
                }
            );
        },

        /**
         * Perform a full grid update, caches will be invalidated
         * changes to grid will be lost.
         *
         * @param {Event} event
         * @param {Array} action
         */
        reloadGrid: function (event, action) {
            let selected = JSON.parse(this.options.inputField.val()),
                indexOfCacheResult,
                i;

            if (action.action === 'remove') {
                for (i = 0; i < action.ids.length; i++) {
                    indexOfCacheResult = selected.indexOf(action.ids[i]);
                    indexOfCacheResult >= 0 && selected.splice(indexOfCacheResult, 1);
                }

                this.options.inputField.val(JSON.stringify(selected));
                registry.set(this.massAssign, true);
                this.updateGrid();
            } else if (action.action === 'assign') {
                for (i = 0; i < action.ids.length; i++) {
                    selected.indexOf(action.ids[i]) !== -1 || selected.push(action.ids[i]);
                }

                this.options.inputField.val(JSON.stringify(selected));
                registry.set(this.massAssign, true);
                this.updateGrid();
            } else {
                this.updateGrid();
            }
        },

        /**
         * @returns {{type: String, title: String, opened: *, buttons: *}}
         * @private
         */
        _getConfig: function () {
            let data = JSON.parse(this.options.inputField.val()),
                selected = [];

            if (Object.keys(data).length) {
                for (let key in data) {
                    selected.push(key);
                }
            }

            registry.set('brand_list_position_cache_valid', true);
            registry.set('brand_list_selected_cache',  JSON.stringify(selected));

            return {
                title  : $.mage.__('Add Brand(s)'),
                opened : $.proxy(this._opened, this),
                buttons: this._getButtonsConfig()
            };
        },

        /**
         * @private
         */
        _opened: function () {
            if (!this.isGridLoaded) {
                $.ajax({
                    type    : 'GET',
                    url     : this.options.dialogUrl,
                    context : $('body'),
                    dataType: 'json',
                    success : $.proxy(this._ajaxSuccess, this)
                });
            } else {
                this.updateGrid();
            }
        },

        /**
         * @param {String} data
         * @private
         */
        _ajaxSuccess: function (data) {
            this._validateAjax(data);
            this.bootstrap(data);
            this.onGridLoad();
        },

        /**
         * @param {Object} response
         * @private
         */
        _validateAjax: function (response) {
            if (response.ajaxExpired && response.ajaxRedirect) {
                setLocation(response.ajaxRedirect);
            } else if (response.url) {
                setLocation(response.url);
            }
        },

        /**
         * @returns {*[]}
         * @private
         */
        _getButtonsConfig: function () {
            return [{
                text : $.mage.__('Wait loading...'),
                class: '',
                click: $.proxy(this._save, this),
                attr : {
                    name    : this.saveButtonName,
                    disabled: 'disabled'
                }
            }];
        },

        /**
         * @private
         */
        _save: function () {
            let idColumn = this.registry
                .get('sm_brand_add_brand_listing.sm_brand_add_brand_listing.sm_brand_add_brand_listing_columns.ids');

            this._trigger('dialogSave', null, [
                idColumn.selected(),
                this
            ]);
        }
    });

    return $.mage.addCategoryBrandList;
});
