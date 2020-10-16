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
                    updateLabelSubtotalSelected();
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
            title: $.mage.__('Delete Items'),
            content: $.mage.__('Would you like to remove items?'),
            actions: {
                confirm: function () {
                    //Remove selected items GTM
                    gtmCollectData.collectData();

                    const form = $("form[name='remove_selected_item']");
                    form.children('input[name="remove_ids"]').val(removeIds);
                    form.children('button').trigger('click');
                },
                cancel: function (){},
                always: function (){}
            }
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
                    updateLabelSubtotalSelected();
                    if (itemValue == 0) {
                        current.prop( "checked", false ).removeAttr('checked');
                    } else if (itemValue == 1) {
                        current.attr('checked','true');
                    }
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
                    updateLabelSubtotalSelected();

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
        //Decrease quantity GTM
        gtmCollectData.collectData();

        let itemId = $(this).attr('itemId');
        let itemQty = $('#cart-qty-'+ itemId);
        let elementId = $('a#subtract-cart-item-' + itemId);
        let plusElementId = $('a#add-cart-item-' + itemId);
        let form = $("#form-validate");

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
                    updateLabelSubtotalSelected();
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
            updateLabelSubtotalSelected();
            $('.totals-tax').remove();
        }
    }, 1000);

    /**
     * update subtotal text
     */
    function updateLabelSubtotalSelected()
    {
        let i= 0;
        $('.item-checked').each(function () {
            if ($(this).is(":checked")) {
                let id = $(this).attr('name');
                let qty = parseInt($('#cart-qty-'+ id).val());
                i +=  qty;
            }
        });
        if (i <= 1) {
            $('.totals .sub >  .mark').html($.mage.__('Subtotal <span class="totals-count-items"> (%1 item) </span>').replace('%1', i));
        } else {
            $('.totals .sub >  .mark').html($.mage.__('Subtotal <span class="totals-count-items"> (%1 items) </span>').replace('%1', i));
        }
    }
});
