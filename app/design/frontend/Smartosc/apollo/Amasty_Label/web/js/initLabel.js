/**
 * Initialize Module depends on the area
 * @return widget
 */

define([
    'jquery',
    'Amasty_Label/js/label',
    'domReady!'
], function ($) {

    $.widget('mage.amInitLabel', {
        options: {
            mode: null,
            isAdminArea: null,
            config: null,
            productId: null,
            selector: null,
        },

        /**
         * Widget constructor.
         * @protected
         */
        _create: function () {
            var self = this,
                element = $("[data-product-id='" + self.options.productId + "']").closest('.product-item, .item');

            // observe only on category pages and without swatches
            if (self.options.mode === 'prod'
                || self.options.isAdminArea
                || self.element.hasClass('amlabel-swatch')
                || self.isIE()
            ) {
                self.execLabel();
            } else if (self.options.mode === 'cat' && element.length && !self.element.hasClass('amlabel-swatch')) {
                self._handleIntersect(element);
                //custom insert label
                self._insertValueLabelEl(element);
                //end custom insert label
            } else {
                self.execLabel();
            }
        },

        /**
         * Exec Amasty Label widget
         * @public
         */
        execLabel: function () {
            this.element.amShowLabel(this.options.config);
        },

        /**
         *
         * @returns {boolean}
         */
        isIE: function () {
            var ua = window.navigator.userAgent;

            return ua.indexOf("MSIE ") > -1 || ua.indexOf("Trident/") > -1;
        },

        /**
         * Use IntersectionObserver to lazy loading Amasty Label widget
         * @protected
         * @returns {function}
         */
        _handleIntersect: function (element) {
            var self = this,
                observer;

            observer = new IntersectionObserver(function (entries) {
                if (entries[0].isIntersecting) {
                  self.execLabel();
                  observer.disconnect();
                }
            });

            observer.observe(element[0]);
        },

        //custom insert label to cat list
        /**
         * Insert value to input label element
         * @public
         */
        _insertValueLabelEl: function (element) {
            var self = this,
                itemLabelEl = $(this.options.selector);
            if(itemLabelEl.text() != ''){
                var itemInputEl = $('input[name=product-cat-label-' + self.options.productId + ']');

                itemInputEl.val($.trim(itemLabelEl.text()));
            }
        },
        //end custom insert label to cat list
    });

    return $.mage.amInitLabel;
});
