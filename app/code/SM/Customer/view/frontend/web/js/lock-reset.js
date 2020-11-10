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
        'mage/translate'
    ], function ($, modal) {
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
                    if (urlParams.has('recoverytoken')) {
                        $(document).trigger('customer:login');
                        if (self.options.login == 'true') {
                            token = urlParams.get('recoverytoken');
                            email = '';
                            name = urlParams.get('name');
                            $(self.options.tabLockResetSelector).find('[selector=title]').text($.mage.__('Hi, %1! Your account is back.').replace('%1', name));
                        }

                        $(self.options.tabLockResetSelector).modal('openModal').on('modalclosed', function() {
                            if (self.options.login == 'false') {
                                //$('#tab-login').modal('openModal').show();
                                $('.sign-link a').trigger('click');
                            }
                        }).show();
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
                        $('#tab-recovery-password').modal('openModal').show();
                    });
                }
            }
        );

        return $.sm.lockReset;
    }
);
