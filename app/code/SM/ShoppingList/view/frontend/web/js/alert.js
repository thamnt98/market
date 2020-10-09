define(
    [
        'jquery',
        'Magento_Ui/js/modal/modal'
    ],
    function ($, modal) {
        "use strict";

        var alertModal;

        alertModal = {
            /**
             * @param error_log
             */
            showError: function (error_log) {
                var errorModal = $('<div/>', {
                    "class": 'content'
                }).append($('<span/>', {
                    text: $.mage.__(error_log)
                }));

                var options = {
                    type: 'popup',
                    responsive: true,
                    modalClass: 'pp-shopping-list',
                    title: $.mage.__('Error'),
                    buttons: [{
                        text: jQuery.mage.__('OK'),
                        class: 'action primary',
                        click: function () {
                            this.closeModal();
                        }
                    }]
                };
                $(errorModal).modal(options).modal('openModal');
            },

            showExist: function (error_log) {
                var errorModal = $('<div/>', {
                    "class": 'content'
                }).append($('<span/>', {
                    text: $.mage.__(error_log)
                }));

                var options = {
                    type: 'popup',
                    responsive: true,
                    modalClass: 'pp-shopping-list',
                    title: $.mage.__('It\'s already there'),
                    buttons: [{
                        text: jQuery.mage.__('Okay'),
                        class: 'action primary',
                        click: function () {
                            this.closeModal();
                        }
                    }]
                };
                $(errorModal).modal(options).modal('openModal');
            }
        };
        return alertModal;
    }
);

