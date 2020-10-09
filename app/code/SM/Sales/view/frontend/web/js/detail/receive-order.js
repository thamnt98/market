define(
    [
        'jquery',
        'Magento_Ui/js/modal/modal',
        'Magento_Ui/js/lib/view/utils/async'
    ],
    function ($, modal, async) {
        'use strict';

        return function () {
            window.eventCreate = window.eventCreate || {};
            var selector = ".btn-received";
            async.async(selector ,function () {
                if (!eventCreate[selector]) {
                    eventCreate[selector] = 0;
                }

                let current = $($(selector)[eventCreate[selector]]);
                $(current).click(function (e) {
                    let submitUrl = $(this).attr("data-receive-url");
                    let options = {
                        type : 'popup',
                        title : $.mage.__('Order Received'),
                        modalClass: 'pp-shopping-list',
                        responsive: true,
                        buttons: [{
                            text: jQuery.mage.__('Cancel'),
                            class: 'action secondary',
                            click: function () {
                                this.closeModal();
                            }
                        }, {
                            text: jQuery.mage.__('Confirm'),
                            class: 'action primary',
                            click: function () {
                                window.location.href = submitUrl;
                            }
                        }]
                    };

                    var firstLine = $('<span/>')
                        .append($('<span/>', {
                            text: $.mage.__("We're glad that you have received the item(s)!")
                        }))
                       /* .append($('<b/>', {
                            text: $.mage.__("item(s)!")
                        }));*/

                    var secondLine = $('<span/>')
                        /*.append($('<b/>', {
                            text: $.mage.__("Please confirm to ")
                        }))*/
                        .append($('<span/>', {
                            text: $.mage.__("Please confirm to finish this order.")
                        }));

                    var confirmModal = $('<div/>', {
                        "class": 'content'
                    }).append($('<span/>', {
                        "class": 'remove-wishlist-verify'
                    })
                        .append(firstLine)
                        .append($("<br/>"))
                        .append(secondLine));

                    $(confirmModal).modal(options).modal('openModal');
                });
                eventCreate[selector]++;
            });
        };
    }
);
