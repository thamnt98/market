define(
    [
        'jquery',
        'Magento_Ui/js/modal/modal'
    ],
    function ($, modal) {
        'use strict';

        $.widget('vendor.mod', {
            _create: function () {
                this._initElement();
            },
            _initElement: function () {
                var that = this;
                var options = {
                    type : 'popup',
                    title : $.mage.__('Settings'),
                    modalClass: 'pp-shopping-list',
                    responsive: true,

                    buttons: [{
                        text: jQuery.mage.__('Cancel'),
                        class: 'action secondary',
                        click: function () {
                            this.closeModal();
                        }
                    }, {
                        text: jQuery.mage.__('Delete'),
                        class: 'action primary',
                        click: function () {
                            window.location.href = that.options.remove_list_url;
                        }
                    }]
                };

                modal(options, $('#confirm-delete-list-modal-' + this.options.list_id));

                $("#open-delete-modal-" + this.options.list_id).on('click', function () {
                    $('#confirm-delete-list-modal-' + that.options.list_id).modal('openModal');
                });

                // var options_share = {
                //     type : 'popup',
                //     title : $.mage.__('Share "') + this.options.list_name + '"',
                //     modalClass: 'pp-shopping-list share-shoplist',
                //     responsive: true
                // };
                //
                // modal(options_share, $('#share-list-modal-' + this.options.list_id));
                //
                // $("#open-share-modal-" + this.options.list_id).on('click', function () {
                //     $('#share-list-modal-' + that.options.list_id).modal('openModal');
                // });

                // $("#btn-copy-share-url-" + this.options.list_id).on("click", function () {
                //
                //     /* Select the text field */
                //     $("#value-share-url-" + that.options.list_id).select();
                //
                //     /* Copy the text inside the text field */
                //     document.execCommand("copy");
                // });
            }
        });

        return $.vendor.mod;
    }
);
