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
                title : $.mage.__('Reorder All Products'),
                modalClass: 'pp-shopping-list',
                responsive: true,
                buttons: [{
                    text: $.mage.__('Cancel'),
                    class: 'action secondary',
                    click: function () {
                        this.closeModal();
                    }
                }, {
                    text: $.mage.__('Yes'),
                    class: 'action primary',
                    click: function () {
                        $("#form-reorder-all").submit();
                    }
                }]
            };

            modal(options, $("#confirm-reorder-all"));

            $("#btn-reorder-all").on("click", function () {
                $("#confirm-reorder-all").modal('openModal');
            });
        }
    }
);

