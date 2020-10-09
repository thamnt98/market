define([
    'Magento_Ui/js/grid/columns/column',
    'jquery',
    'mage/template',
    'text!SM_StoreLocator/template/grid/cells/opening-hours.html',
    'text!SM_StoreLocator/template/grid/cells/opening-hour-day.html',
    'Magento_Ui/js/modal/modal'
], function (Column, $, mageTemplate, openingHoursPreviewTemplate, openingHourDayPreviewTemplate) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'ui/grid/cells/html',
            fieldClass: {
                'data-grid-html-cell': true
            }
        },
        gethtml: function (row) {
            return row[this.index + '_html'];
        },
        getLabel: function (row) {
            return row[this.index + '_html']
        },
        getTitle: function (row) {
            return row[this.index + '_title']
        },
        getProductData: function (row) {
            return row[this.index]
        },
        preview: function (row) {
            var opening_hours_data = '';
            $.each(this.getProductData(row), function (key, value) {
                if (key) {
                    let opening_hours = 'Not available';

                    if(value)
                        opening_hours = 'Start: ' + value["start"] + ' - From: ' +  value["end"];

                    let attribute = mageTemplate(openingHourDayPreviewTemplate, {
                            weekday: key,
                            opening_hours: opening_hours
                        }
                    );
                    opening_hours_data += attribute;
                }
            });

            var modalHtml = mageTemplate(
                openingHoursPreviewTemplate,
                {
                    html: this.gethtml(row),
                    title: this.getTitle(row),
                    label: this.getLabel(row),
                    opening_hours_data: opening_hours_data,
                }
            );
            var previewPopup = $('<div/>').html(modalHtml);
            previewPopup.modal({
                title: this.getTitle(row),
                innerScroll: true,
                modalClass: '_image-box',
                buttons: []
            }).trigger('openModal');
        },
        getFieldHandler: function (row) {
            return this.preview.bind(this, row);
        }
    });
});
