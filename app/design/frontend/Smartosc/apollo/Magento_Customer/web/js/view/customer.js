define([
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'jquery',
], function (Component, customerData, $) {
    'use strict';

    var customerMenu = $('.header.links');

    customerMenu.on('dropdowndialogopen', function () {
        var hideBackground = '<div class="modals-overlay" id="background-hidden"></div>';
        $('body').after(hideBackground);

        jQuery('.authorization-link a').click(function () {
            jQuery(document).trigger('customer:logout');
        });

        jQuery('ul.header.links li:not(.link) a:first').click(function () {
            jQuery(document).trigger('my_account');
        });

        jQuery('.my-orders').click(function () {
            jQuery(document).trigger('my_account_my_orders');
        });

        jQuery('.ct-cash').click(function () {
            jQuery(document).trigger('my_account_ct_cash');
        });

        jQuery('li.link.wishlist a').click(function () {
            jQuery(document).trigger('my_account_wishlist');
        });

        jQuery('.my-reviews').click(function () {
            jQuery(document).trigger('my_account_my_reviews');
        });

        jQuery('.subscription').click(function () {
            jQuery(document).trigger('my_account_subscription');
        });

        jQuery('.notification-settings').click(function () {
            jQuery(document).trigger('my_account_notification_setting');
        });

        jQuery('.gift-registry').click(function () {
            jQuery(document).trigger('my_account_gift_registry');
        });

        jQuery('.points-vouchers').click(function () {
            jQuery(document).trigger('my_account_points_vouchers');
        });

        $('.customer-menu').find('li').each(function () {
            $(this).on('touchstart', function (event) {
                event.preventDefault();
                event.stopPropagation();
                window.location.href = $(this).find('a').attr('href');
            })
        });
    });

    customerMenu.on('dropdowndialogclose', function () {
        $('#background-hidden').remove();
    });

    return Component.extend({
        /** @inheritdoc */
        initialize: function () {
            this._super();

            this.customer = customerData.get('customer');
        }
    });
});
