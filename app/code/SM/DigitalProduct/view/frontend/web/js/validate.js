define(
    [
        'jquery',
        'Magento_Ui/js/lib/view/utils/async'
    ],
    function ($, async) {
        "use strict";

        return function (config) {
            var input = $("#input-number");
            var errorMsg = $("#error-message");
            var operatorIcon = $("#operator-icon");
            var btnBuy = $("#btn-buy-product");
            var currentRequest = null;

            var typingTimer;
            var doneTypingInterval = 1000;
            var priceWrapper = $("#product-price-wrapper");

            var productDetailContent = $("#product-detail");
            var productPriceValue = $("#product-price-value");
            var productDetailWrapper = $("#product-detail-wrapper");
            var productList = $("#product-list");
            var currentTypeInput = $("#form-current-type");
            var currentType = currentTypeInput.val();
            var regexDigits = new RegExp("^[0-9]*$");
            var fieldProductName = $("#selected-product-name");
            var fieldProductPrice = $("#selected-product-price");
            var fieldProductPriceValue = $("#selected-product-price-value");
            var fieldProductIdVendor = $("#selected-product-id-vendor");

            var submitOperatorIcon = $("#operator-image-value");

            $("form input[type=text]").on('keyup keypress', function(e) {
                var keyCode = e.keyCode || e.which;
                if (keyCode === 13) {
                    e.preventDefault();
                }
            });

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

            function errorInputNotice(message)
            {
                input.addClass("input-error");
                errorMsg.text($.mage.__(message));
                hideDetail();
            }

            async.async('.list-nomial',function () {
                var productSelect = $('input:radio.product-item');
                var categorySelect = $('input[type=radio][name=category_id]');

                productSelect.change(function () {
                    var productPrice = $(this).attr("data-product-price");
                    var productPriceDataValue = $(this).attr("data-product-price-value");
                    var productDesc = $(this).attr("data-product-desc");
                    var productName = $(this).attr("data-product-name");
                    var productIdVendor = $(this).attr("data-product-id-vendor");

                    $("li.active").removeClass("active");
                    $(this).closest("li").addClass("active");
                    productDetailWrapper.show();
                    productPriceValue.text(productPrice);
                    priceWrapper.removeClass("dpb-price-noprice");
                    productDetailContent.empty();
                    productDetailContent.append(productDesc);
                    fieldProductName.val(productName);
                    fieldProductPrice.val(productPriceDataValue);
                    fieldProductPriceValue.val(productPriceDataValue);
                    fieldProductIdVendor.val(productIdVendor);

                    enableButton();
                });

                categorySelect.change(function () {
                    productSelect.prop("checked", false);
                    $("li.active").removeClass("active");

                    productDetailWrapper.hide();
                    priceWrapper.addClass("dpb-price-noprice");
                    disableButton();

                    var categoryId = $(this).val();

                    $("ul.list-nomial").hide();
                    $("#list" + categoryId).show();
                    currentTypeInput.val($(this).data('type'));
                });

                input.on("keyup paste", function () {
                    productDetailWrapper.hide();
                    disableButton();
                });

            });

            //on keyup, start the countdown
            input.on("keyup paste", function () {
                disableButton();
                priceWrapper.addClass("dpb-price-noprice");
                clearTimeout(typingTimer);
                if (input.val()) {
                    typingTimer = setTimeout(doneTyping, doneTypingInterval);
                }
            });


            //user is "finished typing," do something
            function doneTyping()
            {
                var number = input.val();
                if (number.length === 0) {
                    errorInputNotice("Please enter your mobile number");
                } else if (!regexDigits.test(number) || number.length < 8 || number.length > 15) {
                    errorInputNotice("We cannot find this number. Make sure you enter the correct mobile number");
                } else {
                    var data = {
                        number: number,
                        category_id: config.category_id,
                        type : currentType
                    };
                    currentRequest = $.ajax({
                        url: config.check_url,
                        data: data,
                        type: 'post',
                        dataType: 'json',
                        showLoader: true,
                        beforeSend : function () {
                            if (currentRequest != null) {
                                currentRequest.abort();
                            }
                        },
                        success: function (data) {
                            if (data.status == 0) {
                                errorInputNotice(data.message);
                            } else {
                                input.blur();
                                input.removeClass("input-error");
                                errorMsg.text("");
                                operatorIcon.attr("src", data.result.operator_icon);
                                submitOperatorIcon.val(data.result.operator_icon);
                                productList.empty().append(data.result.product_block);
                                productDetailWrapper.hide();
                                priceWrapper.addClass("dpb-price-noprice");
                                disableButton();
                            }
                        },
                        error: function () {
                            errorInputNotice("We cannot find this number. Make sure you enter the correct mobile number");
                        }
                    });
                }
            }

            function hideDetail()
            {
                operatorIcon.attr("src", "");
                productList.empty();
                disableButton();
                productDetailWrapper.hide();
                priceWrapper.addClass("dpb-price-noprice");
            }
        }
    }
);

