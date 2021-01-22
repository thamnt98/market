define(
    [
        'jquery'
    ],
    function ($, modal) {
        "use strict";
        return function (config, element) {
            $(element).on('change',function (e) {
                const $this = $(this),
                    $inputBankContainer = $this.parents('.field').next('.no-display'),
                    $inputBank = $inputBankContainer.find('input');

                if ($this.val() === 'insert_by_text') {
                    $inputBankContainer.css('display', 'block');
                    $inputBank.addClass('required-entry');
                } else {
                    $inputBankContainer.parents('.field').next('.no-display').css('display', 'none');
                    $inputBank.removeClass('required-entry');
                }
            });
        };
    }
);
