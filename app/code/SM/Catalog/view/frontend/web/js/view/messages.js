define([
    'jquery',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'underscore',
    'jquery/jquery-storageapi'
], function ($, Component, customerData, _) {
    'use strict';

    return Component.extend({
        defaults: {
            cookieMessages: [],
            messages: [],
            selector: '.page.messages .message',
            listens: {
                isHidden: 'onHiddenChange'
            }
        },
        /**
         * Extends Component object by storage observable messages.
         */
        initialize: function () {
            this._super();

            this.cookieMessages = _.unique($.cookieStorage.get('mage-messages'), 'text');
            this.messages = customerData.get('messages').extend({
                disposableCustomerData: 'messages'
            });

            // Force to clean obsolete messages
            if (!_.isEmpty(this.messages().messages)) {
                customerData.set('messages', {});
            }

            $.cookieStorage.set('mage-messages', '');

            $(window).on('beforeunload', function () {
                $.cookieStorage.set('mage-messages', '');
            });
        },

        initObservable: function () {
            this._super()
                .observe('isHidden');

            return this;
        },

        hideMessage: function () {
            var el = $(this.selector);
            el.toggleClass('bounceInRight bounceOutRight');
            document.onreadystatechange = () => {
                if (document.readyState === 'complete') {
                    setTimeout(function () {
                        el.hide();
                        $.cookieStorage.set('mage-messages', '');
                    }, 10000);
                }
            };
        },

        isVisible: function () {
            return this.isHidden(!_.isEmpty(this.messages().messages) || !_.isEmpty(this.cookieMessages));
        },

        onHiddenChange: function (isHidden) {
            var self = this;

            if (isHidden) {
                self.hideMessage();
            } else {
                //Hide Message In PDP
                setTimeout(function () {
                    $(self.selector).hide();
                    $.cookieStorage.set('mage-messages', '');
                }, 10000);
            }
            this.isHidden(false);
        }

    });

});
