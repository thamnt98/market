define(
    [
        'jquery',
        'Magento_Ui/js/modal/modal',
        'Magento_Customer/js/customer-data',
        'SM_ShoppingList/js/alert',
        'mage/url'
    ],
    function ($, modal, customerData, alertModal, urlBuilder) {
        'use strict';

        $.widget('vendor.mod', {
            _create: function () {
                this._initElement();
            },
            _initElement: function () {
                var that = this;
                window.clickFirst = true;
                window.favoriteId = false;
                var options = {
                    type: 'popup',
                    title: $.mage.__('Add Product to Shopping List'),
                    modalClass: 'pp-shopping-list',
                    responsive: true,
                    buttons: [{
                        text: jQuery.mage.__('Cancel'),
                        class: 'action secondary move-product-cancel',
                        click: function () {
                            $("#shoppinglist-name-pdp").val("");
                            $('input[type=checkbox][name="selected[]"]').prop('checked', false);
                            $("#create-to-add").hide();
                            this.closeModal();
                            $('footer.modal-footer .error-message').hide().text('')
                        }
                    }, {
                        text: jQuery.mage.__('Add'),
                        class: 'action primary move-product',
                        click: function () {
                            var selected = $('input[type=checkbox][name="selected[]"]:checked');
                            var data = [];
                            if (window.clickFirst) {
                                $('footer.modal-footer').prepend(
                                    '<div class="error-message" style="display: none"> </div>'
                                )
                            }
                            window.clickFirst = false;
                            $('footer.modal-footer .error-message').text('');
                            if (!selected.length) {
                                if (window.favoriteId) {
                                    data.push(Number(window.favoriteId));
                                } else {
                                    that.showErrorMessage('Shopping List name is empty!');
                                }
                            } else {
                                selected.each(function () {
                                    data.push(Number(this.value));
                                });
                            }
                            if (data.length) {
                                that.addItems(data, Number($("#btn-open-add-item").data("product-id")), this);
                            }
                        }
                    }]
                };

                modal(options, $('#add-item-modal'));

                $("#btn-open-add-item").on('click', function () {
                    $('#add-item-modal').modal('openModal');
                    var favoriteItem = $('#add-item-modal input[type=checkbox]')[0];
                    window.favoriteId = $(favoriteItem).val();
                    $(favoriteItem).prop('checked', true);
                });

                $("#open-create-to-add").on('click', function () {
                    $("#create-to-add").show();
                });

                $("#submit-create-pdp").on('click', function () {
                    if (window.clickFirst) {
                        $('footer.modal-footer').prepend(
                            '<div class="error-message" style="display: none"> </div>'
                        )
                    }
                    window.clickFirst = false;
                    $('footer.modal-footer .error-message').text('');
                    var shoppinglist_name = $("#shoppinglist-name-pdp").val();
                    that.createShoppingList(shoppinglist_name);

                })
            },

            /**
             * @param name
             */
            createShoppingList : function (name) {
                var that = this;
                if (name === '') {
                    that.showErrorMessage('Shopping List name is empty!');
                } else {
                    var data = {
                        shopping_list_name : name
                    };
                    $('footer.modal-footer .error-message').hide()
                    $.ajax({
                        url: urlBuilder.build("wishlist/ajax/createlist"),
                        type: "POST",
                        data: data,
                        dataType: 'json',
                        showLoader: true,
                        success: function (response) {
                            if (response.status == 1) {
                                data = response.result
                                $('<li/>', {
                                    "class": 'checkbox-custom'
                                }).append($('<input/>', {
                                    type : "checkbox",
                                    name : "selected[]",
                                    value : data.list_id,
                                    checked : true
                                })).append($('<label/>', {
                                    text : data.name
                                })).appendTo("#list-choice-pdp");

                                $("#create-to-add").hide();
                                $("#shoppinglist-name-pdp").val("");
                                $('footer.modal-footer .error-message').hide().text('')
                            } else {
                                that.showErrorMessage(response.result);
                            }


                        },
                        error: function () {
                            that.showErrorMessage('An error occurred. Please refresh page and try again');
                        }
                    });
                }
            },

            addItems : function (selected, product_id, add_modal) {
                var that = this;

                var data = {
                    shopping_list_ids: selected,
                    product_id: product_id,
                    show_toast: false
                };
                $('footer.modal-footer .error-message').hide()
                $.ajax({
                    url: urlBuilder.build("wishlist/ajax/additems"),
                    type: "POST",
                    data: data,
                    dataType: 'json',
                    showLoader: true,
                    success: function (data) {
                        if (data.status == 1) {
                            that.showSuccess(data.result);
                            add_modal.closeModal();

                        } else {
                            that.showErrorMessage(data.result);
                        }
                    },
                    error: function () {
                        that.showErrorMessage("An error occurred. Please refresh page and try again");
                    }
                });

            },

            showSuccess : function (data) {
                var that = this;
                var options = {
                    type: 'popup',
                    responsive: true,
                    modalClass: 'pp-shopping-list pp-show-success',
                    title: $.mage.__('Add Product to Shopping List'),
                    buttons: [{
                        text: jQuery.mage.__('OK'),
                        class: 'action primary',
                        click: function () {
                            this.closeModal();
                        }
                    }]
                };

                var listWishlist = [];

                jQuery.each(data, function (key, val) {
                    var element = '<li><a href="' + val.url + '">' + val.name + '</a></li>';
                    $("#destination-list").append(element);
                    listWishlist.push(val.name);
                });

                $('#success-alert-modal').modal(options).modal('openModal').on('modalclosed', function () {
                    $('input[type=checkbox][name="selected[]"]').prop('checked', false);
                    $("#destination-list").empty();
                });

                $('[data-gtm-pdp="add-to-wishlist"]').attr('data-gtm-wishlist', listWishlist.toString()).val('true').change();
            },


            /**
             * check item has added to show action
             * */
            checkItemHasAdded(productId, is_added_shopping_list) {
                var itemAdd = $('#btn-open-add-item'),
                    itemRemove = $('#btn-open-remove-item');

                if (is_added_shopping_list === 'true') {
                    itemAdd.hide();
                    itemRemove.show();
                }
            },

            /**
             * remove item from product list page
             * */
            removeItemFromPLP(productId) {
                var self = this,
                    itemAdd = $('#btn-open-add-item'),
                    itemRemove = $('#btn-open-remove-item');

                var data = {product_id: productId};
                $.ajax({
                    url: urlBuilder.build("wishlist/ajax/removeitem"),
                    data: data,
                    type: 'post',
                    dataType: 'json',
                    showLoader: true,
                    success: function (result) {
                        if (result) {
                            itemAdd.show();
                            itemRemove.hide();
                        }
                    }
                });
            },
            showErrorMessage: function (message) {
                $('footer.modal-footer .error-message').show().text($.mage.__(message))
            }

        });

        return $.vendor.mod;
    }
);
