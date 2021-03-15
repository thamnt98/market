define([
    'jquery'
], function ($) {
    'use strict';

    return function (widget) {
        $.widget('mage.catalogAddToCart', widget, {
            /**
             * Handler for the form 'submit' event
             *
             * @param {jQuery} form
             */
            submitForm: function (form) {
                if ($('.sign-link a').length == 0) {
                    this.ajaxSubmit(form);
                } else {
                    $('.sign-link a').trigger('click');
                }
            }
        });

        return $.mage.catalogAddToCart;
    }
});
