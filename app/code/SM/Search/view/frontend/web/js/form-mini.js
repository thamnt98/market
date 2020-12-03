define(
    [
        'jquery',
        'mage/translate'
    ],
    function ($) {
        "use strict";

        let mod = {};

        mod.init = function () {
            mod.initElements();
            mod.initEvents();
            mod.updateValueForInputCat();
            mod.clickUISameSelect();
        };

        mod.initElements = function () {
            mod.container = $('#search_mini_form');
            mod.input = mod.container.find('#search');
            mod.dropdown = mod.container.find('input[name="cat"]');
            mod.wrapper = mod.container.find('.search-additional-wrap');
            mod.latestSearch = mod.wrapper.find('.latest-search');
            mod.latestSearchContent = mod.latestSearch.find('.content-wrap');
            mod.popularSearch = mod.wrapper.find('.popular-search');
            mod.popularSearchContent = mod.popularSearch.find('.content-wrap');
            mod.latestViewed = mod.wrapper.find('.latest-viewed-product');
            mod.latestViewedContent = mod.latestViewed.find('.content-wrap');
            mod.iconSearch = mod.container.find(".action.search");
            mod.itemCat = mod.container.find(".search-cat .item-cat");
            mod.labelSearchCat = mod.container.find(".label-search-cat");
            mod.searchCat = mod.container.find(".search-cat");
        };

        mod.initGTM = function() {
            window.eventName = "suggested_search";
            let pushGTM = (event) => {
                if (typeof dataLayerSourceObjects !== 'undefined' && dataLayerSourceObjects.customer.loginType !== "null") {
                    if(!event){
                        event = 'suggested_search';
                    }
                    let searchVal = (event === "search_query") ? mod.input.val() :
                        (mod.container.find('li[class="selected"][role="option"]').find('span.qs-option-name').length !== 0 ?
                            mod.container.find('li[class="selected"][role="option"]').find('span.qs-option-name').text() :
                            (mod.container.find('li[role="option"]').find('span.qs-option-name.selected').length !== 0 ?
                                mod.container.find('li[role="option"]').find('span.qs-option-name.selected').text() : ""));
                    let searchCategory = mod.labelSearchCat.text();
                    window.dataLayer = window.dataLayer || [];
                    window.dataLayer.push({
                        'event': event,
                        'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                        'userID': dataLayerSourceObjects.customer.userID,
                        'customerID': dataLayerSourceObjects.customer.customerID,
                        'customerType': dataLayerSourceObjects.customer.customerType,
                        'loyalty': dataLayerSourceObjects.customer.loyalty,
                        'customerStatus': dataLayerSourceObjects.customer.customerStatus,
                        'loginType': dataLayerSourceObjects.customer.loginType,
                        'store_name': dataLayerSourceObjects.customer.storeName,
                        'store_ID': dataLayerSourceObjects.customer.storeID,
                        'query': searchVal.trim(),
                        'category': searchCategory.trim()
                    });
                }
            };
            mod.container.on('submit', function (e) {
                pushGTM(window.eventName);
            });
            mod.container.find('input').bind('keypress keydown keyup',function (e) {
                if (e.keyCode == 13) {
                    window.eventName="search_query";
                    e.preventDefault();
                    return false;
                }
            });
            mod.iconSearch.on('click', function (event) {
                event.preventDefault();
                window.eventName = "search_query";
                mod.container.trigger('submit');
            });

            return pushGTM;
        };

        mod.initEvents = function () {
            mod.initInputEvent();
            mod.initDropdownEvent();
            if (mod.isLoggedIn()) {
                mod.loadLatestSearch();
                mod.initLatestSearchEvent();
                mod.loadLatestViewedProduct();
            }
            mod.loadPopularSearch();
            mod.initGTM();
        };

        mod.initInputEvent = function() {
            mod.input.on('click', function () {
                if ($(this).val().trim().length === 0) {
                    mod.showWrap();
                }
            });

            mod.input.on('keyup', function () {
                if ($(this).val().trim().length === 0) {
                    mod.showWrap();
                }
                else {
                    mod.hideWrap();
                }
            });

            mod.input.on('blur', function () {
                setTimeout(mod.hideWrap, 250);
            });
        };

        mod.initDropdownEvent = function() {
            mod.dropdown.on('change', function () {
                mod.toggleVisibleLatestSearch();
                mod.loadPopularSearch();
            });
        };

        mod.initLatestSearchEvent = function() {
            mod.latestSearch.on('click', '.delete-all', function () {
                $.ajax({
                    type: "DELETE",
                    url: BASE_URL + 'rest/V1/search/mine/delete-search/all',
                    beforeSend: function(xhr){
                        xhr.setRequestHeader('Accept', 'application/json');
                        xhr.setRequestHeader('Content-Type', 'application/json');
                    },
                    success: function (response) {
                        mod.latestSearchContent.html('');
                        mod.latestSearch.hide();
                    }
                });
            });
            mod.latestSearchContent.on('click', '.delete-query', function () {
                var element = $(this).parent();
                var query = $(this).data('query');
                $.ajax({
                    type: "DELETE",
                    url: BASE_URL + 'rest/V1/search/mine/delete-search/' + encodeURI(query),
                    beforeSend: function(xhr){
                        xhr.setRequestHeader('Accept', 'application/json');
                        xhr.setRequestHeader('Content-Type', 'application/json');
                    },
                    success: function (response) {
                        element.remove();
                        if (mod.latestSearchContent.find('.latest-search-query').length === 0) {
                            mod.latestSearch.hide();
                        }
                    }
                });
            });
        };

        mod.showWrap = function() {
            if ($('#modals-overlay-default').length === 0) {

                var heightHeader = $('.page-wrapper .page-header').outerHeight();
                $('body').append('<div class="modals-overlay" style="top: '+ heightHeader +'px;z-index: 10;" id="modals-overlay-default"></div>');
            }
            let catId = mod.dropdown.val();
            if (catId === '' || catId == '0') {
                mod.latestSearch.show();
            } else {
                mod.latestSearch.hide();
            }
            mod.loadPopularSearch();
            mod.wrapper.show();
        };

        mod.hideWrap = function() {
            mod.wrapper.hide();
            if ($('#search_autocomplete').is(':hidden') && $('#modals-overlay-default').length > 0) {
                $('#modals-overlay-default').remove();
            }
        };

        mod.toggleVisibleLatestSearch = function() {
            if (mod.dropdown.val() === '') {
                if (mod.latestSearchContent.find('.latest-search-query').length > 0) {
                    mod.latestSearch.show();
                    return true;
                }
            }

            mod.latestSearch.hide();
            return false;
        };

        mod.loadLatestSearch = function() {
            mod.latestSearch.hide();
            $.ajax({
                type: "GET",
                url: BASE_URL + 'rest/V1/search/mine/latest-search',
                beforeSend: function(xhr){
                    xhr.setRequestHeader('Accept', 'application/json');
                    xhr.setRequestHeader('Content-Type', 'application/json');
                },
                success: function (response) {
                    if (response.length > 0) {
                        $.each(response, function (index, item) {
                            var uri = BASE_URL + 'catalogsearch/result/?cat=&q=' + item['query_text'];
                            mod.latestSearchContent.append(
                                '<span class="latest-search-query item-search">' +
                                    '<a href="' + uri + '" data-gtm-event="latest_search" data-gtm-name="search"' +
                                        'data-gtm=\'{"query": "' + item['query_text'] + '"}\'' +
                                    '>' + item['query_text'] + '</a>' +
                                    '<span class="delete-query apl-cancel" data-gtm-event="delete_latest_search" ' +
                                        'data-gtm-name="search" data-gtm=\'{"query": "' + item['query_text'] + '"}\'' +
                                        ' data-query="'+item['query_text']+'"></span>' +
                                '</span>'
                            );
                        });
                        mod.toggleVisibleLatestSearch();
                    }
                }
            });
        };

        mod.loadPopularSearch = function() {
            let catId = mod.dropdown.val();
            let urlPath = 'rest/V1/search/popular-search';
            let oldCatId = mod.popularSearchContent.find('span.popular-search-query').data('cat-id');
            if (catId) {
                urlPath = 'rest/V1/search/popular-search/category/' + catId;
            }
            if(oldCatId == catId) {
                return;
            }

            mod.popularSearch.hide();
            $.ajax({
                type: "GET",
                url: BASE_URL + urlPath,
                beforeSend: function(xhr){
                    xhr.setRequestHeader('Accept', 'application/json');
                    xhr.setRequestHeader('Content-Type', 'application/json');
                },
                success: function (response) {
                    if (response.length > 0) {
                        mod.popularSearchContent.html('');
                        $.each(response, function (index, item) {
                            var uri = BASE_URL + 'catalogsearch/result/?cat='+catId+'&q=' + item['query_text'];
                            mod.popularSearchContent.append(
                                '<span class="popular-search-query item-search" data-cat-id="' + catId +'">' +
                                '<a href="' + uri + '">' + item['query_text'] + '</a>' +
                                '</span>'
                            );
                        });
                        mod.popularSearch.show();
                    }
                }
            });
        };

        mod.loadLatestViewedProduct = function() {
            //mod.latestViewed.hide();
            $.ajax({
                type: "GET",
                url: BASE_URL + 'reports/ajax/latestViewedProduct',
                cache: false,
                success: function (response) {
                    if (response) {
                        mod.latestViewedContent.html(response);
                        //mod.latestViewed.show();
                    }
                }
            });
        };

        mod.isLoggedIn = function () {
            return $('body').hasClass('login');
        };

        mod.updateValueForInputCat = function() {
            $('.jsResizeSelect').on('click', ".item-cat", function () {
                var liItemClickVal = $(this).attr('value'),
                    liItemClickText = $(this).text().trim();

                if(liItemClickText != ''){
                    mod.dropdown.attr('label', liItemClickText);
                    mod.dropdown.attr('value', liItemClickVal);
                }
            });
        };
        mod.clickUISameSelect = function () {
            $("body").on("click",function(){
                mod.searchCat.hide();
            });
            mod.labelSearchCat.on("click",function(event){
                 event.stopPropagation();
                mod.searchCat.show();
            });
            mod.itemCat.on("click",function(){
                let value = $(this).text().trim();
                mod.labelSearchCat.text(value);
            });
        };
        return mod;
    }
);
