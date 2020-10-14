define([
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'jquery',
    'ko',
    'underscore'
], function (Component, customerData, $, ko, _) {
    'use strict';

    return Component.extend({

        /** @inheritdoc */
        initialize: function () {
            let self = this;
            this._super();

            this.wishlist = customerData.get('wishlist');

            $('[data-block="sub-wishlist"]').on('contentLoading', function () {
                self.isLoading(true);
            });
        },

        isLoading: ko.observable(false),

        /**
         * Get wishlist param by name.
         */
        getWishlistCounter: function () {
            if (this.wishlist().counter) {
                return parseInt(this.wishlist().counter);
            }

            return 0;
        },
    });
});
