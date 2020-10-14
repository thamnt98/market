/**
 * @category SM
 * @package SM_Coachmarks
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
    'mage/translate'
], function ($, Component) {
    'use strict';

    return Component.extend({
        /**
         * init function
         */
        initialize: function (config) {
            this._super();
            self = this;

            var productId = config.productId;

            var addQty = $('#add-cart-item-' + productId),
                subtractQty = $('#subtract-cart-item-'+ productId),
                qtyBlock = $('#qty-action-pdp-'+ productId).find("input[type=number]");

            addQty.on('click', function(){
                var qtyVal = parseInt(qtyBlock.val());
                //increase qty to add cart
                qtyVal = qtyVal + 1;
                qtyBlock.val(qtyVal);
            });

            subtractQty.on('click', function(){
                var qtyVal = parseInt(qtyBlock.val());
                //des-crease qty to add cart
                if(qtyVal > 0){
                    qtyVal = qtyVal - 1;
                    qtyBlock.val(qtyVal);
                }
            });

        },

    })
});
