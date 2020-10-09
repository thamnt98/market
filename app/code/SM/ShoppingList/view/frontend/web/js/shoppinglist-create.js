define(
    [
        'jquery',
        'Magento_Ui/js/modal/modal',
        'Magento_Customer/js/customer-data',
        'SM_ShoppingList/js/alert'
    ],
    function ($, modal, customerData, alertModal) {
        'use strict';

        $.widget('vendor.mod', {
            _create: function () {
                var sections = ['wishlist', 'cart'];
                customerData.invalidate(sections);
                customerData.reload(sections, true);
                this._initElement();
            },
            _initElement: function () {
                var that = this;
                var options = {
                    type: 'popup',
                    responsive: true,
                    modalClass: 'pp-shopping-list',
                    title: $.mage.__('Create a List'),
                    buttons: [
                        {
                            text: jQuery.mage.__('Cancel'),
                            class: 'action secondary create-list-cancel',
                            click: function () {
                                this.closeModal();
                            }
                        },
                        {
                            text: jQuery.mage.__('Create'),
                            class: 'action primary create-list-submit',
                            click: function () {
                                that.createShoppingList($("#create-list-name").val());
                            }
                        }]
                };
                var openModalCreate = modal(options, $('#create-wishlist-modal'));

                $("#button-open-create-modal").on('click', function () {
                    $("#create-list-name").val("");
                    $('#create-wishlist-modal').modal('openModal');
                });
            },
            /**
             *
             * @param name
             */
            createShoppingList : function (name) {
                if (name === "") {
                    alertModal.showError("Shopping List name is empty!");
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
                                if (typeof dataLayerSourceObjects !== 'undefined') {
                                    window.dataLayer = window.dataLayer || [];
                                    let dt = new Date();
                                    let time = $.datepicker.formatDate('dd/mm/yy', dt) + ' ' + dt.getHours() + ":" + dt.getMinutes() + ":" + dt.getSeconds();
                                    window.dataLayer.push({
                                        'event': 'create_shoppingList',
                                        'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                                        'userID': dataLayerSourceObjects.customer.userID,
                                        'customerID': dataLayerSourceObjects.customer.customerID,
                                        'customerType': dataLayerSourceObjects.customer.customerType,
                                        'loyalty': dataLayerSourceObjects.customer.loyalty,
                                        'customerStatus': dataLayerSourceObjects.customer.customerStatus,
                                        'loginType': dataLayerSourceObjects.customer.loginType,
                                        'timestamp': time,
                                        'shoppingList_name': response.result.name
                                    })
                                }
                                $("#form-create-list").submit();
                            } else {
                                alertModal.showError(response.result);
                            }
                        }
                    });
                }

            }
        });

        return $.vendor.mod;
    }
);
