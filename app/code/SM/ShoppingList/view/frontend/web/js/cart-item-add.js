define(
    [
        'jquery',
        'Magento_Ui/js/modal/modal',
        'SM_ShoppingList/js/alert',
        'gtmProduct',
        'mage/url'
    ],
    function ($, modal, alertModal, gtmProduct, urlBuilder) {
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
                            $("#shoppinglist-name-" + that.options.item_id).val("");
                            $('input[type=checkbox][name="selected-' + that.options.item_id + '[]"]').prop('checked', false);
                            $("#create-to-add-" + that.options.item_id).hide();
                            this.closeModal();
                            $('footer.modal-footer .error-message').hide()
                        }
                    }, {
                        text: jQuery.mage.__('Add'),
                        class: 'action primary move-product',
                        click: function () {
                            var selected = $('input[type=checkbox][name="selected-' + that.options.item_id + '[]"]:checked');
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
                                that.addItems(data, that.options.product_id, this);
                            }
                        }
                    }]
                };

                modal(options, $("#add-item-modal-" + that.options.item_id));

                $("#btn-open-add-item-" + that.options.item_id).on('click', function () {
                    $("#add-item-modal-" + that.options.item_id).modal('openModal');
                    var favoriteItem = $('#add-item-modal-' + that.options.item_id + ' input[type=checkbox].shopping-item')[0];
                    window.favoriteId = $(favoriteItem).val();
                    $(favoriteItem).prop('checked', true);
                    $('footer.modal-footer .error-message').text('');
                });

                $("#open-create-to-add-" + that.options.item_id).on('click', function () {
                    $("#create-to-add-" + that.options.item_id).show();
                });

                $("#submit-create-cart-" + that.options.item_id).on('click', function () {
                    if (window.clickFirst) {
                        $('footer.modal-footer').prepend(
                            '<div class="error-message" style="display: none"> </div>'
                        )
                    }
                    window.clickFirst = false;
                    $('footer.modal-footer .error-message').text('');
                    var shoppinglist_name = $("#shoppinglist-name-" + that.options.item_id).val();
                    that.createShoppingList(shoppinglist_name);

                })
            },

            /**
             *
             * @param name
             */
            createShoppingList : function (name) {
                var that = this;
                if (name === '') {
                    that.showErrorMessage("Shopping List name is empty!");
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
                                data = response.result;
                                var item_ids = that.options.item_ids.split(",");
                                item_ids.forEach(myFunction);
                                $("#shoppinglist-name-" + that.options.item_id).val("");
                                $("#create-to-add-" + that.options.item_id).hide();
                                function myFunction(item_id, index)
                                {
                                    $('<li/>', {
                                        "class": 'checkbox-custom'
                                    }).append($('<input/>', {
                                        type : "checkbox",
                                        name : "selected-'  + item_id +'[]",
                                        value : data.list_id,
                                        checked : true
                                    })).append($('<label/>', {
                                        text : data.name
                                    })).appendTo("#list-choice-" + item_id);

                                }
                            } else {
                                that.showErrorMessage(response.result);
                            }
                        },
                        error: function () {
                            that.showErrorMessage("An error occurred. Please refresh page and try again");
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
                var shoppingLists = "";

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
                            if (typeof dataLayerSourceObjects !== 'undefined') {
                                $.each(selected, function (key, value) {
                                    shoppingLists += ($('input[value="'+ value+ '"]').parents().eq(0).find('label').text() + ", ");
                                });
                                if (product_id) {
                                    let productIds = [];
                                    let dataProduct = {
                                        'productId' : product_id
                                    };
                                    productIds.push(dataProduct);
                                    let data = {
                                        'productsInfo' : productIds
                                    };
                                    $.ajax({
                                        type: 'POST',
                                        url: urlBuilder.build('sm_gtm/gtm/product'),
                                        data: data,
                                        dataType: "json",
                                        async: true,
                                        success: function (result) {
                                            //Todo Remove Debug
                                            if (result) {
                                                try {
                                                    let dataParse = JSON.parse(result);
                                                    dataParse['shopping_listName'] = shoppingLists;
                                                    gtmProduct.push('add_to_shoppingList', dataParse);
                                                } catch (e) {

                                                }
                                            }
                                        },
                                        error: function () {

                                        }
                                    });
                                }
                            }
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
                    title: $.mage.__('Add Product to Shopping List'),
                    modalClass: 'pp-shopping-list',
                    buttons: [{
                        text: jQuery.mage.__('OK'),
                        class: 'action primary',
                        click: function () {
                            this.closeModal();
                            $('input[type=checkbox][name="selected-' + that.options.item_id + '[]"]').prop('checked', false);
                            $("#destination-list-" + that.options.item_id).empty();
                        }
                    }]
                };

                jQuery.each(data, function (key, val) {
                    var element = '<li><a href="' + val.url + '">' + val.name + '</a></li>';
                    $("#destination-list-" + that.options.item_id).append(element);
                });

                $("#success-alert-modal-" + that.options.item_id).modal(options).modal('openModal');
            },

            showErrorMessage: function (message) {
                $('footer.modal-footer .error-message').text('');
                $('footer.modal-footer .error-message').show().text($.mage.__(message))
            }

        });

        return $.vendor.mod;
    }
);
