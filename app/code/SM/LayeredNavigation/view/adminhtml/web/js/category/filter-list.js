/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_LayeredNavigation
 *
 * Date: April, 27 2020
 * Time: 10:34 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

define([
    'jquery',
    'mage/template',
    'uiRegistry',
    'jquery/ui',
    'prototype',
    'SM_LayeredNavigation/js/category/add-attribute-filter'
], function ($, mageTemplate, registry) {
    'use strict';

    $.widget('mage.SMLayer', {
        options       : {
            'gridSelector'         : '[data-grid-id=catalog_category_filter_list]',
            'addAttributeContainer': '[data-role=catalog_category_add_attribute_filter_content]',
            'addAttributesUrl'     : null,
            'currentCategoryId'    : null,
            'fieldName'            : null
        },
        sourcePosition: null,
        sourceIndex   : null,
        currentView   : null,
        inputField    : null,

        /**
         * @private
         */
        _create: function () {
            this.gridView = $(this.options.gridSelector);
            this.inputField = $('#' + this.options.fieldName);

            this.setupGridView();
            this.initGridEventHandlers();

            $(this.options.addAttributeContainer).addCategoryFilterList({
                dialogUrl   : this.options.addAttributesUrl,
                dialogButton: '[data-ui-id=category-form-assign-attribute-add-attribute-button]',
                dialogSave  : $.proxy(this._dialogSave, this),
                inputField  : this.inputField
            });

            // Only updates when Ajax loads another page
            this.gridView.on(
                'contentUpdated',
                function () {
                    this.setupGridView();
                }.bind(this)
            );
        },

        /**
         * @returns void
         */
        initGridEventHandlers: function () {
            this.gridView.on(
                'focus',
                'input[name=position]',
                this.positionFieldFocused.bindAsEventListener(this)
            ).on(
                'change',
                'input[name=position]',
                this.positionFieldChanged.bindAsEventListener(this)
            ).keypress(function (event) {
                if (event.which === Event.KEY_RETURN) {
                    $(event.target).trigger('blur');
                }
            });

            this.gridView.on('click', 'a[name=unassign]', function (e) {
                this.removeRow(e);
            }.bind(this));
            this.gridView.on('click', '.move-top', this.moveToTop.bindAsEventListener(this));
            this.gridView.on('click', '.move-bottom', this.moveToBottom.bindAsEventListener(this));
        },

        /**
         * @param {Event} event
         * @param {Array} selected
         * @param {mage.addCategoryFilterList} modal
         * @private
         */
        _dialogSave: function (event, selected, modal) {
            let selectedSortData,
                sorted,
                finalData;

            selectedSortData = this.sortSelectedProducts(selected);
            sorted = this.getSortedPositionsFromData(selectedSortData);

            finalData = $H();
            sorted.each(function (item, idx) {
                finalData.set(item.key, String(idx));
            });

            this.inputField.val(Object.toJSON(finalData));
            this.reloadViews();
            modal.closeDialog();
        },

        /**
         * @returns void
         */
        reloadViews: function () {
            let view = this._getGridViewComponent();

            view.reloadParams = {selected: JSON.parse(this.inputField.val())};
            view.reload();
        },

        /**
         * @returns {*}
         * @private
         */
        _getGridViewComponent: function () {
            let k = $(this.options.gridSelector).attr('id') + 'JsObject';

            return window[k];
        },

        /**
         * @returns void
         */
        setupGridView: function () {
            let sortableParent = this.gridView.find('table > tbody'),
                data;

            sortableParent.sortable({
                distance            : 8,
                tolerance           : 'pointer',
                cancel              : 'input, button',
                forcePlaceholderSize: true,
                axis                : 'y',
                update              : this.sortableDidUpdate.bind(this),
                start               : this.sortableStartUpdate.bind(this)
            });

            data = {};
            sortableParent.data('sortable').items.each(function (instance) {
                let key = $(instance.item).find('input[name=entity_id]').val();

                data[key] = $(instance.item);
            });
            sortableParent.data('item_id_mapper', data);
        },

        /**
         * @param {Array} selectedIds
         * @returns {Hash}
         */
        sortSelectedProducts: function (selectedIds) {
            let sortData = this.getSortData(),
                result   = $H(),
                offset   = 0;

            for (let i in sortData._object) {
                if (offset < sortData._object[i]) {
                    offset = sortData._object[i];
                }
            }

            $(selectedIds).each(function (idx, entityId) {
                if (sortData.get(entityId) === undefined) {
                    result.set(entityId, ++offset);
                } else {
                    result.set(entityId, sortData.get(entityId));
                }
            });

            return result;
        },

        /**
         * @param {Array} sortData
         * @returns {Array}
         */
        getSortedPositionsFromData: function (sortData) {
            let sortedArr = [];

            sortData.each(Array.prototype.push.bindAsEventListener(sortedArr));
            sortedArr.sort(this.sortArrayAsc.bind(this, sortData));

            return sortedArr;
        },

        /**
         * @returns {Number}
         */
        getTotalItem: function () {
            return this.gridView.find('table > tbody > tr').length;
        },

        /**
         * @returns {Number}
         */
        getTotalIndex: function () {
            return this.getTotalItem() - 1;
        },

        /**
         * Used to re-populate text inputs and move items in non-active view
         * triggered by {sortable}
         *
         * @param {Event} event
         * @param {Object} ui
         */
        sortableDidUpdate: function (event, ui) {
            let to,
                from,
                otherViews;

            to = ui.item.index();
            from = ui.item.data('originIndex');

            this.populateFromIdx(ui.item.parents('.ui-sortable').find('> *'));

            otherViews = this.element.find('.ui-sortable').not(ui.item.parent());
            otherViews.each(function (idx, view) {
                this.moveItemInView($(view), from, to);
            }.bind(this));

            this.sortDataObject({
                target: ui.item.parents('.ui-sortable')
            });
        },

        /**
         * Generic helper to move items in DOM and repopulate position indexes
         *
         * @param {Object} view
         * @param {int} from
         * @param {int} to
         */
        moveItemInView: function (view, from, to) {
            let items = view.find('>*');

            if (to > from) {
                $(items.get(from)).insertAfter($(items.get(to)));
            } else {
                $(items.get(from)).insertBefore($(items.get(to)));
            }
            items.removeClass('selected');
            this.populateFromIdx(items);
        },

        /**
         * Store the original index
         *
         * @param {Event} event
         * @param {Object} ui
         */
        sortableStartUpdate: function (event, ui) {
            ui.item.data('originIndex', ui.item.index());
        },

        /**
         * UI trigger for attribute remove
         *
         * @param {Event} event
         */
        removeRow: function (event) {
            let row,
                data = this.getSortData();

            event.preventDefault();
            row = $(event.currentTarget).parents('li,tr');
            data.unset(row.find('[name=entity_id]').val());
            this.inputField.val(Object.toJSON(data));
            this.updateRegistry();
            row.remove();

        },

        /**
         * Triggered by clicking on move to top button
         *
         * @param {Event} event
         */
        moveToTop: function (event) {
            let input;

            event.preventDefault();

            input = $(event.currentTarget).next('input[name=position]');
            this.moveToPosition(input, 0);
        },

        /**
         * Triggered by clicking on move to bottom button
         *
         * @param {Event} event
         */
        moveToBottom: function (event) {
            let input;

            event.preventDefault();
            input = $(event.currentTarget).prev('input[name=position]');
            this.moveToPosition(input, this.getTotalIndex());
        },

        /**
         * @param {Object} input
         * @param {int} targetPosition
         */
        moveToPosition: function (input, targetPosition) {
            this.positionFieldFocused({
                'currentTarget': input
            });

            input.val(targetPosition);
            this.changePosition(input);
        },

        /**
         * Triggered by 'onchange' and keypress
         *
         * @param {Event} event
         */
        positionFieldChanged: function (event) {
            let input = $(event.currentTarget);

            if (input.val() !== parseInt(input.val(), 10).toString()) {
                input.val(this.sourcePosition);

                return;
            }
            this.changePosition(input);
        },

        /**
         * Do all the necessary calls to re-position an element
         *
         * @param {Object} input
         */
        changePosition: function (input) {
            let destinationPosition = parseInt(input.val(), 10),
                destinationIndex    = destinationPosition,
                data,
                sorted,
                result,
                movedItem;

            if (destinationPosition > this.getTotalIndex()) {
                input.val(this.getTotalIndex());
                this.changePosition(input);
                this.updateRegistry();

                return;
            }

            // Moving within current page
            if (this.isValidMove(this.sourcePosition, destinationPosition)) {
                // Move on all views
                this.element.find('.ui-sortable').each(function (idx, item) {
                    this.moveItemInView($(item), this.sourceIndex, destinationIndex);
                }.bind(this));

                this.sortDataObject({
                    target: input.parents('.ui-sortable')
                });

                this.updateRegistry();

                return;
            }

            // Moving off the current page
            if (
                this.isValidPosition(this.sourcePosition) &&
                destinationPosition >= 0 &&
                this.sourcePosition !== destinationPosition
            ) {
                data = this.getSortData();
                sorted = this.getSortedPositionsFromData(data);
                result = [];

                movedItem = sorted[this.sourcePosition];
                movedItem.value = String(destinationPosition);

                sorted.each(function (item, idx) {
                    if (idx !== this.sourcePosition && idx !== destinationPosition) {
                        result.push(item);
                    }

                    if (idx === destinationPosition) {
                        if (destinationPosition > this.sourcePosition) {
                            result.push(item);
                            result.push(movedItem);
                        } else {
                            result.push(movedItem);
                            result.push(item);
                        }
                    }
                }.bind(this));

                result.each(function (item, idx) {
                    data.set(item.key, String(idx));
                });

                this.inputField.val(Object.toJSON(data));
                this.reloadViews();
                this.updateRegistry();

                return;
            }

        },

        /**
         * @param {Array} items
         */
        populateFromIdx: function (items) {
            let startIdx = 0;

            items.find('input[name=position][type=text]').each(function (idx, item) {
                $(item).val(startIdx + idx);
            });
        },

        /**
         * @param {int} src
         * @param {int} dst
         * @returns {Boolean}
         */
        isValidMove: function (src, dst) {
            return this.isValidPosition(src) && this.isValidPosition(dst) && src !== dst;
        },

        /**
         * @param {int} pos
         * @returns {Boolean}
         */
        isValidPosition: function (pos) {
            let maxPos = this.getTotalItem(),
                minPos = 0;

            return pos !== null && pos >= minPos && pos < maxPos;
        },

        /**
         * Stores position for later use by this.positionFieldChanged
         *
         * @param {Event} event
         */
        positionFieldFocused: function (event) {
            let idx = parseInt($(event.currentTarget).parents('tr,li').index(), 10);

            if (!this.isValidPosition(idx)) {
                this.sourcePosition = null;
                this.sourceIndex = null;
            } else {
                this.sourcePosition = idx;
                this.sourceIndex = idx;
            }
        },

        /**
         * @param {Object} sortData
         * @param {Object} a
         * @param {Object} b
         * @returns {Number}
         */
        sortArrayAsc: function (sortData, a, b) {
            let keyA = sortData.get(a.key),
                keyB = sortData.get(b.key),
                diff = parseFloat(a.value) - parseFloat(b.value);

            if (diff !== 0) {
                return diff;
            }

            if (keyA === undefined && keyB !== undefined) {
                return -1;
            }

            if (keyA !== undefined && keyB === undefined) {
                return 1;
            }

            return 0;
        },

        /**
         * @returns {Hash}
         */
        getSortData: function () {
            return $H(JSON.parse(this.inputField.val()));
        },

        /**
         * Re-sort the actual positions array which will be sent to the server
         *
         * @param {Event} event
         */
        sortDataObject: function (event) {
            // Data format: {entity_id => sort index, ... }
            let data     = this.getSortData(),
                startIdx = 0;

            // Overwrite positions with items from UI
            $(event.target).find('> *').find('[name=entity_id]').each(function (idx, item) {
                data.set($(item).val(), startIdx);
                startIdx++;
            });

            this.inputField.val(Object.toJSON(data));
        },

        updateRegistry: function () {
            let data     = JSON.parse(this.inputField.val()),
                selected = [];

            if (Object.keys(data).length) {
                for (let key in data) {
                    selected.push(key);
                }
            }

            registry.set('filter_list_position_cache_valid', true);
            registry.set('filter_list_selected_cache', JSON.stringify(selected));
        }
    });

    return $.mage.SMLayer;
});
