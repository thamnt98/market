define(
    [
        'jquery',
        'mage/translate',
        'mage/validation'
    ],
    function ($) {
        "use strict";

        $('#input-search').keypress(function (e) {
            if (e.which == 13) {
                var currentSort = '';
                var url = window.location.href;
                if($.fn.getValueParam('sort') !== null) {
                    currentSort = $.fn.getValueParam('sort');
                    url = $.fn.setValueParameter(url, 'sort', currentSort);
                }
                var keyValue = $('#input-search').val();
                url = $.fn.setValueParameter(url, 'key', keyValue);
                window.location.href = url;
            }
        });

        $('.review-search').on('click', '#btn-search-item', function () {
            var currentSort = '';
            var url = window.location.href;
            if($.fn.getValueParam('sort') !== null) {
                currentSort = $.fn.getValueParam('sort');
                url = $.fn.setValueParameter(url, 'sort', currentSort);
            }
            var keyValue = $('#input-search').val();
            url = $.fn.setValueParameter(url, 'key', keyValue);
            window.location.href = url;
        });

        $('.review-sortby').on('click', '.radio-custom', function () {
            $('.review-sortby .radio-custom input[name="sort"]').removeAttr('checked');
            $(this).find('input[name="sort"]').attr('checked', true);
            var currentKey = '';
            var url = window.location.href;
            if($.fn.getValueParam('key') !== null) {
                currentKey = $.fn.getValueParam('key');
                url = $.fn.setValueParameter(url, 'key', currentKey);
            }
            var sortValue = $(this).find('input[name="sort"]').attr('value');
            url = $.fn.setValueParameter(url, 'sort', sortValue);
            window.location.href = url;
        });

        $.fn.getValueParam = function(param) {
            let sPageURL = window.location.href.split('?');
            let sURLVariables = (window.location.href.indexOf('?') > 0) ? sPageURL[1].split('&') : sPageURL[0].split('&');
            for (let i = 0; i < sURLVariables.length; i++){
                let sParameterName = sURLVariables[i].split('=');
                if (sParameterName[0] == param)
                {
                    return sParameterName[1];
                }
            }

            return null;
        };

        $.fn.setValueParameter = function(url, param, value) {
            let sPageURL = window.location.href.split('?');
            let sURLVariables = (window.location.href.indexOf('?') > 0) ? sPageURL[1].split('&') : sPageURL[0].split('&');
            let checkParam = 0;
            let params = [];
            for (let i = 0; i < sURLVariables.length; i++){
                let sParameterName = sURLVariables[i].split('=');
                if (sParameterName[0] == param)
                {
                    checkParam = 1;
                    sParameterName[1] = value;
                }

                params.push(sParameterName[0] + '=' + sParameterName[1]);
            }
            if(!checkParam) {
                url = (url.indexOf('?') > 0) ? url + '&' + param +'=' + value: url + '?' + param +'=' + value;
            } else {
                url = sPageURL[0] + '?' + params.join('&');
            }
            return url;
        };
    }
);
