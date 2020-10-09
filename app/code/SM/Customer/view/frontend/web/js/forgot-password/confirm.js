/**
 * @category SM
 * @package SM_Customer
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author Logan Ta <phuongtt2@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */
define(
    [
        'jquery',
        'ModalCreator',
        'mage/translate'
    ],
    function ($, ModalCreator) {
        "use strict";

        let mod = {};

        mod.init = function () {
            mod.initElements();
            mod.initEvents();
            mod.close();
        };

        mod.initElements = function () {
            mod.container = $('#tab-notify-email');
            mod.label = mod.container.find('p#emailSend');
        };

        mod.initEvents = function () {
            ModalCreator.create(mod.container, 'modal-popup-forgot-password-confirm modal-popup-signin');
        };

        mod.openPopup = function(closingPopup, email) {
            mod.label.text(email);
            closingPopup.addClass('not-open').modal('closeModal');
            mod.container.modal('openModal').on('modalclosed', function() {
                $('#tab-login').modal('openModal');
            });
            mod.container.show();
        };

        mod.close = function () {
            mod.container.find('[selector=close]').click(function () {
                mod.container.modal('closeModal');
            });
        };

        return mod;
    }
);
