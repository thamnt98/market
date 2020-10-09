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
                window.clickFirst = true;
                var options = {
                    type: 'popup',
                    title: $.mage.__('Move Product to'),
                    modalClass: 'pp-shopping-list',
                    responsive: true,
                    buttons: [{
                        text: jQuery.mage.__('Cancel'),
                        class: 'action secondary move-product-cancel',
                        click: function () {
                            $("#shoppinglist-name-" + that.options.item_id).val("");
                            $('input[type=checkbox][name="selected-' + that.options.item_id + '[]"]').prop('checked', false);
                            $("#create-to-move-" + that.options.item_id).hide();
                            this.closeModal();
                        }
                    }, {
                        text: jQuery.mage.__('Move'),
                        class: 'action primary move-product',
                        click: function () {
                            var selected = $('input[type=checkbox][name="selected-' + that.options.item_id + '[]"]:checked');
                            if (window.clickFirst) {
                                $('footer.modal-footer').prepend(
                                    '<div class="error-message"> </div>'
                                )
                            }
                            window.clickFirst = false;
                            $('footer.modal-footer .error-message').text('');
                            if (!selected.length) {
                                that.showErrorMessage("Please select a shopping list to continue");
                            } else {
                                var data = [];
                                selected.each(function () {
                                    data.push(Number(this.value));
                                });

                                that.moveItem(data, that.options.product_id, this);
                            }

                        }
                    }]
                };

                modal(options, $('#move-shoppinglist-modal-' + this.options.item_id));

                $("#button-open-move-" + this.options.item_id).on('click', function () {
                    $('#move-shoppinglist-modal-' + that.options.item_id).modal('openModal');
                    $('footer.modal-footer .error-message').text('');
                });

                $("#open-create-to-move-" + this.options.item_id).on('click', function () {
                    $("#create-to-move-" + that.options.item_id).show();
                });

                $("#submit-create-" + this.options.item_id).on('click', function () {

                    var shoppinglist_name = $("#shoppinglist-name-" + that.options.item_id).val();
                    that.createShoppingList(shoppinglist_name);

                });

                var remove_options = {
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
                            window.location.href = that.options.remove_item_url
                        }
                    }]
                };

                modal(remove_options, $('#confirm-delete-item-modal-' + this.options.item_id));

                $("#button-remove-item-" + this.options.item_id).on('click', function () {
                    if (typeof dataLayerSourceObjects !== 'undefined') {
                        let dt = new Date();
                        let time = $.datepicker.formatDate('dd/mm/yy', dt) + ' ' + dt.getHours() + ":" + dt.getMinutes() + ":" + dt.getSeconds();
                        window.dataLayer = window.dataLayer || [];
                        window.dataLayer.push({
                            'event': 'remove_shoppingList',
                            'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                            'userID': dataLayerSourceObjects.customer.userID,
                            'customerID': dataLayerSourceObjects.customer.customerID,
                            'customerType': dataLayerSourceObjects.customer.customerType,
                            'loyalty': dataLayerSourceObjects.customer.loyalty,
                            'customerStatus': dataLayerSourceObjects.customer.customerStatus,
                            'loginType': dataLayerSourceObjects.customer.loginType,
                            'timestamp': time,
                            'shoppingList_name': $.trim($('.shopping-lists .product.data.items .item.title.active').text())
                        })
                    }
                    window.location.href = that.options.remove_item_url;
                });
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
                    $.ajax({
                        url: this.options.create_list_url,
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
                                $("#create-to-move-" + that.options.item_id).hide();
                                function myFunction(item_id, index)
                                {
                                    $('<li/>', {
                                        "class": 'checkbox-custom'
                                    }).append($('<input/>', {
                                        type : "checkbox",
                                        name : "selected-" + item_id + "[]",
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

            moveItem : function (selected, product_id, add_modal) {
                var that = this;

                var data = {
                    shopping_list_ids: selected,
                    item_id: this.options.item_id
                };

                $.ajax({
                    url: this.options.move_item_url,
                    type: "POST",
                    data: data,
                    dataType: 'json',
                    showLoader: true,
                    success: function (response) {
                        if (response.status == 1) {
                            that.showSuccess(response.result);
                            $("#list-item-" + that.options.item_id).remove();
                            add_modal.closeModal();
                        } else {
                            that.showErrorMessage(response.result);
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
                $('footer.modal-footer .error-message').text($.mage.__(message))
            }

        });

        return $.vendor.mod;
    }
);
