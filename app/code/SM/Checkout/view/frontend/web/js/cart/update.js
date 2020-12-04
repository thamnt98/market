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
        updateController = BASE_URL + "transcheckout/cart/update";
    /**
     * check product near by limiting
     */
    $('#form-validate').ready(function() {
        $('.increase-qty').each(function(){
            let itemId = $(this).attr('itemId');
            let itemStockQty = $(this).attr('itemstock');
            let itemQty = $('#cart-qty-'+ itemId).val();

            if(parseInt(itemQty) >= parseInt(itemStockQty)){
                $('.out-stock-'+ itemId).show();
                $('.instock-'+ itemId).hide();
            }else{
                $('.out-stock-'+ itemId).hide();
                $('.instock-'+ itemId).show();
            }
        });
    });
    cartContainer.on("click", "#selected-all", function () {
        let checked = ($(this).is(":checked"))? 1 : 0;
        let self = this;
        $(".loading-mask").show();

        $.ajax({
            url : updateController,
            dataType: 'json',
            type: 'POST',
            data : {"selected-all": checked},
            success : function (response) {

                if (response.status === 'success') {
                    /**
                     * update correct checked
                     */
                    $('.item-checked').each(function () {
                        let isCheckedAll = $('#selected-all').is(":checked");
                        $(this).attr('checked', isCheckedAll);
                    });
                    $(".loading-mask").hide();
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
                $(".loading-mask").hide();
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
                    //Remove selected items GTM
                    gtmCollectData.removeItemSelected();

                    const form = $("form[name='remove_selected_item']");
                    form.children('input[name="remove_ids"]').val(removeIds);
                    form.children('button').trigger('click');
                    customerData.invalidate(['cart']);
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
        let itemId = $(this).attr('name');
        let itemValue = ($(this).is(":checked"))? 1 : 0;
        let itemPost = itemId + '=' + itemValue;
        var nooneSelect = true;
        let self = this;
        var current = $(this);
        $(".loading-mask").show();

        $.ajax({
            url : updateController,
            dataType: 'json',
            type: 'POST',
            data : {"itemId": itemPost},
            success : function (response) {
                if (response.status === 'success') {
                    let count = 0;
                    $('.item-checked').each(function(){
                        if ($(this).is(":checked") == true) {
                            count++;
                        }
                    });
                    if (count == $('.item-checked').length) {
                        $('#selected-all').attr('checked', true);
                    } else {
                        $('#selected-all').attr('checked', false)
                    }

                    $(".loading-mask").hide();
                    /**
                     * reload totals
                     */
                    cartCache.set('totals',null);
                    recollect(true);
                    if (itemValue == 0) {
                        current.prop( "checked", false ).removeAttr('checked');
                    } else if (itemValue == 1) {
                        current.attr('checked','true');
                    }
                }

                if (response.reload === true) {
                    reloadPageAfterChangeAddress(response);
                }
            },
            error : function () {
                $(".loading-mask").hide();
            }
        })
    });

    cartContainer.on("click", ".increase-qty", function () {

        let itemId = $(this).attr('itemId');
        let itemStockQty = $(this).attr('itemstock');
        let itemQty = $('#cart-qty-'+ itemId);
        let form = $("#form-validate");
        let elementId = $('a#add-cart-item-' + itemId);
        let downElementId = $('a#subtract-cart-item-' + itemId);

        //Increase quantity GTM
        let productId = $(this).parents().eq(4).find('[data-gtm-event="removeFromCart"]').attr('data-gtm-product-id');
        gtmCollectData.collectData('addToCart', productId, itemQty.val());

        if (elementId.is('[readonly]')) {
            return this;
        }

        itemQty.val(parseInt(itemQty.val())+1);
        /**
         * change background & disable action +/-
         */
        if (parseInt(itemQty.val()) >= 99 || parseInt(itemQty.val()) >= itemStockQty) {
            elementId.css("background", "#ccc");
            elementId.attr('readonly', true);
        } else {
            downElementId.css("background", "#f7b500");
            downElementId.attr('readonly', false);
        }

        if (form !== undefined) {
            $.ajax({
                url: form.attr('action'),
                data: form.serialize(),
                showLoader: true,
                success: function (res) {
                    let parsedResponse = $.parseHTML(res);
                    let result = $(parsedResponse).find("#form-validate");
                    let sections = ['cart'];

                    $("#form-validate").replaceWith(result);
                    /**
                     * show tooltip when having maximum
                     */
                    if(parseInt(itemQty.val()) >= parseInt(itemStockQty)){
                        $('.out-stock-'+ itemId).show();
                        $('.instock-'+ itemId).hide();
                    }else{
                        $('.out-stock-'+ itemId).hide();
                        $('.instock-'+ itemId).show();
                    }

                    // The mini cart reloading
                    customerData.reload(sections, true);

                    // The totals summary block reloading
                    recollect(true);

                    var messages = $.cookieStorage.get('mage-messages');
                    if (!_.isEmpty(messages)) {
                        customerData.set('messages', {messages: messages});
                        $.cookieStorage.set('mage-messages', '');
                    }

                },
                error: function (xhr, status, error) {
                    let err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                }
            });
        }
    });

    cartContainer.on("click", ".decrease-qty", function () {

        let itemId = $(this).attr('itemId');
        let itemQty = $('#cart-qty-'+ itemId);
        let elementId = $('a#subtract-cart-item-' + itemId);
        let plusElementId = $('a#add-cart-item-' + itemId);
        let form = $("#form-validate");

        //Decrease quantity GTM
        let productId = $(this).parents().eq(4).find('[data-gtm-event="removeFromCart"]').attr('data-gtm-product-id');
        gtmCollectData.collectData('removeFromCart', productId, itemQty.val());

        if (elementId.is('[readonly]')) {
            return this;
        }

        if (itemQty.val() > 1) {
            itemQty.val(itemQty.val()-1);
        }

        if (parseInt(itemQty.val()) <= 1) {
            elementId.css("background", "#ccc");
            elementId.attr('readonly', true);
        } else {
            plusElementId.css("background", "#f7b500");
            plusElementId.attr('readonly', false);
        }

        if (form !== undefined) {
            $.ajax({
                url: form.attr('action'),
                data: form.serialize(),
                showLoader: true,
                success: function (res) {
                    let parsedResponse = $.parseHTML(res);
                    let result = $(parsedResponse).find("#form-validate");
                    let sections = ['cart'];
                    $("#form-validate").replaceWith(result);

                    // The mini cart reloading
                    customerData.reload(sections, true);

                    // The totals summary block reloading

                    recollect(true);
                },
                error: function (xhr, status, error) {
                    let err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                }
            });
        }
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

    function reloadPageAfterChangeAddress(response) {
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
