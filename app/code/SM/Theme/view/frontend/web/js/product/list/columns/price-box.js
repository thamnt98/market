define(function () {
    'use strict';

    var mixin = {
        defaults: {
            bodyTmpl: 'SM_Theme/product/price/price_box',
        },
        isBundle: function (row) {
            return row.type === 'bundle';
        }
    };

    return function (target) { // target == Result that Magento_Ui/.../columns returns.
        return target.extend(mixin); // new result that all other modules receive
    };
});
