define(
    [
        'jquery',
        'Magento_Ui/js/lib/view/utils/async',
        'Magento_Catalog/js/price-utils'
    ],
    function ($, async, priceUtils) {
        "use strict";
        const TYPE_BILL = "electricity_bill";
        const TYPE_TOKEN = "electricity_token";

        var current = TYPE_TOKEN;
        var radioBill = $("#radio-bill");
        var radioToken = $("#radio-token");

        var input = $("#input-number");
        var errorMsg = $("#error-message");
        var operatorIcon = $("#operator-icon");
        var btnBuy = $("#btn-buy-product");

        var typingTimer;
        var doneTypingInterval = 1500;  //time in ms (3 seconds)
        var productSelect = $('input:radio.product-item');
        var priceWrapper = $("#product-price-wrapper");
        var customerInformationToken = $("#customer-information-token");
        var customerInformation = $("#customer-information");
        var inquireError = $("#inquire-error");
        var productPriceTotal = $("#product-price-value");

        var formCurrentType = $("#form-current-type");
        var regexDigits = new RegExp("^[0-9]*$");

        var fieldProductName = $("#selected-product-name");
        var fieldProductPrice = $("#selected-product-price");
        var fieldProductPriceValue = $("#selected-product-price-value");
        var fieldProductIdVendor = $("#selected-product-id-vendor");
        var fieldAdminFeeSubmit = $("#admin-fee-submit");

        var billProduct = $("#pln-bill-product");

        var submitOperatorIcon = $("#operator-image-value");
        var dataRowToken = $("#data-row-token");

        var priceHeader = $("#price-header");
        var adminFeeHeader = $("#admin-fee-header");
        var priceData = $("#price-data");
        var adminFeeDataColumn = $("#admin-fee-column");
        var adminFeeDataSubmit = $("#admin-fee-data");

        return function (config) {
            function disableButton()
            {
                btnBuy.prop("disabled", true);
                btnBuy.addClass("disabled");
                priceWrapper.addClass("dpb-price-noprice");
            }

            function enableButton()
            {
                btnBuy.prop("disabled", false);
                btnBuy.removeClass("disabled");
                priceWrapper.removeClass("dpb-price-noprice");
            }

            function hideDetail()
            {
                customerInformation.empty();
                customerInformation.hide();
                $(".data-ajax").remove();
                customerInformationToken.hide();
                inquireError.hide();
                priceWrapper.addClass("dpb-price-noprice");
                disableButton();
                input.val("");
                //operatorIcon.attr("src", "");
            }

            function getFormattedPrice(price)
            {
                return priceUtils.formatPrice(price, window.orderPriceFormat);

            }

            function errorInputNotice(message)
            {
                input.addClass("input-error");
                errorMsg.text($.mage.__(message));
                hideDetail();
            }

            function listenChangeProduct()
            {
                productSelect.change(function () {

                    $("li.active").removeClass("active");
                    $(this).closest("li").addClass("active");

                    var productPrice = $(this).attr("data-product-price");
                    var productPriceValue = $(this).attr("data-product-price-value");
                    var productIdVendor = $(this).attr("data-product-id-vendor");
                    var productName = $(this).attr("data-product-name");
                    var productDenom = $(this).attr("data-product-denom");

                    fieldProductName.val(productName);
                    fieldProductPrice.val(productPrice);
                    fieldProductPriceValue.val(productPriceValue);
                    fieldProductIdVendor.val(productIdVendor);
                    fieldAdminFeeSubmit.val("");

                    priceHeader.show();
                    priceData.show();

                    productPriceTotal.text(productPrice);
                    priceData.text(productPrice);

                    if (input.val().length !== 0) {
                        doneTyping();
                    }
                });
            }

            function doneTyping()
            {
                var number = input.val();
                if (number.length === 0) {
                    errorInputNotice("Please enter your meter number/customer ID");
                } else if (!regexDigits.test(number) || number.length > 16) {
                    errorInputNotice("Make sure you enter the correct number");
                } else {
                    var productIdVendor;
                    if (current === TYPE_BILL ) {
                        productIdVendor = $("#bill-product-id-vendor").val();
                    } else {
                        productIdVendor = $("#selected-product-id-vendor").val();
                    }

                    var data = {
                        customer_id: number,
                        type: current,
                        product_id_vendor : productIdVendor
                    };

                    $.ajax({
                        url: config.check_url,
                        data: data,
                        type: 'post',
                        dataType: 'json',
                        showLoader: true,
                        success: function (data) {
                            if (data.status == 0) {
                                errorInputNotice(data.message);
                            } else if (data.status == 1) {
                                input.blur();
                                input.removeClass("input-error");
                                errorMsg.text("");
                                //operatorIcon.attr("src", data.result.operator_icon);
                                //submitOperatorIcon.val(data.result.operator_icon);

                                if (current === TYPE_BILL) {
                                    customerInformation.empty().append(data.result.info_block);
                                    handleProductBill();
                                    customerInformation.show();
                                } else {
                                    $(".data-ajax").remove();
                                    dataRowToken.prepend(data.result.info_block);
                                    customerInformationToken.show();
                                }
                                enableButton();
                            } else {
                                customerInformation.hide();
                                customerInformationToken.hide();
                                input.removeClass("input-error");
                                errorMsg.text("");
                                //operatorIcon.attr("src", data.result.operator_icon);
                                //submitOperatorIcon.val(data.result.operator_icon);
                                inquireError.empty().append(data.result.info_block);
                                inquireError.show();
                                disableButton();
                            }
                        },
                        error: function () {
                            errorInputNotice("We cannot find this ID. Make sure you enter the correct ID");
                        }
                    });
                }
            }

            function handleProductBill()
            {
                var adminFeeData = $("#admin-fee-data");
                var totalData = $("#total-data");
                var responseHolder = $("#response-holder");

                var adminFeeFormat = billProduct.attr("data-admin-fee");
                var adminFeeValue = billProduct.attr("data-product-price-value");
                adminFeeData.text(adminFeeFormat);
                var totalAmount =
                    parseInt(adminFeeValue) +
                    parseInt(responseHolder.attr("data-penalty")) +
                    parseInt(responseHolder.attr("data-incentive")) +
                    parseInt(responseHolder.attr("data-total-value"));
                var formattedTotal = getFormattedPrice(totalAmount);

                totalData.text(formattedTotal);
                productPriceTotal.text(formattedTotal);
                fieldProductPriceValue.val(totalAmount);
                fieldAdminFeeSubmit.val(adminFeeValue);
            }

            function activeDefaultProduct()
            {
                $('.list-nomial').first()
                    .children().eq($("input[name='min_position']").val())
                    .find('.product-item').trigger('click');
            }

            /* Event */
            radioBill.on("click", function () {
                current = TYPE_BILL;
                hideDetail();
                formCurrentType.val(TYPE_BILL);
            });
            radioToken.on("click", function () {
                current = TYPE_TOKEN;
                hideDetail();
                formCurrentType.val(TYPE_TOKEN);
            });

            listenChangeProduct();
            activeDefaultProduct();

            input.on("keyup paste", function () {
                disableButton();
                clearTimeout(typingTimer);
                if (input.val()) {
                    typingTimer = setTimeout(doneTyping, doneTypingInterval);
                }
            });
        }
    }
);

