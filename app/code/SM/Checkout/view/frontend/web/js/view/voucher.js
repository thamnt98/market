/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'uiComponent',
    'ko',
    'Magento_Ui/js/modal/modal',
    'mage/url',
    'mage/storage',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/action/get-totals',
    'Magento_Checkout/js/model/quote',
    'gtmCheckout',
    'moment',
    'Magento_Catalog/js/price-utils',
    'SM_Checkout/js/view/global-observable'
], function (
    $,
    Component,
    ko,
    modal,
    urlManager,
    storage,
    fullScreenLoader,
    getTotalsAction,
    quote,
    gtmCheckout,
    moment,
    priceUtils,
    globalVar
) {
    'use strict';
    return Component.extend({
        applyList: ko.observableArray([]),
        notApplyList: ko.observableArray([]),
        showVoucherBlock: ko.observable(false),
        initialize: function(){
            this._super();
            this.getDefaultCouponLoaded();
            quote.totals.subscribe(this.getVoucherRuleFromTotals.bind(this));
            this.canShowVoucher();
            return this;
        },
        canShowVoucher: function(){
            var currentUrl = window.checkoutConfig.currentUrl;
            var self = this;
            if (currentUrl == "transcheckout_digitalproduct_index") {
                this.showVoucherBlock(true);
            }else{
                self.showVoucherBlock(globalVar.isStepShipping());
                globalVar.isStepShipping.subscribe(function(newValue){
                    self.showVoucherBlock(newValue);
                });

            }
        },
        voucherList: function(){
            return window.checkoutConfig.voucher_list;
        },
        /**
         * check if it can show voucher by config
         * @returns {*}
         */
        canShow: function(){
            return window.checkoutConfig.canShowVoucher;
        },
        /**
         * open voucher list
         */
        openVoucherList: function(){
            $('#ct-apply-voucher-popup').modal("openModal");
        },
        /**
         * close voucher list
         */
        closeVoucherList: function(){
            $('#ct-apply-voucher-popup').modal("closeModal");
        },
        onRenderComplete: function () {
            var self = this,
                selector = $('#ct-apply-voucher-popup'),
                options = {
                    type: 'popup',
                    responsive: true,
                    innerScroll: false,
                    title: '',
                    buttons: [],
                    modalClass: 'modal-popup-ct-apply-voucher-popup',
                    clickableOverlay: false,
                    keyEventHandlers: {
                        escapeKey: function () {
                            return;
                        }
                    }
                };
            modal(options, selector);
        },
        onRenderCompleteDetails: function (id) {
            let selector = $('#ct-apply-voucher-popup-'+ id),
                options = {
                    type: 'popup',
                    responsive: true,
                    innerScroll: false,
                    title: '',
                    buttons: [],
                    modalClass: 'pp-apply-voucher-detail',
                    clickableOverlay: false,
                    keyEventHandlers: {
                        escapeKey: function () {
                            return;
                        }
                    }
                };
            modal(options, selector);
        },

        /**
         * show voucher details
         * @param id
         */
        showDetails: function (id) {
            this.closeVoucherList();
            $('#ct-apply-voucher-popup-' + id).modal("openModal");
        },

        /**
         * close voucher details
         * @param id
         */
        closeDetails: function (id) {
            $('#ct-apply-voucher-popup-' + id).modal("closeModal");
        },

        /**
         * API
         * add code by click action
         * @param voucherCode
         */
        applyVoucher: function (voucherCode, isDetail){
            let quoteId = window.checkoutConfig.quoteData.entity_id,
                data = JSON.stringify({cartId: quoteId, couponCode: voucherCode}),
                self = this,
                applyCode = self.voucherList().filter(function (obj) {
                    return obj.code === voucherCode;
                }),
                hasApplied = this.applyList().filter(function (obj) {
                    return obj.code === voucherCode;
                });

            if (isDetail) {
                if (applyCode.length > 0) {
                    this.closeDetails(applyCode[0].id);
                }
            } else {
                this.closeVoucherList();
            }

            if (hasApplied.length) {
                return;
            }

            fullScreenLoader.startLoader();
            storage.post(
                urlManager.build('rest/V1/trans-checkout/me/applyvoucher'),
                data,
                false
            ).done(
                function (response) {
                    /**
                     * remove this voucher on totals by ko
                     */
                    self.addApplyList(voucherCode);
                    /**
                     * reload block totals
                     */
                    var deferred = $.Deferred();
                    getTotalsAction([], deferred);
                    fullScreenLoader.stopLoader();
                    //Todo Remove Debug
                    console.log(applyCode);
                    if (typeof dataLayerSourceObjects !== "undefined") {
                        var currentTime = moment().format('YYYY-MM-DD');
                        //Todo Remove Debug
                        console.log(currentTime);
                        let data = [];
                        data['voucher_id'] = applyCode[0].id;
                        data['voucher_name'] = applyCode[0].name;
                        data['voucher_description'] = applyCode[0].description;
                        if (applyCode[0].to_date) {
                            data['voucher_validation'] = 'Valid until ' + applyCode[0].to_date;
                            data['voucher_status'] = gtmCheckout.minusDay(currentTime,applyCode[0].to_date);
                        } else {
                            data['voucher_validation'] = "Not available";
                            data['voucher_status'] = "Expired";
                        }
                        gtmCheckout.push('apply_voucher',data);
                    }
                    return true;
                }
            ).fail(
                function (response) {
                    fullScreenLoader.stopLoader();
                    return false;
                }
            );
        },
        /**
         * API
         * remove code by click action
         * @param voucherCode
         */
        removeVoucher: function (voucherCode){
            var quoteId = window.checkoutConfig.quoteData.entity_id;
            var data = JSON.stringify({cartId: quoteId, couponCode: voucherCode});
            var self = this;

            fullScreenLoader.startLoader();
            storage.post(
                urlManager.build('rest/V1/trans-checkout/me/removevoucher'),
                data,
                false
            ).done(
                function (response) {
                    /**
                     * remove this voucher on totals by ko
                     */
                    self.removeApplyList(voucherCode);
                    /**
                     * reload block totals
                     */
                    var deferred = $.Deferred();
                    getTotalsAction([], deferred);
                    fullScreenLoader.stopLoader();
                    var removeCode = self.voucherList().filter(function( obj ) {
                        return obj.code === voucherCode;
                    });
                    //Todo Remove Debug
                    console.log(removeCode);
                    if (typeof dataLayerSourceObjects !== "undefined" && removeCode.length > 0) {
                        var currentTime = moment().format('YYYY-MM-DD');
                        //Todo Remove Debug
                        console.log(currentTime);
                        let data = [];
                        data['voucher_id'] = removeCode[0].id;
                        data['voucher_name'] = removeCode[0].name;
                        data['voucher_description'] = removeCode[0].description;
                        if (removeCode[0].to_date) {
                            data['voucher_validation'] = 'Valid until ' + removeCode[0].to_date;
                            data['voucher_status'] = gtmCheckout.minusDay(currentTime,removeCode[0].to_date);
                        } else {
                            data['voucher_validation'] = 'Not available';
                            data['voucher_status'] = "Expired";
                        }
                        gtmCheckout.push('remove_voucher',data);
                    }
                    return true;
                }
            ).fail(
                function (response) {
                    fullScreenLoader.stopLoader();
                    return false;
                }
            );
        },
        /**
         * KO template
         * render a line voucher if this code can apply
         * @param voucherCode
         * @returns {boolean}
         */
        addApplyList: function(voucherCode){
            /**
             * if this code exist then it doesn't need to add additional a line
             */
            var checkExist = this.applyList.filter(function( obj ) {
                return obj.code === voucherCode;
            });
            if(checkExist.length > 0) return false;

            /**
             * get code amount from list
             * this will be replaced by real rule amount by function getVoucherRuleFromTotals()
             */
            var applyCode = this.voucherList().filter(function( obj ) {
                return obj.code === voucherCode;
            });
            var data = {code: applyCode[0].code, amount: applyCode[0].amount};
            this.applyList.push(data);
        },
        /**
         * KO template
         * remove code by apply list
         * @param voucherCode
         */
        removeApplyList: function(voucherCode){
            var afterRemove = this.applyList.filter(function( obj ) {
                return obj.code !== voucherCode;
            });
            this.applyList(afterRemove);

            var afterRemove = this.notApplyList.filter(function( obj ) {
                return obj.code !== voucherCode;
            });
            this.notApplyList(afterRemove);
        },
        /**
         * add code applied after loading
         * @returns {*}
         */
        getDefaultCouponLoaded: function(){
            var self = this;
            var coupons = window.checkoutConfig.quoteData.coupon_code;

            if(coupons == null) return [];

            var couponArr = coupons.split(",");
            $.each(this.voucherList(), function(key, item){
                if(couponArr.indexOf(item.code) != -1){
                    self.applyList.push({code: item.code, amount: item.amount});
                }
            });
        },
        /**
         * show/hide voucher by js when filter
         */
        findVoucher: function (){
            let searchValue = $('#voucher-search').val().toLowerCase(),
                vouchers = $('.list-voucher > ul > li.item-voucher');
            $.each(vouchers, function(){
                let name = $(this).attr('name').toLowerCase(),
                    des = $(this).attr('description').toLowerCase();
                if(name != undefined && des != undefined){
                    if(name.indexOf(searchValue) > -1 || des.indexOf(searchValue) > -1){
                        $(this).show();
                    }else{
                        $(this).hide();
                    }
                }
            });
        },
        /**
         * subscribe quote totals changed
         * replace correct rule id amount from totals
         * show correct amount in line
         * @param totals
         */
        getVoucherRuleFromTotals: function (totals) {
            var self = this;

            self.applyList([]);
            self.notApplyList([])
            if (totals.extension_attributes && totals.extension_attributes.amrule_discount_breakdown) {
                var rules = totals.extension_attributes.amrule_discount_breakdown;
                $.each(rules, function(key, item){
                    if (item.code) {
                        self.removeApplyList(item.code);
                        self.applyList.push({code: item.code, amount: item.rule_amount});
                    }
                });
            }
            if (totals.extension_attributes && totals.extension_attributes.apply_voucher) {
                var applyVoucher = totals.extension_attributes.apply_voucher;
                $.each(applyVoucher, function(key, item){
                    var hasApplied = self.notApplyList().filter(function( obj ) {
                        return obj.code === item.code;
                    });
                    if(hasApplied.length == 0 ){
                        var newData = {code: item.code, amount: item.amount};
                        self.notApplyList.push(newData);
                    }
                });
            }
        },

        /**
         * @param {*} price
         * @return {*|String}
         */
        getFormattedPrice: function (price) {
            return priceUtils.formatPrice(price, quote.getPriceFormat());
        },

        /**
         * @returns {string|*}
         */
        getAddButtonTitle: function () {
            if (this.applyList().length > 0) {
                return $.mage.__('Add');
            } else {
                let voucherNumber = this.voucherList().length;

                return voucherNumber + ' ' + $.mage.__('Vouchers');
            }
        },

        searchEnter: function(data , event){
            if (event.which == 13) {
                this.findVoucher();
            } else {
                return true;
            }
        }
    });
});
