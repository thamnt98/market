define(
    [
        'jquery',
        'Magento_Ui/js/modal/modal',
        'Magento_Customer/js/customer-data',
        'SM_ShoppingList/js/alert',
        'Magento_Ui/js/lib/view/utils/async',
        'mage/url'
    ],
    function ($, modal, customerData, alertModal, async, urlBuilder) {
        'use strict';

        return function (config) {
            window.eventCreate = window.eventCreate || {};
            var selector = ".towishlist";
            async.async(selector ,function () {
                if (!eventCreate[selector]) {
                    eventCreate[selector] = 0;
                }

                let current = $($(selector)[eventCreate[selector]]);
                $(current).click(function (e) {
                    var itemId = Number($(this).data("product-id"));
                    addItems(itemId);
                });
                eventCreate[selector]++;

                //remove item
                // $(selectorRemove).click(function (e) {
                //     removeItemFromPLP(config.product_id);
                // });
            });

            function addItems(product_id)
            {
                var data = {
                    product_id: product_id,
                    show_toast: true
                };

                $.ajax({
                    url: urlBuilder.build("wishlist/ajax/additems"),
                    data: data,
                    type: 'post',
                    dataType: 'json',
                    showLoader: true,
                    success: function (data) {
                        if (data.status != 1) {
                            alertModal.showExist(data.result);
                        }
                    },
                    error: function () {
                        alertModal.showError("An error occurred. Please refresh page and try again");
                    }
                });

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
                    url: urlBuilder.build("wishlist/ajax/removeitem"),
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
