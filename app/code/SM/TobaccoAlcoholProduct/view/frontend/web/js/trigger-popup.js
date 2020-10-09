define(
    [
        'jquery',
        'Magento_Ui/js/modal/modal',
        'mage/url',
        'domReady!'
    ],
    function (
        $,
        modal,
        urlBuilder
    ) {
        "use strict";
        var popup = $("#tobacco-product-popup-content");

        /**
         * Trigger ajax to update customer is informed
         */
        function setCustomerIsInformed()
        {
            $.ajax(
                {
                    url: urlBuilder.build("alcohol/ajax/setcustomerisinformed"),
                    type: "post",
                    dataType: "json",
                    success: function (result) {

                    }
                }
            );
        }

        /**
         * Display popup to inform customer
         */
        function showPopup()
        {
            var options = {
                type: 'popup',
                responsive: true,
                title: jQuery.mage.__('Age Verification'),
                modalClass: 'pp-shopping-list pp-alcohol-notice',
                buttons: [{
                    text: jQuery.mage.__('Got it'),
                    class: 'action primary',
                    click: function () {
                        setCustomerIsInformed();
                        this.closeModal();
                    }
                }]
            };
            $(popup).modal(options).modal('openModal');
        }

        return function (config) {
            var isShow = popup.attr("data-is-show");
            if (isShow) {
                showPopup();
            }
        };
    }
);

