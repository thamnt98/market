/**
 * @category Trans
 * @package Trans_CustomerMyProfile
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author Nam Nguyen <namnd2@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */
define(
    [
        'jquery',
        'Magento_Ui/js/modal/modal',
        'mage/url',
        'mage/translate'
    ], function ($, modal, urlBuilder) {
        'use strict';

        var token = '',
            email = '',
            name= '';

        $.widget(
            'sm.lockReset', {
                _create: function () {
                    this.init();
                    this.close();
                },

                init: function () {
                    var self = this,
                        options = {
                            type: 'popup',
                            responsive: true,
                            innerScroll: false,
                            title: '',
                            buttons: [],
                            modalClass: 'modal-popup-lock-reset modal-popup-xsmall',
                            clickableOverlay: false
                        };

                    modal(options, $(self.options.tabLockResetSelector));
                    var urlParams = new URLSearchParams(window.location.search);
                    if (urlParams.has('recoverytoken') && urlParams.has('email') && !urlParams.has('account')) {
                        this.callAjax(urlParams.get('recoverytoken'), urlParams.get('email'));
                    } else if (urlParams.has('account') && urlParams.get('account') == 'back') {
                        //$(self.options.tabLockResetSelector).find('[selector=title]').text($.mage.__('Hi, %1! Your account is back.').replace('%1', urlParams.get('name')));
                        $(self.options.tabLockResetSelector).modal('openModal');
                        token = urlParams.get('recoverytoken');
                        //email = urlParams.get('email');
                        //name = urlParams.get('name');
                        window.history.replaceState(null, null, window.location.pathname);
                    }
                },

                close: function () {
                    var self = this;
                    $(self.options.tabLockResetSelector).find('[selector=next]').click(function () {
                        $(self.options.tabLockResetSelector).modal('closeModal');
                    });
                    $(self.options.tabLockResetSelector).find('[selector=change-password]').click(function () {
                        $(self.options.tabLockResetSelector).modal('closeModal');
                        $('#tab-recovery-password').find('#reset-password-token').val(token);
                        //$('#tab-recovery-password').find('#reset-password-email').val(email);
                        $('#tab-recovery-password').modal('openModal').show();
                    });
                },

                callAjax: function (token, email) {
                    var url = urlBuilder.build("customer/trans/resetpass"),
                        baseUrl = url.replace("customer/trans/resetpass", ""),
                        self = this;
                    $.ajax({
                        url: url,
                        type: 'POST',
                        dataType: 'json',
                        data: {'recoverytoken': token, 'email': email},
                        showLoader: true,
                        success: function(response) {
                            window.history.replaceState(null, null, window.location.pathname);
                            if (response.status == true) {
                                window.location.href = baseUrl + '?account=back' + '&recoverytoken=' + token;
                            } else {
                                $(self.options.tabLockResetSelector).modal('openModal').on('modalclosed', function() {
                                    if (response.status == false) {
                                        $('.sign-link a').trigger('click');
                                    }
                                }).show();
                            }
                        }
                    });
                }
            }
        );

        return $.sm.lockReset;
    }
);
