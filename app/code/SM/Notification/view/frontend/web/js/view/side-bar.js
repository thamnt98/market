/**
 * SMCommerce
 *
 * @category    SM
 * @package     SM_Notification
 *
 * Date: December, 02 2020
 * Time: 5:17 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */
define([
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'jquery',
    'ko',
    'mage/url',
    'mage/storage'
], function (
    Component,
    customerData,
    $,
    ko,
    urlBuilder,
    storage
) {
    'use strict';

    let notification = $('[data-block="notification"]');

    notification.on('dropdowndialogopen', function () {
        let hideBackground = '<div class="modals-overlay" id="background-hidden"></div>',
            tab = $('button.tab.active');

        if (tab.length > 0) {
            $(tab[0]).trigger('click');
        }

        $('body').after(hideBackground);
    });

    notification.on('dropdowndialogclose', function () {
        $('#background-hidden').remove();
    });

    return Component.extend({
        notificationUrl       : urlBuilder.build('notification'),
        visibleUpdatesTab     : ko.observable(false),
        orderStatusList       : ko.observable(false),
        updateList            : ko.observable(false),
        total                 : ko.observable(0),
        orderStatusUnreadTotal: ko.observable(false),
        updateUnreadTotal     : ko.observable(false),
        /**
         * @override
         */
        initialize: function () {
            let self     = this,
                customer = customerData.get('customer');

            $('[data-block="notification"]').on('contentLoading', function () {
                self.isLoading(true);
            });

            if (customer()['cus_id']) {
                self.generateNewDevice(customer()['cus_id']);
            } else {
                customer.subscribe(function (data) { // Init total after load customer
                    let cusId = data['cus_id'];

                    self.generateNewDevice(cusId);
                }, self);
            }

            return this._super();
        },
        //jscs:enable requireCamelCaseOrUpperCaseIdentifiers

        isLoading         : ko.observable(false),
        updateLoading     : ko.observable(false),
        orderStatusLoading: ko.observable(false),

        /**
         * @return {boolean}
         */
        closeNotification: function () {
            let notification = $('[data-block="notification"]');

            notification.on('click', '[data-action="close"]', function (event) {
                event.stopPropagation();
                notification.find('[data-role="dropdownDialog"]').dropdownDialog('close');
            });

            return true;
        },

        /**
         * Open tab order status
         */
        openOrderStatusTab: function () {
            if (this.orderStatusList() === false) {
                this.getNotificationOrderStatus();
            }

            this.visibleUpdatesTab(false);
            $('#order-status-tab').addClass('active');
            $('#updates-tab').removeClass('active');
        },

        /**
         * Open tab updates
         */
        openUpdatesTab: function () {
            if (this.updateList() === false) {
                this.getNotificationUpdate();
            }
            this.visibleUpdatesTab(true);
            $('#updates-tab').addClass('active');
            $('#order-status-tab').removeClass('active');
        },

        /**
         * @return {String}
         */
        getItemRenderer: function () {
            return 'defaultRenderer';
        },

        getNotificationOrderStatus: function () {
            let self = this,
                url = urlBuilder.build('rest/V1/customer/notification/list');

            url += "?searchCriteria[filter_groups][0][filters][0][field]=event&" +
                "searchCriteria[filter_groups][0][filters][0][value]=order_status&" +
                "searchCriteria[sortOrders][0][field]=created_at&" +
                "searchCriteria[sortOrders][0][direction]=DESC&" +
                "searchCriteria[pageSize]=5";

            storage.get(
                url,
                null,
                false
            ).done(function (response) {
                self.orderStatusList(response.items);
            }).fail(function () {
                self.orderStatusList([]);
            }).always(function () {
                self.updateLoading(false);
            });
        },

        updateTotal: function () {
            this.total(this.orderStatusUnreadTotal() + this.updateUnreadTotal());
        },

        getNotificationUpdate: function () {
            let self = this,
                url = urlBuilder.build('rest/V1/customer/notification/list');

            url += "?searchCriteria[filter_groups][0][filters][0][field]=event&" +
                "searchCriteria[filter_groups][0][filters][0][value]=order_status&" +
                "searchCriteria[filter_groups][0][filters][0][condition_type]=neq&" +
                "searchCriteria[sortOrders][0][field]=created_at&" +
                "searchCriteria[sortOrders][0][direction]=DESC&" +
                "searchCriteria[pageSize]=5";

            self.updateLoading(true);
            storage.get(
                url,
                null,
                false
            ).done(function (response) {
                self.updateList(response.items);
            }).fail(function () {
                self.updateList([]);
            }).always(function () {
                self.updateLoading(false);
            });
        },

        getTotalNotification: function () {
            return this.total();
        },

        countUpdateUnread: function () {
            let self = this,
                url = urlBuilder.build('rest/V1/customer/notification/unread-number');

            url += "?searchCriteria[filter_groups][0][filters][0][field]=event&" +
                "searchCriteria[filter_groups][0][filters][0][value]=order_status&" +
                "searchCriteria[filter_groups][0][filters][0][condition_type]=neq";

            storage.get(
                url,
                null,
                false
            ).done(function (response) {
                self.updateUnreadTotal(response);
                self.updateTotal();
            }).fail(function () {
                self.updateUnreadTotal(0);
                self.updateTotal();
            });
        },

        /**
         * Count notification order status unread
         */
        countOrderStatusUnread: function () {
            let self = this,
                url = urlBuilder.build('rest/V1/customer/notification/unread-number');

            url += "?searchCriteria[filter_groups][0][filters][0][field]=event&" +
                "searchCriteria[filter_groups][0][filters][0][value]=order_status&" +
                "searchCriteria[filter_groups][0][filters][0][condition_type]=eq";

            storage.get(
                url,
                null,
                false
            ).done(function (response) {
                self.orderStatusUnreadTotal(response);
                self.updateTotal();
            }).fail(function () {
                self.orderStatusUnreadTotal(0);
                self.updateTotal();
            });
        },

        /**
         * Mark read a notification.
         * @param {Object} item
         * @param {Event} event
         */
        actionRead: function (item, event) {
            let self = this,
                url = urlBuilder.build('rest/V1/notification/read/');

            if ($(event.currentTarget).hasClass('read')) {
                if (item.redirect_url && item.redirect_url !== '#') {
                    window.location.href = item.redirect_url;
                }
            } else {
                storage.post(
                    url,
                    JSON.stringify({messageIds: [item.message_id]})
                ).done(function (response) {
                    if (response) {
                        $(event.currentTarget).addClass('read');
                        if ($(event.currentTarget).parents('#block-updates').length > 0) {
                            self.updateUnreadTotal(self.updateUnreadTotal() - response);
                        } else {
                            self.orderStatusUnreadTotal(self.orderStatusUnreadTotal() -response);
                        }

                        self.updateTotal();
                        if (item.redirect_url && item.redirect_url !== '#') {
                            window.location.href = item.redirect_url;
                        }
                    }
                });
            }
        },

        /**
         * Read all notification on `Order Status` tab
         */
        readAllOrderStatus: function () {
            let self   = this,
                url    = urlBuilder.build('rest/V1/notification/read/updateAll'),
                params = {
                    searchCriteria: {
                        filter_groups: [{
                            filters: [{
                                field         : 'event',
                                value         : 'order_status',
                                condition_type: 'eq'
                            }]
                        }]
                    }
                };

            storage.post(
                url,
                JSON.stringify(params)
            ).done(function (response) {
                if (response) {
                    self.orderStatusUnreadTotal(self.orderStatusUnreadTotal() -response);
                    $('#block-order-status li.notification-item').addClass('read');
                    self.updateTotal();
                }
            });
        },

        /**
         * Read all notification on `Update` tab.
         */
        readAllUpdate: function () {
            let self = this,
                url = urlBuilder.build('rest/V1/notification/read/updateAll'),
                params = {
                    searchCriteria: {
                        filter_groups: [
                            {
                                filters: [
                                    {
                                        field         : 'event',
                                        value         : 'order_status',
                                        condition_type: 'neq'
                                    }
                                ]
                            }
                        ]
                    }
                };

            storage.post(
                url,
                JSON.stringify(params)
            ).done(function (response) {
                if (response) {
                    self.updateUnreadTotal(self.updateUnreadTotal() - response);
                    self.total(self.total() - response);
                    $('#block-updates li.notification-item').addClass('read');
                }
            });
        },

        /**
         * Highlight title.
         * @param {Object} notify
         * @returns string(html)
         */
        convertTitle: function (notify) {
            if (notify['highlight_title']) {
                return notify['title'].replace(
                    notify['highlight_title'],
                    '<span class="highlight">' + notify['highlight_title'] + '</span>'
                );
            } else {
                return notify['title'];
            }
        },

        /**
         * Highlight content.
         * @param {Object} notify
         * @returns string(html)
         */
        convertContent: function (notify) {
            if (notify['highlight_content']) {
                return notify['content'].replace(
                    notify['highlight_content'],
                    '<span class="highlight">' + notify['highlight_content'] + '</span>'
                );
            } else {
                return notify['content'];
            }
        },

        generateNewDevice: function (customerId) {
            if (!customerId) {
                return;
            }

            let self             = this,
                browserCustomers = $.localStorage.get('cus-db');

            if (!browserCustomers || typeof browserCustomers !== 'object') {
                browserCustomers = [];
            }

            let ids = browserCustomers.filter(function (id) {
                return id == customerId;
            });

            if (!ids.length) { // Create notification login new device.
                $.ajax(
                    {
                        url: urlBuilder.build('notification/index/createLoginNotify'),
                        type: 'POST',
                        data: {
                            customer_id: customerId
                        }
                    }
                ).done(
                    function (response) {
                        if (response.status) {
                            browserCustomers.push(customerId);
                            $.localStorage.set('cus-db', browserCustomers);
                            self.countUpdateUnread();
                            self.countOrderStatusUnread();
                        }
                    }
                );
            } else {
                self.countUpdateUnread();
                self.countOrderStatusUnread();
            }
        }
    });
});
