define(
    [
        'jquery',
        'Magento_Ui/js/modal/modal',
        'mage/url'
    ],
    function ($, modal, urlBuilder) {
        'use strict';

        $.widget('vendor.mod', {
            _create: function () {
                this._initElement();
                this._initErrorMessageSection()
            },

            _initErrorMessageSection: function () {
                $('footer.modal-footer').prepend(
                    '<div class="error-message" style="display: none"> </div>'
                )
            },

            _initElement: function () {
                var self = this;
                var options = {
                    type: 'popup',
                    title: $.mage.__('Move Product to'),
                    modalClass: 'pp-shopping-list',
                    responsive: true,
                    buttons: [{
                        text: jQuery.mage.__('Cancel'),
                        class: 'action secondary move-product-cancel',
                        click: function () {
                            this.closeModal();
                        }
                    }, {
                        text: jQuery.mage.__('Move'),
                        class: 'action primary move-product',
                        click: function () {
                            var selected = $('input[type=checkbox][name="selected[]"]:checked');
                            $('footer.modal-footer .error-message').hide();
                            if (!selected.length) {
                                self.showErrorMessage("Please select a shopping list to continue");
                            } else {
                                var data = [];
                                selected.each(function () {
                                    data.push(Number(this.value));
                                });

                                self.moveItem(data, $("input[name='item_id']").val(), this);
                            }
                        }
                    }]
                };

                /**
                 * Init modal move item with options above
                 */
                $(".moveotherlist-content").each(function (index, item) {
                    modal(options, $(item));
                })

                /**
                 * Handle open model when click button "Move Item"
                 */
                $(".moveotherlist").on("click", function () {
                    var itemId = $(this).data("item-id");
                    $("input[name='item_id']").val(itemId)

                    $(".input-list-name").val("");
                    $(".create-list-section").hide();
                    $(".wishlist-checkbox").prop("checked", false);
                    $("footer.modal-footer .error-message").hide();
                    $(self.generateSelector("moveotherlist-content", itemId)).modal('openModal');
                });

                /**
                 *  Handle show create list section
                 */
                $(".btn-create-list").on('click', function () {
                    var itemId = $(this).data("item-id");

                    $(self.generateSelector("create-list-section", itemId)).show();
                });

                /**
                 * Handle action submit create list
                 */
                $(".submit-create-list").on('click', function () {
                    var itemId = $(this).data("item-id");

                    var listName = $(self.generateSelector("input-list-name", itemId)).val();
                    self.createShoppingList(listName);

                });

                $(".btn-remove").on("click", function () {
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
                    window.location.href = $(this).data("remove-url")
                })
            },

            generateSelector : function (className, itemId) {
                return "." + className + "[data-item-id=" + itemId + "]"
            },

            /**
             *
             * @param name
             */
            createShoppingList : function (name) {
                var self = this;
                if (name === '') {
                    self.showErrorMessage("Shopping List name is empty!");
                } else {
                    var data = {
                        shopping_list_name : name
                    };

                    $("footer.modal-footer .error-message").hide();
                    $.ajax({
                        url: urlBuilder.build("wishlist/ajax/createlist"),
                        type: "POST",
                        data: data,
                        dataType: 'json',
                        showLoader: true,
                        success: function (response) {
                            if (Number(response.status) === 1) {
                                data = response.result;
                                $(".input-list-name").val("");
                                $(".create-list-section").hide();

                                $('<li/>', {
                                    "class": "checkbox-custom"
                                }).append($('<input/>', {
                                    type : "checkbox",
                                    class : "wishlist-checkbox",
                                    name : "selected[]",
                                    value : data.list_id,
                                    checked : true
                                })).append($('<label/>', {
                                    text : data.name
                                })).appendTo(".list-choice");

                            } else {
                                self.showErrorMessage(response.result);
                            }
                        },
                        error: function () {
                            self.showErrorMessage("An error occurred. Please refresh page and try again");
                        }
                    });
                }
            },

            moveItem : function (selected, itemId, moveModal) {
                var self = this;

                var data = {
                    shopping_list_ids: selected,
                    item_id: itemId
                };

                $("footer.modal-footer .error-message").hide();
                $.ajax({
                    url: urlBuilder.build("wishlist/ajax/moveitem"),
                    type: "POST",
                    data: data,
                    dataType: 'json',
                    showLoader: true,
                    success: function (response) {
                        if (Number(response.status) === 1) {
                            self.showSuccess(response.result, itemId);
                            $("#item_" + itemId).remove();
                            moveModal.closeModal();
                        } else {
                            self.showErrorMessage(response.result);
                        }
                    },
                    error: function () {
                        self.showErrorMessage("An error occurred. Please refresh page and try again");
                    }
                });

            },

            showSuccess : function (data, itemId) {
                var self = this;
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
                            $(".wishlist-checkbox").prop("checked", false);
                            $(".destination-list").empty();
                        }
                    }]
                };

                jQuery.each(data, function (key, val) {
                    var element = '<li><a href="' + val.url + '">' + val.name + '</a></li>';
                    $(self.generateSelector("destination-list", itemId)).append(element);
                });

                $(self.generateSelector("success-modal", itemId)).modal(options).modal('openModal');
            },

            showErrorMessage: function (message) {
                $('footer.modal-footer .error-message').show().text($.mage.__(message))
            }

        });

        return $.vendor.mod;
    }
);
