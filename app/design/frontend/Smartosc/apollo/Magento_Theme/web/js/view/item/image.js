define([
    'uiComponent'
], function (Component) {
    'use strict';

    return Component.extend({
        /** @inheritdoc */
        initialize: function () {
            this._super();

            this.template = window.checkout.imageTemplate || this.template;
        }
    });
});
