/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'jquery',
    'underscore',
    'mage/template',
    'matchMedia',
    'SearchMiniForm',
    'jquery-ui-modules/widget',
    'jquery-ui-modules/core',
    'mage/translate'
], function ($, _, mageTemplate, mediaCheck,searchMiniForm) {
    'use strict';

    /**
     * Check whether the incoming string is not empty or if doesn't consist of spaces.
     *
     * @param {String} value - Value to check.
     * @returns {Boolean}
     */
    function isEmpty(value) {
        return value.length === 0 || value == null || /^\s+$/.test(value);
    }

    $.widget('mage.quickSearch', {
        options: {
            autocomplete: 'off',
            minSearchLength: 3,
            responseFieldElements: 'ul li',
            selectClass: 'selected',
            template:
                '<li class="<%- data.row_class %>" id="qs-option-<%- data.index %>" role="option" data-url="<%- data.url %>">' +
                    '<span class="qs-option-name">' +
                       ' <%- data.name %>' +
                    '</span>' +
                    '<span aria-hidden="true" class="amount">' +
                        '<%- data.category_names %>' +
                    '</span>' +
                '</li>',
            submitBtn: 'button[type="submit"]',
            searchLabel: '[data-role=minisearch-label]',
            isExpandable: null,
            suggestionDelay: 300
        },

        /** @inheritdoc */
        _create: function () {
            this.responseList = {
                indexList: null,
                selected: null
            };
            this.autoComplete = $(this.options.destinationSelector);
            this.searchForm = $(this.options.formSelector);
            this.submitBtn = this.searchForm.find(this.options.submitBtn)[0];
            this.searchLabel = this.searchForm.find(this.options.searchLabel);
            this.isExpandable = this.options.isExpandable;

            _.bindAll(this, '_onKeyDown', '_onPropertyChange', '_onSubmit');

            this.submitBtn.disabled = true;

            this.element.attr('autocomplete', this.options.autocomplete);

            mediaCheck({
                media: '(max-width: 768px)',
                entry: function () {
                    this.isExpandable = true;
                }.bind(this),
                exit: function () {
                    this.isExpandable = true;
                }.bind(this)
            });

            this.searchLabel.on('click', function (e) {
                // allow input to lose its' focus when clicking on label
                if (this.isExpandable && this.isActive()) {
                    e.preventDefault();
                }
            }.bind(this));

            this.element.on('blur', $.proxy(function () {
                if (!this.searchLabel.hasClass('active')) {
                    return;
                }

                setTimeout($.proxy(function () {
                    if (this.autoComplete.is(':hidden')) {
                        this.setActiveState(false);
                    } else {
                        this.element.trigger('focus');
                    }
                    this.autoComplete.hide();
                    if ($('#modals-overlay-default').length > 0) {
                        $('#modals-overlay-default').remove();
                    }
                    this._updateAriaHasPopup(false);
                }, this), 250);
            }, this));

            this.element.on('click', $.proxy(function () {
                if (this.element.val().length > 0 && this.autoComplete.html()) {
                    if ($('#modals-overlay-default').length === 0) {
                        var heightHeader = $('.page-wrapper .page-header').outerHeight();

                        $('body').append('<div class="modals-overlay" style="top: '+ heightHeader +'px" id="modals-overlay-default"></div>');
                    }
                    this.autoComplete.show();
                }
            }, this));

            if (this.element.get(0) === document.activeElement) {
                this.setActiveState(true);
            }

            this.element.on('focus', this.setActiveState.bind(this, true));
            this.element.on('keydown', this._onKeyDown);
            // Prevent spamming the server with requests by waiting till the user has stopped typing for period of time
            this.element.on('input propertychange', _.debounce(this._onPropertyChange, this.options.suggestionDelay));

            this.searchForm.on('submit', $.proxy(function (e) {
                this._onSubmit(e);
                this._updateAriaHasPopup(false);
            }, this));
        },

        /**
         * Checks if search field is active.
         *
         * @returns {Boolean}
         */
        isActive: function () {
            return this.searchLabel.hasClass('active');
        },

        /**
         * Sets state of the search field to provided value.
         *
         * @param {Boolean} isActive
         */
        setActiveState: function (isActive) {
            var searchValue;

            this.searchForm.toggleClass('active', isActive);
            this.searchLabel.toggleClass('active', isActive);

            if (this.isExpandable) {
                this.element.attr('aria-expanded', isActive);
                searchValue = this.element.val();
                this.element.val('');
                this.element.val(searchValue);
            }
        },

        /**
         * @private
         * @return {Element} The first element in the suggestion list.
         */
        _getFirstVisibleElement: function () {
            return this.responseList.indexList ? this.responseList.indexList.first() : false;
        },

        /**
         * @private
         * @return {Element} The last element in the suggestion list.
         */
        _getLastElement: function () {
            return this.responseList.indexList ? this.responseList.indexList.last() : false;
        },

        /**
         * @private
         * @param {Boolean} show - Set attribute aria-haspopup to "true/false" for element.
         */
        _updateAriaHasPopup: function (show) {
            if (show) {
                this.element.attr('aria-haspopup', 'true');
            } else {
                this.element.attr('aria-haspopup', 'false');
            }
        },

        /**
         * Clears the item selected from the suggestion list and resets the suggestion list.
         * @private
         * @param {Boolean} all - Controls whether to clear the suggestion list.
         */
        _resetResponseList: function (all) {
            this.responseList.selected = null;

            if (all === true) {
                this.responseList.indexList = null;
            }
        },

        /**
         * Executes when the search box is submitted. Sets the search input field to the
         * value of the selected item.
         * @private
         * @param {Event} e - The submit event
         */
        _onSubmit: function (e) {
            var value = this.element.val();

            if (isEmpty(value)) {
                e.preventDefault();
            }

            if (this.responseList.selected) {
                this.element.val(this.responseList.selected.find('.qs-option-name').text());
            }
        },

        /**
         * Executes when keys are pressed in the search input field. Performs specific actions
         * depending on which keys are pressed.
         * @private
         * @param {Event} e - The key down event
         * @return {Boolean} Default return type for any unhandled keys
         */
        _onKeyDown: function (e) {
            var keyCode = e.keyCode || e.which;

            switch (keyCode) {
                case $.ui.keyCode.HOME:
                    if (this._getFirstVisibleElement()) {
                        this._getFirstVisibleElement().addClass(this.options.selectClass);
                        this.responseList.selected = this._getFirstVisibleElement();
                    }
                    break;

                case $.ui.keyCode.END:
                    if (this._getLastElement()) {
                        this._getLastElement().addClass(this.options.selectClass);
                        this.responseList.selected = this._getLastElement();
                    }
                    break;

                case $.ui.keyCode.ESCAPE:
                    this._resetResponseList(true);
                    this.autoComplete.hide();
                    if ($('#modals-overlay-default').length > 0) {
                        $('#modals-overlay-default').remove();
                    }
                    break;

                case $.ui.keyCode.ENTER:
                    if (this.responseList.selected && this.responseList.selected.data('url')) {
                        this.element.val(this.responseList.selected.find('.qs-option-name').text());
                        window.location.href = this.responseList.selected.data('url');
                        return false;
                    }
                    else {
                        this.searchForm.trigger('submit');
                        e.preventDefault();
                    }
                    break;

                case $.ui.keyCode.DOWN:
                    if (this.responseList.indexList) {
                        if (!this.responseList.selected) {  //eslint-disable-line max-depth
                            this._getFirstVisibleElement().addClass(this.options.selectClass);
                            this.responseList.selected = this._getFirstVisibleElement();
                        } else if (!this._getLastElement().hasClass(this.options.selectClass)) {
                            this.responseList.selected = this.responseList.selected
                                .removeClass(this.options.selectClass).next().addClass(this.options.selectClass);
                        } else {
                            this.responseList.selected.removeClass(this.options.selectClass);
                            this._getFirstVisibleElement().addClass(this.options.selectClass);
                            this.responseList.selected = this._getFirstVisibleElement();
                        }
                        this.element.val(this.responseList.selected.find('.qs-option-name').text());
                        this.element.attr('aria-activedescendant', this.responseList.selected.attr('id'));
                    }
                    break;

                case $.ui.keyCode.UP:
                    if (this.responseList.indexList !== null) {
                        if (!this._getFirstVisibleElement().hasClass(this.options.selectClass)) {
                            this.responseList.selected = this.responseList.selected
                                .removeClass(this.options.selectClass).prev().addClass(this.options.selectClass);

                        } else {
                            this.responseList.selected.removeClass(this.options.selectClass);
                            this._getLastElement().addClass(this.options.selectClass);
                            this.responseList.selected = this._getLastElement();
                        }
                        this.element.val(this.responseList.selected.find('.qs-option-name').text());
                        this.element.attr('aria-activedescendant', this.responseList.selected.attr('id'));
                    }
                    break;
                default:
                    return true;
            }
        },

        /**
         * Executes when the value of the search input field changes. Executes a GET request
         * to populate a suggestion list based on entered text. Handles click (select), hover,
         * and mouseout events on the populated suggestion list dropdown.
         * @private
         */
        _onPropertyChange: function () {
            var searchField = this.element,
                clonePosition = {
                    position: 'absolute',
                    // Removed to fix display issues
                    // left: searchField.offset().left,
                    // top: searchField.offset().top + searchField.outerHeight(),
                    width: searchField.outerWidth()
                },
                source = this.options.template,
                template = mageTemplate(source),
                dropdown = $('<ul role="listbox"></ul>'),
                value = this.element.val();

            this.submitBtn.disabled = isEmpty(value);

            if (value.length >= parseInt(this.options.minSearchLength, 10)) {
                // hide popup
                this.autoComplete.html('').hide();
                if ($('#modals-overlay-default').length > 0) {
                    $('#modals-overlay-default').remove();
                }

                $.getJSON(this.options.url, {
                    q: value,
                    cat: $('#search_mini_form input[name="cat"]').val()
                }, $.proxy(function (data) {
                    if (data.products && data.products.length) {
                        $.each(data.products, function (index, element) {
                            var html;

                            element.index = index;
                            html = template({
                                data: element
                            });

                            html = html.replace(/&lt;/g,'<').replace(/&gt;/g,'>');
                            console.log(html);

                            dropdown.append(html);
                        });

                        // show popup
                        if ($('#modals-overlay-default').length === 0) {
                            var heightHeader = $('.page-wrapper .page-header').outerHeight();
                            $('body').append('<div class="modals-overlay" style="top: '+ heightHeader +'px" id="modals-overlay-default"></div>');
                        }

                        this._resetResponseList(true);

                        this.responseList.indexList = this.autoComplete.html(dropdown)
                            .css(clonePosition)
                            .show()
                            .find(this.options.responseFieldElements + ':visible');

                        this.element.removeAttr('aria-activedescendant');

                        if (this.responseList.indexList.length) {
                            this._updateAriaHasPopup(true);
                        } else {
                            this._updateAriaHasPopup(false);
                        }

                        this.responseList.indexList
                            .on('click', function (e) {
                                this.responseList.selected = $(e.currentTarget);
                                if (this.responseList.selected.data('url')) {
                                    let pushGTM = searchMiniForm.initGTM();
                                    pushGTM();
                                    this.element.val(this.responseList.selected.find('.qs-option-name').text());
                                    window.location.href = this.responseList.selected.data('url');
                                }
                                else {
                                    this.searchForm.trigger('submit');
                                }
                            }.bind(this))
                            .on('mouseenter mouseleave', function (e) {
                                this.responseList.indexList.removeClass(this.options.selectClass);
                                $(e.target).addClass(this.options.selectClass);
                                this.responseList.selected = $(e.target);
                                this.element.attr('aria-activedescendant', $(e.target).attr('id'));
                            }.bind(this))
                            .on('mouseout', function (e) {
                                if (!this._getLastElement() &&
                                    this._getLastElement().hasClass(this.options.selectClass)) {
                                    $(e.target).removeClass(this.options.selectClass);
                                    this._resetResponseList(false);
                                }
                            }.bind(this));

                        // handle additional block
                        if (data.type === 'match_results') {
                            // handle recommendation for you
                            var baseElement = $('.search-additional-wrap .latest-viewed-product');

                            this.autoComplete.append(
                                baseElement.clone()
                            );
                        }
                        else {
                            // handle latest search
                            var baseElement = $('.search-additional-wrap .latest-search');

                            this.autoComplete.append(
                                baseElement.clone()
                            );

                            var latestSearch = this.autoComplete.find('.latest-search');
                            latestSearch.hide();
                            latestSearch.find('.delete-all').remove();

                            var contentWrap = latestSearch.find('.content-wrap');
                            if (contentWrap.html()) {
                                latestSearch.show();
                                contentWrap.on('click', '.delete-query', function () {
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
                                            baseElement.find('.delete-query[data-query="' + query + '"]').parent().remove();
                                            if (contentWrap.find('.latest-search-query').length === 0) {
                                                latestSearch.hide();
                                                baseElement.hide();
                                            }
                                        }
                                    });
                                });
                            }
                        }
                    } else {
                        this._resetResponseList(true);
                        this.autoComplete.hide();
                        if ($('#modals-overlay-default').length > 0) {
                            $('#modals-overlay-default').remove();
                        }
                        this._updateAriaHasPopup(false);
                        this.element.removeAttr('aria-activedescendant');
                    }
                }, this));
            } else {
                this._resetResponseList(true);
                this.autoComplete.hide();
                if ($('.search-additional-wrap').is(':hidden') && $('#modals-overlay-default').length > 0) {
                    $('#modals-overlay-default').remove();
                }
                this._updateAriaHasPopup(false);
                this.element.removeAttr('aria-activedescendant');
            }
        }
    });

    return $.mage.quickSearch;
});
