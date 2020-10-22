require([
    'jquery',
    'ko',
    'underscore'
], function ($, ko, _) {
    'use strict';

    $(document).ready(function(){
        $('input.qty').on('keyup', function() {
            if($(this).val() == 0) {
                $('.grouped-super input, .grouped-super select', $(this).closest('tr')).prop('disabled', true);

                if($(this).closest('.grouped').hasClass('hide-options-zero-qty')) {
                    $('.grouped-super', $(this).closest('tr')).hide();
                }
            } else {
                $('.grouped-super input, .grouped-super select', $(this).closest('tr')).prop('disabled', false);

                if($(this).closest('.grouped').hasClass('hide-options-zero-qty')) {
                    $('.grouped-super', $(this).closest('tr')).show();
                }
            }
        })

        $('input.qty').trigger('keyup');
    });
});
