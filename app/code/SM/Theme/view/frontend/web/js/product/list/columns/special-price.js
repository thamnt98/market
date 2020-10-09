define(function () {
    'use strict';

    var mixin = {

        isGroupedProduct: function (row) {
            return row['type'] === 'grouped';
        }
    };

    return function (target) { // target == Result that Magento_Ui/.../columns returns.
        return target.extend(mixin); // new result that all other modules receive
    };
});
