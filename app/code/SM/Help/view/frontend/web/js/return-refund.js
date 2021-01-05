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
            modalClass: 'modal-popup-small select-order-list',
            title: $.mage.__('Select Order'),
            buttons: [],
        },
            options1 = {
                type: 'popup',
                responsive: true,
                innerScroll: true,
                modalClass: 'modal-popup-small popup-return-refund-order',
                title: $.mage.__('Select Products'),
                buttons: [],
        },
            returnRefundOrder = $('#return-refund-order'),
            helpOrderList = $('#help-order-list-order'),
            helpOrderListReturn = $('#help-order-list-refund'),
            helpOrderListPopup = modal(options, helpOrderList),
            helpOrderListPopupReturn = modal(options, helpOrderListReturn),
            url = $('#getOrderProduct').text(),
            getProductById = $('#getOrderProductById').text(),
            returnRefundProduct = $("#return-refund-product"),
            productSelected = $('#selected-product'),
            btnSelectProduct = $("#button-select-product"),
            myOrderId = $("#myOrderId").text(),
            returnRefundId = $("#returnRefundId").text();


        btnSelectProduct.on('click',function () {
            let is_received = $('[name="is_received"]:checked').val();
            if (is_received == "return") {
                helpOrderListPopupReturn.openModal();
            } else {
                helpOrderListPopup.openModal();
            }
        });

        $(".order-track-box").on('click',function () {
            let cateValue = $('input[name="category"]').val();
            if (cateValue == returnRefundId || cateValue == myOrderId) {
                let orderId = $(this).attr('id');

                productSelected.hide();
                $("#order-product-selected-all").prop("checked", false);
                btnSelectProduct.show();
                returnRefundProduct.trigger("processStart");

                setTimeout(function () {
                    $.ajax({
                        context: '#return-refund-product',
                        url: url,
                        type: "POST",
                        data: {orderId : orderId},
                    }).done(function (data) {
                        let returnRefundOrderPopup = modal(options1, returnRefundOrder);
                        if (returnRefundProduct.text().length > 0) {
                            returnRefundProduct.html("");
                        }
                        returnRefundProduct.html(data.output);
                        returnRefundProduct.trigger("processStop");
                        helpOrderListPopup.closeModal();
                        returnRefundOrderPopup.openModal();
                        $("#back-save").on('click',function () {
                            if ($('input[name="selected-product"]').is(":checked") === false) {
                                btnSelectProduct.show();
                                productSelected.hide();
                            } else {
                                btnSelectProduct.hide();
                                productSelected.show();
                            }
                            let productIDs = $('input[name="selected-product"]:checked').map(function () {
                                return $(this).val();
                            }).get();
                            productSelected.trigger("processStart");
                            setTimeout(function () {
                                $.ajax({
                                    context: productSelected,
                                    url: getProductById,
                                    type: "POST",
                                    data: {
                                        orderId : orderId,
                                        productIDs : {productIDs}
                                    },
                                }).done(function (data) {
                                    if (productSelected.text().length > 0) {
                                        productSelected.empty();
                                    }
                                    productSelected.html(data.output);
                                    productSelected.trigger("processStop");
                                    returnRefundOrderPopup.closeModal();
                                    return true;
                                });
                            },2000);
                        });

                        $("#back-cancel").on('click',function () {
                            returnRefundOrderPopup.closeModal();
                        });
                        return true;
                    });
                },2000);

                $("#order-product-selected-all").click(function () {
                    $('input[name="selected-product"]:checkbox').not(this).prop('checked', this.checked);
                });
            }
        });
    }
);
