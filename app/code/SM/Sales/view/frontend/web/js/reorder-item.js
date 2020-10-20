define(
    [
        'jquery',
        'Magento_Ui/js/modal/modal'
    ],
    function ($, modal) {
        "use strict";

        return function (config) {
            var options = {
                type : 'popup',
                title : $.mage.__('Confirm'),
                modalClass: 'pp-shopping-list',
                responsive: true,
                buttons: [{
                    text: $.mage.__('No'),
                    class: 'action secondary',
                    click: function () {
                        this.closeModal();
                    }
                }, {
                    text: $.mage.__('Yes'),
                    class: 'action primary',
                    click: function () {
                        $("#form-reorder-item-" + config.item_id).submit();
                    }
                }]
            };

            modal(options, $("#confirm-reorder-item-" + config.item_id));

            $("#btn-reorder-item-" + config.item_id).on("click", function () {
                $("#confirm-reorder-item-" + config.item_id).modal('openModal');
            });
        }
    }
);

