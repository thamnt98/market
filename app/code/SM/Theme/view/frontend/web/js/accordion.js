/**
 * SMCommerce
 *
 * @category
 * @package   _${MODULE}
 *
 * Date: May, 26 2020
 * Time: 3:35 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */
define([
    'jquery',
    'mage/accordion'
], function ($) {
    'use strict';

    $.widget('sm.filterAccordion', $.mage.accordion, {
        _create: function () {
            if ($(window).width() < 768) {
                this.options.active = false;
            }

            if (typeof this.options.disabled === 'string') {
                this.options.disabled = this.options.disabled.split(' ').map(function (item) {
                    return parseInt(item, 10);
                });
            }
            this._processPanels();
            this._handleDeepLinking();
            this._processTabIndex();
            this._closeOthers();
            this._bind();
        }
    });

    return $.sm.filterAccordion;
});