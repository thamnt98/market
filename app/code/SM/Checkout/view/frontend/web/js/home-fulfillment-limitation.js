/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define(
    [
        'jquery',
        'Magento_Ui/js/modal/modal',
        'Magento_Customer/js/customer-data',
        'SM_Coachmarks/js/view/maincoachmarks-flag',
        'mage/url',
        'mage/translate',
    ], function ($, modal, customerData, maincoachmarks, urlBuilder) {
        'use strict';
    let showPopup = false;
    $.widget(
        'sm.limitation', {
            _create: function () {
                let self = this,
                    customer = customerData.get('customer');
                if (customer().firstname) {
                    console.log('customer');
                    self.showPopup()
                }
                customer.subscribe(function (customerUpdate) {
                    if (customerUpdate.firstname) {
                        console.log('customer change');
                        self.showPopup()
                    }
                });
                maincoachmarks.coachMarks.subscribe(function (newValue) {
                    var customer = customerData.get('customer');
                    if (customer().firstname) {
                        console.log('coachMarks finish');
                        self.showPopup()
                    }
                });
                customerData.get('fulfillment').subscribe(function (newValue) {
                    console.log(newValue);
                });
            },

            showPopup: function () {
                console.log(customerData.get('fulfillment')().show);
                console.log(maincoachmarks.coachMarks());
                console.log(showPopup);
                if (customerData.get('fulfillment')().show || !maincoachmarks.coachMarks() || showPopup) {
                    return;
                }
                showPopup = true;
                $.ajax({
                    type: "POST",
                    url: urlBuilder.build('transcheckout/Fulfillment/update'),
                    dataType: "json",
                    data: JSON.stringify({action: 'update'}),
                    success: function (response) {

                    }
                });
                var options = {
                    type: 'popup',
                    responsive: true,
                    innerScroll: true,
                    modalClass: 'pp-shopping-list pp-alcohol-notice',
                    title: $.mage.__('Our Delivery Area'),
                    buttons: [{
                        text: $.mage.__('Got It'),
                        class: 'action primary',
                        click: function () {
                            this.closeModal();
                        }
                    }]
                };
                var popup = modal(options, $('#home-fulfillment-limitation'));
                $('#home-fulfillment-limitation').modal('openModal');
            }
        }
    );
    return $.sm.limitation;
});
