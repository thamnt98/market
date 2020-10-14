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
    'uiComponent',
    'mage/url',
    'mage/translate',
], function ($, Component, urlBuilder) {
    'use strict';

    return Component.extend({
        /**
         * init function
         */
        initialize: function (config) {
            var self = this,
                addQty = $('#add-cart-item'),
                subtractQty = $('#subtract-cart-item'),
                qtyBlock = $('.qty-block').find("input[type=number]"),
                qtyVal = parseInt(qtyBlock.val()),
                buynowBt = $('#product-buy-now'),
                addtocartBt = $('#product-addtocart-button'),
                productType = config.productType,
                productId = config.productId;

            //check qty val input load default
            if (qtyVal <= 1) {
                subtractQty.attr('disabled', true);
                subtractQty.css('background', '#ccc');
            }

            if (productType === 'simple') {
                self._addQtySimpleAction(addQty, qtyBlock, subtractQty, productId, productType);
            } else {
                self._addQtyAction(addQty, qtyBlock, subtractQty, productId, productType);
            }

            self._subtractQtyAction(subtractQty, qtyBlock);

            self._resetQtyValWhenAddCart(addtocartBt, qtyBlock);

            self._resetQtyWhenBuyNow(buynowBt, qtyBlock);
        },

        /**
         *  add quantity action
         */
        _addQtyAction: function (addQty, qtyBlock, subtractQty, productId, productType) {
            const MIN_QUANTITY_ALLOW = 1;
            const MAX_QUANTITY_ALLOW = 99;
            const QUANTITY_LAST_STEP = 98;
            addQty.on('click', function () {
                var qtyVal = parseInt(qtyBlock.val());

                //increase qty to add cart
                if (qtyVal <= QUANTITY_LAST_STEP) {
                    qtyVal = qtyVal + 1;
                    qtyBlock.val(qtyVal);
                }

                //disabled addQty qty button
                if (qtyVal == MAX_QUANTITY_ALLOW) {
                    addQty.attr('disabled', true);
                    addQty.css('background', '#ccc');
                }
                //remove disabled attribute subtract qty button
                if (qtyVal > MIN_QUANTITY_ALLOW) {
                    subtractQty.css('background', '');
                    subtractQty.attr('disabled', false);
                }
            });
        },

        /**
         *  add quantity action
         */
        _addQtySimpleAction: function (addQty, qtyBlock, subtractQty, productId, productType) {
            const MAX_QUANTITY_ALLOW = 99;
            const QUANTITY_LAST_STEP = 98;
            var qtyVal,
                tooltipQty = $('.tooltip-qty');
            addQty.on('click', function () {
                if(productType === 'simple'){
                    qtyVal = parseInt(qtyBlock.val());
                }
                //increase qty to add cart
                if (qtyVal <= QUANTITY_LAST_STEP) {
                    qtyVal = qtyVal + 1;
                    var url = urlBuilder.build('catalog/product/checkqtystock'),
                        data = {
                            product_id: productId,
                            product_qty: qtyVal
                        };

                    $.ajax({
                        url: url,
                        data: data,
                        type: 'post',
                        dataType: 'json',
                        showLoader: true,
                        success: function (data) {
                            if (data.result) {
                                qtyBlock.val(qtyVal);
                                //show tooltip mess
                                if (qtyVal == parseInt(data.qtySource)) {
                                    tooltipQty.show();
                                    addQty.attr('disabled', true);
                                    addQty.css('background', '#ccc');
                                }
                                //case max quantity allow
                                if (qtyVal == MAX_QUANTITY_ALLOW) {
                                    addQty.attr('disabled', true);
                                    addQty.css('background', '#ccc');
                                }
                            } else {
                                tooltipQty.show();
                                addQty.attr('disabled', true);
                                addQty.css('background', '#ccc');
                            }
                        }
                    });
                }
                //remove disabled attribute subtract qty button
                if (qtyVal > 1) {
                    subtractQty.css('background', '');
                    subtractQty.attr('disabled', false);
                    //subtractQty.on("click");
                }
            });
        },

        /**
         *  subtract quantity action
         */
        _subtractQtyAction: function (subtractQty, qtyBlock) {
            const MIN_QUANTITY_ALLOW = 1;
            const MAX_QUANTITY_ALLOW = 99;
            subtractQty.on('click', function () {
                var qtyVal = parseInt(qtyBlock.val()),
                    tooltipQty = $('.tooltip-qty');

                if (qtyVal <= MIN_QUANTITY_ALLOW) {
                    subtractQty.attr('disabled', true);
                    subtractQty.css('background', '#ccc');
                }
                //des-crease qty to add cart
                if (qtyVal > MIN_QUANTITY_ALLOW) {
                    qtyVal = qtyVal - 1;
                    qtyBlock.val(qtyVal);
                    tooltipQty.hide();
                    $("#add-cart-item").attr('disabled', false);
                    $("#add-cart-item").removeClass('disabled');
                    $("#add-cart-item").css('background', '#f7b500');
                } else {
                    subtractQty.css('background', '#ccc');
                    subtractQty.attr('disabled', true);
                    //subtractQty.off("click");
                }

                if (qtyVal <= MIN_QUANTITY_ALLOW) {
                    subtractQty.attr('disabled', true);
                    subtractQty.css('background', '#ccc');
                }
            });
        },

        /**
         *  reset qty block val is 1 when add cart
         */
        _resetQtyValWhenAddCart: function (addtocartBt, qtyBlock) {
            addtocartBt.on('click', function () {
                setTimeout(function () {
                    qtyBlock.val(1);
                }, 3000);
            });
        },

        /**
         *  reset qty block val is 1 when buy now
         */
        _resetQtyWhenBuyNow: function (buynowBt, qtyBlock) {
            buynowBt.on('click', function () {
                setTimeout(function () {
                    qtyBlock.val(1);
                }, 1000);
            });
        },
    })
});


