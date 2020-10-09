/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/modal/confirm',
    'mage/url',
    'jquery-ui-modules/widget',
    'mage/translate'
], function ($, confirm, urlBuilder) {
    'use strict';

    $.widget('mage.address', {
        /**
         * Options common to all instances of this widget.
         * @type {Object}
         */
        options: {
            deleteConfirmMessage: $.mage.__('Are you sure you want to delete this address?'),
            validateMaxAddress: '',
            limit: ''
        },

        /**
         * Bind event handlers for adding and deleting addresses.
         * @private
         */
        _create: function () {
            var options         = this.options,
                deleteAddress   = options.deleteAddress;

            this._validateMaximumAddress();

            if (deleteAddress) {
                $(document).on('click', deleteAddress, this._deleteAddress.bind(this));
            }
        },

        /**
         * Validate Maximum Addresses Created
         * @private
         */
        _validateMaximumAddress: function () {
            let self = this,
                url = urlBuilder.build('rest/V1/transcustomer/validateMaxAddress');
            $.ajax({
                type : "POST",
                url  : url,
                async: false,
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('Accept', 'application/json');
                    xhr.setRequestHeader('Content-Type', 'application/json');
                },
                success: function (response) {
                    if (response == true) {
                        self.options.validateMaxAddress = true;
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    self.options.validateMaxAddress = xhr.responseJSON.message;
                    console.log(xhr.responseJSON.parameters[0]);
                    self.options.limit = xhr.responseJSON.parameters[0];
                }
            });
            if (self.options.addAddress) {
                $(document).on('click', self.options.addAddress, this._addAddress.bind(this));
            }
        },

        /**
         * Add a new address.
         * @private
         */
        _addAddress: function () {
            if (this.options.validateMaxAddress === true) {
                window.location = this.options.addAddressLocation;
            } else {
                var a = $.mage.__('You can only save up to %1 addresses. To add a new one, please delete the existing address.').replace('%1', this.options.limit);
                $(this.options.addAddress).addClass('disabled');
                $(this.options.addAddressNotification).show().text(a);
                $("#address-notification-wrap").show();
            }
        },

        /**
         * Delete the address whose id is specified in a data attribute after confirmation from the user.
         * @private
         * @param {jQuery.Event} e
         * @return {Boolean}
         */
        _deleteAddress: function (e) {
            var self = this;

            confirm({
                title  : $.mage.__('Delete Address'),
                content: this.options.deleteConfirmMessage,
                buttons: [{
                    text: $.mage.__('Cancel'),
                    class: 'action secondary',
                    click: function () {
                        this.closeModal();
                    }
                }, {
                    text: $.mage.__('Delete'),
                    class: 'action primary',
                    click: function () {
                        if (typeof $(e.target).parent().data('address') !== 'undefined') {
                            window.location = self.options.deleteUrlPrefix + $(e.target).parent().data('address') +
                                '/form_key/' + $.mage.cookies.get('form_key');
                        } else {
                            window.location = self.options.deleteUrlPrefix + $(e.target).data('address') +
                                '/form_key/' + $.mage.cookies.get('form_key');
                        }
                    }
                }]
            });

            return false;
        }
    });

    return $.mage.address;
});
