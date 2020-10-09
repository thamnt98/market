/**
 * @category SM
 * @package SM_Help
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */
define(
    [
        'jquery',
        'jquery-ui-modules/widget',
        'mage/translate'
    ],
    function ($) {
        $.widget('trans.searchQuestions', {
            options: {
                currentStoreCode: '',
                questionBaseUrl: BASE_URL + '/question/',
                urlSuffix: '.html',
            },
            questionsKey: 'questions_list',
            canSaveToLocal: true,
            searchString: '',
            searchResultTag: '',

            /** @inheritdoc */
            _create: function () {
                const self = this;
                let timer;
                self.element.on('keyup', function (e) {
                    self.searchResultTag = self.element.parents('.help-search').find('.autocomplete-results');
                    self.searchString = self.element.val().toLowerCase();
                    if (self.searchString.length > 2) {
                        self.searchResultTag.empty().show();
                        let localData = localStorage.getItem(self.questionsKey);

                        if (self.canSaveToLocal && localData && localData !== "undefined") {
                            self._autoComplete(localData);
                        } else {
                            clearTimeout(timer);
                            timer = setTimeout(function () {
                                self._ajaxGetData($(this));
                            }, 1000);
                        }
                    }
                });
                /*$(document).on('click', function(e){
                    e.stopPropagation();
                    //check if the clicked area is dropDown or not
                    if (self.searchResultTag.has(e.target).length === 0) {
                        self.searchResultTag.hide();
                    }
                });*/

            },

            /**
             * @param {jQuery} element
             */
            _ajaxGetData: function (element) {
                const self = this,
                    loader = element.prev('.action-primary-loader');
                    let params = {};

                loader.show();

                if (self.canSaveToLocal) {
                    params = self._getDefaultParams();
                } else {
                    params = self._getAjaxSearchParams();
                }

                $.ajax({
                    method: "GET",
                    url: BASE_URL + 'rest/' + this.options.currentStoreCode + '/V1/help/search',
                    data: params,
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('Accept', 'application/json');
                        xhr.setRequestHeader('Content-Type', 'application/json');
                    },
                }).done(function (data) {
                    let result = JSON.stringify(data.items);

                    if (self.canSaveToLocal && self._roughSizeOfObject(data) < 5) {
                        localStorage.setItem(self.questionsKey, result);
                    } else {
                        self.canSaveToLocal = false;
                    }

                    self._autoComplete(result);
                    loader.hide();
                });
            },

            /**
             *
             * @param data
             * @private
             */
            _autoComplete: function (data) {
                const self = this,
                    searchResultTag = self.searchResultTag,
                     searchDropDown = self.element.parents('.help-search');
                    let count = 0;

                $.each(JSON.parse(data), function (index, item) {
                    let title = item.title,
                    titleToSearch = item.title.toLowerCase(),
                    indexStr = titleToSearch.indexOf(self.searchString);

                    if (indexStr !== -1) {
                        if (count > 9) {
                            return false;
                        }

                        count++;
                        let fistStr = title.substring(0, indexStr),
                        matchSearchStr = '<strong>' + title.substring(indexStr, self.searchString.length + indexStr) + '</strong>',
                        lastStr = title.substring(indexStr + self.searchString.length, title.length),
                        resultTitle = fistStr + matchSearchStr + lastStr;
                        let dataGtm = JSON.stringify({
                            query : item.title
                        });
                        searchResultTag.append(
                            `<li><a data-gtm-event="suggested_help" data-gtm='${dataGtm}' data-gtm-name="query" href="${self.options.questionBaseUrl + item.url_key + self.options.urlSuffix}">${resultTitle}</a></li>`
                        );

                        $.each(searchResultTag.children(), function (index, item) {
                            $(this).click(function () {
                                searchResultTag.hide();
                            });
                        });
                    }
                });

                if (searchResultTag.is(':empty')) {
                    searchResultTag.html(
                        "<li class='no-result'>" + $.mage.__('No results found.') + "</li>"
                    );
                }

                searchResultTag.find('[data-gtm-event="suggested_help"]').each(function () {
                    $(this).on('click', function () {
                        let query = $(this).text();
                        window.dataLayer = window.dataLayer || [];
                        window.dataLayer.push({
                            'event': 'suggested_help',
                            'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                            'userID': dataLayerSourceObjects.customer.userID,
                            'customerID': dataLayerSourceObjects.customer.customerID,
                            'customerType': dataLayerSourceObjects.customer.customerType,
                            'loyalty': dataLayerSourceObjects.customer.loyalty,
                            'customerStatus': dataLayerSourceObjects.customer.customerStatus,
                            'loginType': dataLayerSourceObjects.customer.loginType,
                            'store_name': dataLayerSourceObjects.customer.storeName,
                            'store_ID': dataLayerSourceObjects.customer.storeID,
                            'query': $.trim(query)
                        })
                    });
                });

            },

            /**
             * @returns object
             * @private
             */
            _getDefaultParams: function () {
                return {
                    "search_criteria": {
                        "filter_groups": [],
                        "sort_orders": [
                            {
                                'field': 'question_id',
                                'direction': "desc"
                        }
                        ],
                    },
                };
            },

            /**
             * @returns object
             * @private
             */
            _getAjaxSearchParams: function () {
                const self = this;

                return {
                    "search_criteria": {
                        "filter_groups": [
                            {
                                'filters': [
                                    {
                                        'field' : 'title',
                                        'value' : '%25' + self.searchString.toLowerCase() + '%25',
                                        'condition_type' : 'like'
                                }
                                ],
                        },
                        ],
                        "sortOrders": [
                            {
                                'field': 'question_id',
                                'direction': "DESC"
                        }
                        ],
                        "currentPage": 1,
                        "pageSize": 10
                    },
                }
            },

            /**
             *
             * @param object
             * @returns {number} MB
             * @private
             */
            _roughSizeOfObject: function (object) {
                const objectList = [];
                const stack = [object];
                let bytes = 0;

                while (stack.length) {
                    const value = stack.pop();

                    if (typeof value === 'boolean') {
                        bytes += 4;
                    } else if (typeof value === 'string') {
                        bytes += value.length * 2;
                    } else if (typeof value === 'number') {
                        bytes += 8;
                    } else if (
                        typeof value === 'object'
                        && objectList.indexOf(value) === -1
                    ) {
                        objectList.push(value);

                        for (let i in value) {
                            stack.push(value[i]);
                        }
                    }
                }
                return bytes / 1000000;
            },
        });
    }
)
