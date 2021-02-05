define([
    'jquery',
    'Magento_Ui/js/modal/confirm',
    'Magento_Checkout/js/model/cart/cache',
    'Amasty_Conditions/js/action/recollect-totals',
    'Magento_Customer/js/customer-data',
    'gtmCheckout',
    'SM_GTM/js/gtm/sm-gtm-cart-collect-data',
    'mage/translate',
    'loader'
], function (
    $,
    confirm,
    cartCache,
    recollect,
    customerData,
    gtmCheckout,
    gtmCollectData
) {
    /**
     * process selected all
     */
    const cartContainer = $(".cart-container"),
        updateController = BASE_URL + "transcheckout/cart/update",
        updateItemController = BASE_URL + "transcheckout/cart/updateitemqty",
        doneTypingInterval = 1000;
    let timerFunction;

    /**
     * check product near by limiting
     */
    $('#form-validate').ready(function () {
        $('.increase-qty').each(function () {
            let itemId = $(this).attr('itemId');
            let itemStockQty = $(this).attr('itemstock');
            let itemQty = $('#cart-qty-'+ itemId).val();

            if (parseInt(itemQty) >= parseInt(itemStockQty)) {
                $('.out-stock-'+ itemId).show();
                $('.instock-'+ itemId).hide();
            } else {
                $('.out-stock-'+ itemId).hide();
                $('.instock-'+ itemId).show();
            }
        });
    });

    cartContainer.on("click", "#selected-all", function () {
        let checked = ($(this).is(":checked"))? 1 : 0,
            $loadingMark = $(".loading-mask");

        /**
         * update correct checked
         */
        $('.item-checked').each(function () {
            let isCheckedAll = $('#selected-all').is(":checked");
            $(this).attr('checked', isCheckedAll);
        });

        $loadingMark.show();

        setTimeout(function () {
            $loadingMark.hide();
        }, 1500);

        $.ajax({
            url : updateController,
            dataType: 'json',
            type: 'POST',
            data : {"selected-all": checked},
            success : function (response) {
                if (response.status === 'success') {

                    /**
                     * reload totals
                     */
                    cartCache.set('totals',null);
                    recollect(true);
                }

                if (response.reload === true) {
                    reloadPageAfterChangeAddress(response);
                }
            },
            error : function () {
                /**
                 * rollback checked
                 */
                $('#selected-all').attr('checked', !checked);
            }
        })
    });

    /**
     * process unselected all checked
     */
    cartContainer.on("click", "#remove-selected-items", function () {
        let removeIds = [];
        /**
         * get items need to unselect
         */
        $('.item-checked').each(function () {
            if ($(this).is(":checked")) {
                removeIds.push($(this).attr('name'));
            }
        });
        confirm({
            title: $.mage.__('Remove Item(s)'),
            content: $.mage.__('Are you sure you want to remove the item(s)?'),
            actions: {
                confirm: function () {

                    const form = $("form[name='remove_selected_item']");
                    form.children('input[name="remove_ids"]').val(removeIds);
                    form.children('button').trigger('click');

                    setTimeout(function () {
                        //Remove selected items GTM
                        gtmCollectData.removeItemSelected();
                    }, 1000);
                },
                cancel: function (){},
                always: function (){}
            },
            buttons: [{
                text: $.mage.__('Cancel'),
                class: 'action-secondary action-dismiss',

                /**
                 * Click handler.
                 */
                click: function (event) {
                    this.closeModal(event);
                }
            }, {
                text: $.mage.__('Remove'),
                class: 'action-primary action-accept',

                /**
                 * Click handler.
                 */
                click: function (event) {
                    this.closeModal(event, true);
                }
            }]
        });

    });

    /**
     * process selected single item
     */
    cartContainer.on("click", ".item-checked", function () {
        let itemId = $(this).attr('name'),
            itemValue = ($(this).is(":checked"))? 1 : 0,
            itemPost = itemId + '=' + itemValue,
            nooneSelect = true,
            self = this,
            current = $(this),
            $loadingMark = $(".loading-mask");

        $loadingMark.show();

        let count = 0;
        $('.item-checked').each(function () {
            if ($(this).is(":checked") == true) {
                count++;
            }
        });

        if (count == $('.item-checked').length) {
            $('#selected-all').attr('checked', true);
        } else {
            $('#selected-all').attr('checked', false)
        }

        if (itemValue == 0) {
            current.prop("checked", false).removeAttr('checked');
        } else if (itemValue == 1) {
            current.attr('checked','true');
        }

        setTimeout(function () {
            $loadingMark.hide();
        },1500);

        $.ajax({
            url : updateController,
            dataType: 'json',
            type: 'POST',
            data : {"itemId": itemPost},
            success : function (response) {
                if (response.status === 'success') {
                    /**
                     * reload totals
                     */
                    cartCache.set('totals',null);
                    recollect(true);
                }
                if (response.reload === true) {
                    reloadPageAfterChangeAddress(response);
                }
            },
            error : function () {
            }
        })
    });

    cartContainer.on("click", ".increase-qty", function () {

        let $this = $(this),
            itemId = $this.attr('itemId'),
            elementId = $('a#add-cart-item-' + itemId),
            itemStockQty = $this.attr('itemstock'),
            itemQty = $('#cart-qty-'+ itemId);

        if (elementId.is('[readonly]')) {
            return this;
        }
        const itemQtyUpdate = parseInt(itemQty.val())+1;
        itemQty.val(itemQtyUpdate);

        let form = $("#form-validate");
        let downElementId = $('a#subtract-cart-item-' + itemId);

        /**
         * change background & disable action +/-
         */
        if (parseInt(itemQty.val()) >= 99 || parseInt(itemQty.val()) >= itemStockQty) {
            elementId.css("background", "#ccc");
            elementId.attr('readonly', true);
            elementId.addClass('disabled', true);
        } else {
            downElementId.css("background", "#f7b500");
            downElementId.attr('readonly', false);
            downElementId.removeClass('disabled', false);
        }

        clearTimeout(timerFunction);
        timerFunction = setTimeout(function () {
            if (form !== undefined) {
                const data = {'item_id': itemId, 'item_qty': itemQtyUpdate},
                    $parentCol = $this.parents('.col.qty'),
                    $loader = $parentCol.find('.action-primary-loader'),
                    isChecked = $('#itemid-' + itemId).is(":checked");
                $loader.css('display', 'block');

                setTimeout(function () {
                    $loader.css('display', 'none');
                }, 1500);
                $.ajax({
                    url: updateItemController,
                    data: data,
                    type: 'POST',
                    showLoader: false,
                    success: function (res) {
                        if (res.success === true) {
                            $parentCol.next('.col.subtotal').html(res.row_total);
                            /**
                             * show tooltip when having maximum
                             */
                            if (parseInt(itemQty.val()) >= parseInt(itemStockQty)) {
                                $('.out-stock-'+ itemId).show();
                                $('.instock-'+ itemId).hide();
                            } else {
                                $('.out-stock-'+ itemId).hide();
                                $('.instock-'+ itemId).show();
                            }

                            // The totals summary block reloading
                            if (isChecked) {
                                recollect(true);
                            }

                            var messages = $.cookieStorage.get('mage-messages');
                            if (!_.isEmpty(messages)) {
                                customerData.set('messages', {messages: messages});
                                $.cookieStorage.set('mage-messages', '');
                            }

                            //Increase quantity GTM
                            let productId = $(this).parents().eq(4).find('[data-gtm-event="removeFromCart"]').attr('data-gtm-product-id');
                            gtmCollectData.collectData('addToCart', productId, itemQty.val());
                        } else {
                            itemQty.val((itemQtyUpdate-1));
                            console.log(res.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        let err = eval("(" + xhr.responseText + ")");
                        console.log(err.Message);
                    }
                });
            }

        }, doneTypingInterval);

    });

    cartContainer.on("click", ".decrease-qty", function () {

        const $this = $(this),
            itemId =$this .attr('itemId'),
            itemQty = $('#cart-qty-'+ itemId),
            elementId = $('a#subtract-cart-item-' + itemId),
            plusElementId = $('a#add-cart-item-' + itemId),
            form = $("#form-validate"),
            isChecked = $('#itemid-' + itemId).is(":checked");

        if (itemQty.val() <= 0 || elementId.is('[readonly]')) {
            return this;
        }

        const itemQtyUpdate = parseInt(itemQty.val()) - 1;
        itemQty.val(itemQtyUpdate);

        if (parseInt(itemQty.val()) <= 1) {
            elementId.css("background", "#ccc");
            elementId.attr('readonly', true);
            elementId.addClass('disabled', false);
        } else {
            plusElementId.css("background", "#f7b500");
            plusElementId.attr('readonly', false);
            plusElementId.removeClass('disabled', false);
        }
        clearTimeout(timerFunction);
        timerFunction = setTimeout(function () {
            if (form !== undefined) {
                const data = {'item_id': itemId, 'item_qty': itemQtyUpdate},
                $parentCol = $this.parents('.col.qty'),
                $loader = $parentCol.find('.action-primary-loader');
                $loader.css('display', 'block');

                setTimeout(function () {
                    $loader.css('display', 'none');
                }, 1500);

                $.ajax({
                    url: updateItemController,
                    data: data,
                    type: 'POST',
                    showLoader: false,
                    success: function (res) {
                        if (res.success === true) {
                            $parentCol.next('.col.subtotal').html(res.row_total);
                            // The totals summary block reloading
                            if (isChecked) {
                                recollect(true);
                            }
                            //Decrease quantity GTM
                            let productId = $(this).parents().eq(4).find('[data-gtm-event="removeFromCart"]').attr('data-gtm-product-id');
                            gtmCollectData.collectData('removeFromCart', productId, itemQty.val());
                        } else {
                            itemQty.val((itemQtyUpdate + 1));
                            console.log(res.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        let err = eval("(" + xhr.responseText + ")");
                        console.log(err.Message);
                    }
                });
            }
        }, doneTypingInterval);
    });

    /**
     * init subtotal text, remove tax, grand total
     * @todo Remove use setInterval
     */
    setInterval(function () {
        let text = $('.totals .sub >  .mark').html();
        if (text ==  $.mage.__('Subtotal')) {
            $('.totals-tax').remove();
        }
    }, 1000);

    function reloadPageAfterChangeAddress(response)
    {
        let customerMessages = customerData.get('messages')() || {},
            messages = customerMessages.messages || [];

        messages.push({
            text: response.reload_message,
            type: 'success'
        });

        customerMessages.messages = messages;
        customerData.set('messages', customerMessages);

        // The mini cart reloading
        customerData.reload(['cart'], true);

        //Wait 3 second then reload page
        setTimeout(function () {
            window.location.reload();
        }, 3000);
    }
});
