define(
    [
        'jquery'
    ],
    function ($) {
        "use strict";

        const LABEL_INPUT_BILL = "Customer ID";
        const LABEL_INPUT_TOKEN = "Meter Number/Customer ID";
        const LABEL_BUTTON_BILL = "Go to Payment";
        const LABEL_BUTTON_TOKEN = "Buy";

        var radioBill = $("#radio-bill");
        var radioToken = $("#radio-token");

        var tooltipBill = $("#tooltip-bill");
        var tooltipToken = $("#tooltip-token");

        var informationBill = $("#information-bill");
        var informationToken = $("#information-token");

        var productList = $("#product-list");
        var labelInput = $("#label-input");
        var buttonBuy = $("#btn-buy-product");

        var selectProductBill = $("#pln-bill-product");
        var productPriceToken = $("#selected-product-price");
        var productNameToken = $("#selected-product-name");
        var productIdVendor = $("#selected-product-id-vendor");

        var serviceTypeToken = $("#service-type-token");
        var serviceTypeBill = $("#service-type-bill");
        var billProductIdVendor = $("#bill-product-id-vendor");

        var infoContentToken = $("#info-content-token");
        var infoContentBill = $("#info-content-bill");

        return function () {
            radioBill.on("click", function () {
                tooltipBill.show();
                tooltipToken.hide();
                informationBill.show();
                informationToken.hide();
                productList.hide();
                labelInput.text($.mage.__(LABEL_INPUT_BILL));
                buttonBuy.text($.mage.__(LABEL_BUTTON_BILL));
                if (selectProductBill.length) {
                    selectProductBill.prop("checked", true);
                    productNameToken.prop("checked", false);
                    productPriceToken.prop("checked", false);
                    productIdVendor.prop("checked", false);
                    billProductIdVendor.prop("checked", true);
                }
                serviceTypeBill.prop("checked", true);
                serviceTypeToken.prop("checked", false);
                infoContentBill.prop("checked", true);
                infoContentToken.prop("checked", false);
            });
            radioToken.on("click", function () {
                tooltipBill.hide();
                tooltipToken.show();
                informationBill.hide();
                informationToken.show();
                productList.show();
                labelInput.text($.mage.__(LABEL_INPUT_TOKEN));
                buttonBuy.text($.mage.__(LABEL_BUTTON_TOKEN));
                if (selectProductBill.length) {
                    selectProductBill.prop("checked", false);
                    productNameToken.prop("checked", true);
                    productPriceToken.prop("checked", true);
                    productIdVendor.prop("checked", true);
                    billProductIdVendor.prop("checked", false);
                }
                serviceTypeToken.prop("checked", true);
                serviceTypeBill.prop("checked", false);
                infoContentToken.prop("checked", true);
                infoContentBill.prop("checked", false);
            });
        }
    }
);

