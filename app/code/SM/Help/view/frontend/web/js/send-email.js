/**
 * @category SM
 * @package SM_Help
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author      Chinhvd <chinhvd@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */
define(
    [
        'jquery',
        'Magento_Ui/js/modal/modal',
        'mage/translate'
    ],
    function (
        $,
        modal
    ) {
        let options = {
            type: 'popup',
            responsive: true,
            innerScroll: true,
            modalClass: 'modal-popup-xsmall',
            buttons: [],
            /*opened: function ($Event) {
                $('.modal-header button.action-close', $Event.srcElement).hide();
            },*/
        },
            popup = modal(options, $('.message-notification')),
            url   = $('#sendEmailHelp').text(),
            helpTextarea = $("#help-textarea"),
            form = $('#help-contact-us'),
            myOrderId = $("#myOrderId").text(),
            returnRefundId = $("#returnRefundId").text(),
            selectStore = $("#select-store"),
            selectedProduct = $("#selected-product"),
            checkRadio = $("#radio-custom");

        $("#contact-us-submit").on('click',function (event) {
            if ($.trim(helpTextarea.val()) <= 0) {
                if (helpTextarea.hasClass("mage-error")) {
                    return false;
                } else {
                    event.preventDefault();
                    helpTextarea.addClass('mage-error');
                    html = $.mage.__('This field is required');
                    helpTextarea.after('<div for="help-textarea" generated="true" class="mage-error" id="help-textarea-error">' + html + '</div>');
                    helpTextarea.focus();
                    return false;
                }
            } else {
                helpTextarea.removeClass('mage-error');
                $("#help-textarea-error").remove();
                if (typeof dataLayerSourceObjects !== 'undefined') {
                    window.dataLayer = window.dataLayer || [];
                    window.dataLayer.push({
                        'event': 'submit_help_form',
                        'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                        'userID': dataLayerSourceObjects.customer.userID,
                        'customerID': dataLayerSourceObjects.customer.customerID,
                        'customerType': dataLayerSourceObjects.customer.customerType,
                        'loyalty': dataLayerSourceObjects.customer.loyalty,
                        'customerStatus': dataLayerSourceObjects.customer.customerStatus,
                        'loginType': dataLayerSourceObjects.customer.loginType,
                        'store_name': dataLayerSourceObjects.customer.storeName,
                        'store_ID': dataLayerSourceObjects.customer.storeID,
                        'category': $.trim($('.help-contactus .action.toggle.category').text()),
                        'message': $.trim(helpTextarea.val())
                    })
                }
            }

            let val = $('input[name="category"]').val();
            if (val === returnRefundId || val === myOrderId) {
                if ($('input[name="is_received"]').is(":checked") === false) {
                    event.preventDefault();
                    html = $.mage.__('This field is required');
                    checkRadio.after('<div for="help-textarea" generated="true" class="mage-error" id="radio-custom-error">' + html + '</div>');
                    checkRadio.focus();
                    return false;
                } else {
                    $("#radio-custom-error").remove();
                }

                if ($('div[id="product-track-box"]').is(":visible")) {
                    selectedProduct.removeClass('mage-error');
                    $("#selected-product-error").remove();
                } else {
                    if (selectedProduct.hasClass("mage-error")) {
                        return false;
                    } else {
                        event.preventDefault();
                        selectedProduct.addClass('mage-error');
                        html = $.mage.__('This field is required');
                        selectedProduct.after('<div for="selected-product" generated="true" class="mage-error" id="selected-product-error">' + html + '</div>');
                        selectedProduct.focus();
                        return false;
                    }
                }

                if ($('div[id="select-store"]').is(":visible") && val === returnRefundId) {
                    if ($('input[name="store"]').val() === '') {
                        if (selectStore.hasClass("mage-error")) {
                            return false;
                        } else {
                            event.preventDefault();
                            selectStore.addClass('mage-error');
                            html = $.mage.__('This field is required');
                            selectStore.after('<div for="select-store" generated="true" class="mage-error" id="select-store-error">' + html + '</div>');
                            selectStore.focus();
                            return false;
                        }
                    } else {
                        selectStore.removeClass('mage-error');
                        $("#select-store-error").remove();
                    }
                }
            }

            form[0].checkValidity();
            if (form[0].reportValidity()) {
                event.preventDefault();
                $(".message-notification").modal("openModal");
                // Create an FormData object
                var data = new FormData(form[0]);
                data.append('file', $('input[type=file]')[0].files[0]);
                //Send help email
                $.ajaxSetup({'cache':true});
                if ($('#checkSendEmail').is(":checked")) {
                    $.ajax({
                        type: "POST",
                        enctype: 'multipart/form-data',
                        url : url,
                        processData: false,
                        contentType: false,
                        dataType: "json",
                        data: data,
                        success: function () {
                        }
                    });
                }
            }

        });

        let helpReturnRefund = $(".help-return-refund");
        let selectYes = $('.return-refund-select-yes'),
            selectNo  = $('.return-refund-select-no'),
            productSelected = $('#selected-product'),
            btnSelectProduct = $("#button-select-product");

        $('ul[data-role="sorter"]').find('li').each(function () {
            $(this).on('click',function () {
                $('.action.toggle.category').find('span').text($(this).text());
                $('input[name="category"]').val($(this).attr("data-value"));
                let value = $(this).attr("data-value");

                if(productSelected.is(":visible")){
                    productSelected.hide();
                    btnSelectProduct.show()
                }

                if ( $('#yes').is(':checked') && value === returnRefundId) {
                    selectYes.show();
                    selectNo.show();
                } else if ($('#yes').is(':checked') && value === myOrderId) {
                    selectNo.show();
                    selectYes.hide();
                } else {
                    selectNo.show();
                    selectYes.hide();
                }

                if (value === returnRefundId) {
                    helpReturnRefund.show();
                    helpTextarea.attr('placeholder',$.mage.__('Example: This product came in the wrong size (100 ml). It\'s supposed to be 500 ml.'));
                } else if (value === myOrderId) {
                    helpReturnRefund.show();
                    selectYes.hide();
                    helpTextarea.attr('placeholder',$.mage.__('Example: I need help to track my order because the feature doesn\'t seem to work.'));
                } else {
                    helpReturnRefund.hide();
                    helpTextarea.attr('placeholder',$.mage.__('Example: I need help to track my order because the feature doesn\'t seem to work.'));
                }
                $('input[type="radio"]').click(function () {
                    if ( $('#yes').is(':checked') && value === returnRefundId) {
                        selectYes.show();
                        selectNo.show();
                    } else if ($('#yes').is(':checked') && value === myOrderId) {
                        selectNo.show();
                        selectYes.hide();
                    } else {
                        selectNo.show();
                        selectYes.hide();
                    }
                });
            })
        });

        $('.action.toggle.category').click(function () {
            $('ul[data-role="sorter"]').css('display','block');
        });

        $('ul[data-role="sorter"]').on('mouseleave',function () {
            $(this).css('display','none');
        });

        $('.action.toggle.store').click(function () {
            $('ul[data-role="store"]').css('display','block');
        });

        $('ul[data-role="store"]').find('li').each(function () {
            $(this).on('click',function () {
                $('.action.toggle.store').find('span').text($(this).text());
                $('input[name="store"]').val($(this).attr("data-value"));
                $('ul[data-role="store"]').css('display','none');
            });
        });

        $('ul[data-role="store"]').on('mouseleave',function () {
            $(this).css('display','none');
        });

        helpTextarea.keyup(function () {
            $('.typ-word').text($(this).val().length + '/500');
        });
    }
);
