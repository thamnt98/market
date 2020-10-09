define(
    [
        'jquery',
        'Magento_Ui/js/modal/modal',
        'mage/url',
        'mage/translate'
    ],
    function ($, modal, urlBuilder, $t) {
        'use strict';

        return function (config) {

            var isInstallation = $("#is-installation-" + config.itemId),
                installationNote = $("#installation-note-" + config.itemId),
                fieldInstallationNote = $("#field-installation-note-" + config.itemId),
                installationContent = $('#installation-content-' + config.itemId);

            isChecked(config);

            isInstallation.click(function (e) {
                if (!$(this).attr('checked') && config.hasRemovePopup) {
                    if (!config.removePopup) {
                        initRemovePopup(config);
                    }
                    config.removePopup.openModal();
                } else {
                    fieldInstallationNote.show();
                    installationContent.addClass('active');
                }
            });

            installationNote.change(function () {
                saveNote(config);
            });


            /**
             * Is installation.
             */
            function isChecked(config) {

                if (config.defaultChecked === true) {
                    isInstallation.prop('checked', true);
                    fieldInstallationNote.show();
                    installationContent.addClass('active');
                } else {
                    isInstallation.prop('checked', false);
                }
            }

            /**
             * Send Ajax save installation note.
             */
            function saveNote(config) {
                if (!config.itemId) {
                    return;
                }

                let params = {isAjax: 1, item_id: config.itemId, product_id: config.productId, action: 'update'};

                params['is_installation'] = isInstallation.attr('checked') ? 1 : 0;
                params['installation_note'] = installationNote.val();
                params['installation_fee'] = 0;
                $.ajax({
                    type    : 'POST',
                    url     : urlBuilder.build('installation/actions/index'),
                    data    : params,
                    dataType: "json"
                });
            }

            /**
             * Send Ajax remove installation.
             */
            function removeInstallation(config) {
                let params = {isAjax: 1, item_id: config.itemId, product_id: config.productId, action: 'remove'};

                $.ajax({
                    type    : 'POST',
                    url     : urlBuilder.build('installation/actions/index'),
                    data    : params,
                    dataType: "json",
                    success: function () {
                        installationNote.val('');
                        fieldInstallationNote.hide();
                        $('input[id="is-installation-' + config.itemId + '"]').prop('checked', false);
                        installationContent.removeClass('active');
                    }
                });
            }

            /**
             * Init popup remove installation.
             */
            function initRemovePopup(config) {

                if (config.hasRemovePopup) {
                    let options = {
                        type       : 'popup',
                        responsive : true,
                        innerScroll: false,
                        modalClass : 'installation-remove-modal',
                        title      : $t('Remove Installation'),
                        buttons    : [{
                            text : $t('Cancel'),
                            click: function () {
                                $('input[id="is-installation-' + config.itemId + '"]').prop('checked', true);
                                this.closeModal();
                            }
                        }, {
                            text : $t('Remove'),
                            class: 'primary action',
                            click: function () {
                                removeInstallation(config);
                                this.closeModal();
                            }
                        }]
                    };

                    config.removePopup = modal(options, $('#installation-modal-remove-' + config.itemId));
                    $('.installation-remove-modal [data-role="closeBtn"]').click(function () {
                        $('input[id="is-installation-' + config.itemId + '"]').prop('checked', true);
                    });
                }
            }

        };
    }
);
