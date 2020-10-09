define(
    [
        'jquery',
        'Magento_Ui/js/modal/modal'
    ],
    function ($, modal) {
        let title = $('.link-popup').text(),
            options = {
                type: 'popup',
                responsive: true,
                innerScroll: true,
                modalClass: 'modal-popup-small',
                title: title,
                buttons: []
        };

        let popup = modal(options, $('#terms-and-conditions'));
        $(".link-popup").on('click',function () {
            $("#terms-and-conditions").modal("openModal");
        });

    }
);
