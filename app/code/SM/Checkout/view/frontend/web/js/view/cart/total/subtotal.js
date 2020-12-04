/**
 * SMCommerce
 *
 * @category    SM
 * @package     SM_Checkout
 *
 * Date: December, 03 2020
 * Time: 3:58 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */
define([
    'Magento_Tax/js/view/checkout/summary/subtotal',
    'jquery'
], function (Component, $) {
    'use strict';

    return Component.extend({
        getTitle: function () {
            let title = this.title;

            if (this.totals()) {
                let totalItems = parseInt(this.totals().items_qty);

                title += ' <span class="totals-count-items">'
                    + $.mage.__("(%1 items)").replace('%1', totalItems)
                    + '</span>';
            }

            return title;
        }
    });
});