define(
    [
        'jquery',
        'Magento_Ui/js/modal/modal',
        'Magento_Customer/js/customer-data',
        'SM_ShoppingList/js/alert',
        'Magento_Ui/js/lib/view/utils/async'
    ],
    function ($, modal, customerData, alertModal, async) {
        'use strict';

        return function (config) {
            if (!config.my_favorite_id) {
                return
            }

            /** check item has added*/
            checkItemHasAdded(config.product_id, config.is_added_shopping_list);

            window.eventCreate = window.eventCreate || {};
            var selector = "#shoppinglist-add-" + config.product_id,
                selectorRemove = "#shoppinglist-remove-" + config.product_id;
            async.async(selector ,function () {
                if (!eventCreate[selector]) {
                    eventCreate[selector] = 0;
                }

                let current = $($(selector)[eventCreate[selector]]);
                $(current).click(function (e) {
                    let id = $(this).attr("id");
                    var itemId = Number(id.replace("shoppinglist-add-", ""));
                    addItems(itemId);
                });
                eventCreate[selector]++;

                //remove item
                $(selectorRemove).click(function (e) {
                    removeItemFromPLP(config.product_id);
                });
            });

            function addItems(product_id)
            {
                var data = {
                    product_id: product_id,
                    store_id: config.store_id,
                    show_toast: true
                };
                var itemAdd = $('#shoppinglist-add-' + product_id),
                    itemRemove = $('#shoppinglist-remove-' + product_id);

                $.ajax({
                    url: config.add_item_url,
                    data: data,
                    type: 'post',
                    dataType: 'json',
                    showLoader: true,
                    success: function (data) {
                        if (data.status == 1) {
                            itemAdd.hide();
                            itemRemove.show();
                        } else {
                            alertModal.showExist(data.result);
                        }
                    },
                    error: function () {
                        alertModal.showError("An error occurred. Please refresh page and try again");
                    }
                });

            }

            /**
             * check item has added to show action
             * */
            function checkItemHasAdded(productId, is_added_shopping_list) {
                var itemAdd = $('#shoppinglist-add-' + productId),
                    itemRemove = $('#shoppinglist-remove-' + productId);

                if (is_added_shopping_list === 'true') {
                    itemAdd.hide();
                    itemRemove.show();
                }
            }

            /**
             * remove item from product list page
             * */
            function removeItemFromPLP(productId) {
                var itemAdd = $('#shoppinglist-add-' + productId),
                    itemRemove = $('#shoppinglist-remove-' + productId);

                var sections = ['wishlist', 'messages'];

                var data = {product_id: productId};
                $.ajax({
                    url: config.remove_item_url,
                    data: data,
                    type: 'post',
                    dataType: 'json',
                    showLoader: true,
                    success: function (result) {
                        if (result) {
                            itemAdd.show();
                            itemRemove.hide();
                            customerData.invalidate(sections);
                            customerData.reload(sections, true);
                        }
                    }
                });
            }
        };


    }
);
