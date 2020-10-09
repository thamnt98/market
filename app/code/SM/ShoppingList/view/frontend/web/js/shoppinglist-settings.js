define(
    [
        'jquery',
        'Magento_Ui/js/modal/modal',
        'SM_ShoppingList/js/alert'
    ],
    function ($, modal, alertModal) {
        'use strict';

        $.widget('vendor.mod', {
            _create: function () {
                this._initElement();
            },
            _initElement: function () {
                var that = this;
                var options_move = {
                    type : 'popup',
                    title : $.mage.__('Settings'),
                    modalClass: 'pp-shopping-list',
                    responsive: true,
                    buttons: [{
                        text: jQuery.mage.__('Cancel'),
                        class: 'action secondary',
                        click: function () {
                            $("#input-wishlist-name").val(that.options.shoppinglist_name);
                            this.closeModal();
                        }
                    }, {
                        text: jQuery.mage.__('Save'),
                        class: 'action primary',
                        click: function () {
                            that.updateList($("#input-wishlist-name").val(), that.options.list_id, this);
                        }
                    }]
                };
                modal(options_move, $('#shopping-list-setting-modal'));

                $("#button-open-settings-modal").on('click', function () {
                    $('#shopping-list-setting-modal').modal('openModal');
                });

                var options_delete = {
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
                            window.location.href = that.options.remove_list_url ;
                        }
                    }]
                };

                modal(options_delete, $('#confirm-delete-list-modal'));

                $("#button-confirm-delete-list-modal").on('click', function () {
                    if (that.options.is_default == true) {
                        alertModal.showError("You can not delete My Favorites.")
                    } else {
                        $('#confirm-delete-list-modal').modal('openModal');
                    }

                });

                $('input[type=radio][name="sort"]').change(function () {
                    $("#form-sort-item").submit();
                });

                var options_share = {
                    type : 'popup',
                    title : $.mage.__('Share "') + this.options.shoppinglist_name + '"',
                    modalClass: 'pp-shopping-list share-shoplist',
                    responsive: true
                };

                modal(options_share, $('#share-shoppinglist-modal'));

                $("#btn-share-list").on('click', function () {
                    $('#share-shoppinglist-modal').modal('openModal');
                });

                $("#btn-copy-share-url").on("click", function () {
                    $("#value-share-url").select();
                    document.execCommand("copy");
                });

            },
            /**
             *
             * @param name
             * @param list_id
             * @param modal_settings
             */
            updateList : function (name, list_id, modal_settings) {
                if (name === '') {
                    alertModal.showError("Shopping List name is empty");
                } else if (name === this.options.shoppinglist_name) {
                    modal_settings.closeModal();
                } else {
                    var data = {
                        update_list_name : name,
                        list_id : list_id
                    };
                    $.ajax({
                        url: this.options.update_list_url,
                        type: "POST",
                        data: data,
                        dataType: 'json',
                        showLoader: true,
                        success: function (response) {
                            if (response.status == 1) {
                                $("#form-update-list").submit();
                            } else {
                                alertModal.showError(response.result);
                            }
                        },
                        error: function () {
                            alertModal.showError("An error occurred. Refresh page and try again");
                        }
                    });
                }
            }
        });

        return $.vendor.mod;
    }
);
