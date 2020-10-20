/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'underscore',
    'Magento_Ui/js/grid/columns/column',
    'Magento_Catalog/js/product/list/column-status-validator',
    "jquery",
    'mage/translate'
], function (_, Element, columnStatusValidator, $, $t) {
    'use strict';

    return Element.extend({
        defaults: {
            bodyTmpl: 'Magento_Catalog/product/list/columns/image',
            imageCode: 'default',
            image: {}
        },
        /** @inheritdoc */
        initialize: function () {
            this._super();
            this.getFlashSaleLabel();
        },

        /**
         * Find image by code in scope of images
         *
         * @param {Object} images
         * @returns {*|T}
         */
        getImage: function (images) {
            return _.filter(images, function (image) {
                return this.imageCode === image.code;
            }, this).pop();
        },

        /**
         * Get image path.
         *
         * @param {Object} row
         * @return {String}
         */
        getImageUrl: function (row) {
            return this.getImage(row.images).url;
        },

        /**
         * Get image box width.
         *
         * @param {Object} row
         * @return {Number}
         */
        getWidth: function (row) {
            return this.getImage(row.images).width;
        },

        /**
         * Get image box height.
         *
         * @param {Object} row
         * @return {Number}
         */
        getHeight: function (row) {
            return this.getImage(row.images).height;
        },

        /**
         * Get resized image width.
         *
         * @param {Object} row
         * @return {Number}
         */
        getResizedImageWidth: function (row) {
            return this.getImage(row.images)['resized_width'];
        },

        /**
         * Get resized image height.
         *
         * @param {Object} row
         * @return {Number}
         */
        getResizedImageHeight: function (row) {
            return this.getImage(row.images)['resized_height'];
        },

        /**
         * Get image alt text.
         *
         * @param {Object} row
         * @return {String}
         */
        getLabel: function (row) {
            if (!this.imageExists(row)) {
                return this._super();
            }

            return this.getImage(row.images).label;
        },

        /**
         * Check if image exist.
         *
         * @param {Object} row
         * @return {Boolean}
         */
        imageExists: function (row) {
            return this.getImage(row.images) !== 'undefined';
        },

        /**
         * Check if component must be shown.
         *
         * @return {Boolean}
         */
        isAllowed: function () {
            return columnStatusValidator.isValid(this.source(), 'image', 'show_attributes');
        },

        getFlashSaleLabel: function () {
            $.ajax({
                url: "/flashsalehistory/index/index",
                type: 'POST',
                dataType: 'json',
                complete: function (response) {
                    if(response.responseJSON && typeof response.responseJSON.available_qty !== 'undefined') {
                        $(".product-stock").css("display", "none");
                        var availableQty = response.responseJSON.available_qty;
                        for (var i = 0; i < availableQty.length; i++) {
                            if (availableQty[i]['saleQty'] >= 0) {
                                $(".popular-sale-count-" + availableQty[i]['productId']).text(availableQty[i]['saleQty'] + ' ' + $t('left'));
                                $(".popular-sale-count-" + availableQty[i]['productId']).css("display", "block");
                            }
                        }
                    }
                },
                error: function (xhr, status, errorThrown) {

                }
            });
        }
    });
});
