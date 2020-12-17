/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Theme/js/model/breadcrumb-list',
    'mage/translate'
], function ($, breadcrumbList) {
    'use strict';

    return function (widget) {

        $.widget('mage.breadcrumbs', widget, {
            options: {
                categoryUrlSuffix: '',
                useCategoryPathInUrl: false,
                product: '',
                categoryItemSelector: '.category-item',
                menuContainer: '[data-action="navigation"] > ul'
            },

            /** @inheritdoc */
            _render: function () {
                this._appendCatalogCrumbs();
                this._super();
            },

            /**
             * Append category and product crumbs.
             *
             * @private
             */
            _appendCatalogCrumbs: function () {
                var categoryCrumbs = this._resolveCategoryCrumbs();

                categoryCrumbs.forEach(function (crumbInfo) {
                    breadcrumbList.push(crumbInfo);
                });

                if (this.options.product) {
                    breadcrumbList.push(this._getProductCrumb());
                }
            },

            /**
             * Resolve categories crumbs.
             *
             * @return Array
             * @private
             */
            _resolveCategoryCrumbs: function () {
                var menuItem = this._resolveCategoryMenuItem(),
                    categoryCrumbs = [];
                //case go to pdp from category
                if (menuItem !== null && menuItem.length) {
                    categoryCrumbs.unshift(this._getCategoryCrumb(menuItem));

                    while ((menuItem = this._getParentMenuItem(menuItem)) !== null) {
                        if(menuItem.attr('href') !== '#'){
                            categoryCrumbs.unshift(this._getCategoryCrumb(menuItem));
                        }
                    }
                } else {
                    //case go to pdp from cms
                    var categoryUrl = null;
                    //get category Url
                    if (this.options.useCategoryPathInUrl) {
                        categoryUrl = window.location.href.split('?')[0];
                        categoryUrl = categoryUrl.substring(0, categoryUrl.lastIndexOf('/')) +
                            this.options.categoryUrlSuffix;
                    } else {
                        categoryUrl = document.referrer;
                        if (categoryUrl.indexOf('?') > 0) {
                            categoryUrl = categoryUrl.substr(0, categoryUrl.indexOf('?'));
                        }
                    }
                    // if user visit from node product on page
                    if (categoryUrl != '' && window.location.href != categoryUrl) {
                        var pathUrlArray = categoryUrl.split("/");
                        var cmsPathUrlArray = pathUrlArray,
                            cmsPathUrl = pathUrlArray[pathUrlArray.length - 2],
                            lastItemOfPathUrl = pathUrlArray[pathUrlArray.length - 1],
                            categoryName = lastItemOfPathUrl.indexOf('.html') != -1 ? lastItemOfPathUrl.slice(0, lastItemOfPathUrl.length - 5) : lastItemOfPathUrl,
                            upcaseCategoryName = categoryName.charAt(0).toUpperCase() + categoryName.slice(1),
                            newCatItem = null,
                            cmsItem = null;

                        var cmsUrlArr = cmsPathUrlArray.slice(0, cmsPathUrlArray.length - 1),
                            cmsPathUrlString = cmsUrlArr.join('/');
                        if(categoryName) {
                            newCatItem = {
                                'name': 'category',
                                'label': upcaseCategoryName,
                                'link': categoryUrl,
                                'title': categoryName
                            };
                            categoryCrumbs.unshift(newCatItem);
                        }

                        if (cmsPathUrlArray.length > 4 && cmsPathUrl != '') {
                            var cmsPathUrlName = cmsPathUrl.charAt(0).toUpperCase() + cmsPathUrl.slice(1);
                            var page = cmsPathUrlArray.find(element => (element == 'order' || element == 'review' || element == 'product_compare' || element == 'shoppinglist' || element == 'cart'));
                            if (page) {
                                switch (page) {
                                    case 'order': {
                                        cmsPathUrlName = $.mage.__('My Orders');
                                    } break;
                                    case 'review' : {
                                        cmsPathUrlName = $.mage.__('My Reviews');
                                    } break;
                                    case 'product_compare' : {
                                        cmsPathUrlName = $.mage.__('Compare Products');
                                    } break;
                                    case 'shoppinglist' : {
                                        cmsPathUrlName = $.mage.__('Shopping List');
                                    } break;
                                    case 'cart': {
                                        cmsPathUrlName = $.mage.__('Shopping Cart');
                                    } break;
                                }
                            }
                            cmsItem = {
                                'name': 'cms-category',
                                'label': cmsPathUrlName,
                                'link': cmsPathUrlString,
                                'title': cmsPathUrlName
                            };
                            categoryCrumbs.unshift(cmsItem);
                        }
                    }

                }

                return categoryCrumbs;
            },

            /**
             * Returns crumb data.
             *
             * @param {Object} menuItem
             * @return {Object}
             * @private
             */
            _getCategoryCrumb: function (menuItem) {
                return {
                    'name': 'category',
                    'label': menuItem.text(),
                    'link': menuItem.attr('href'),
                    'title': ''
                };
            },

            /**
             * Returns product crumb.
             *
             * @return {Object}
             * @private
             */
            _getProductCrumb: function () {
                return {
                    'name': 'product',
                    'label': this.options.product,
                    'link': '',
                    'title': ''
                };
            },

            /**
             * Find parent menu item for current.
             *
             * @param {Object} menuItem
             * @return {Object|null}
             * @private
             */
            _getParentMenuItem: function (menuItem) {
                var classes,
                    classNav,
                    parentClass,
                    parentMenuItem = null;

                if (!menuItem) {
                    return null;
                }

                classes = menuItem.parent().attr('class');
                classNav = classes.match(/(nav\-)[0-9]+(\-[0-9]+)+/gi);

                if (classNav) {
                    classNav = classNav[0];
                    parentClass = classNav.substr(0, classNav.lastIndexOf('-'));

                    if (parentClass.lastIndexOf('-') !== -1) {
                        parentMenuItem = $(this.options.menuContainer).find('.' + parentClass + ' > a');
                        parentMenuItem = parentMenuItem.length ? parentMenuItem : null;
                    }
                }

                return parentMenuItem;
            },

            /**
             * Returns category menu item.
             *
             * Tries to resolve category from url or from referrer as fallback and
             * find menu item from navigation menu by category url.
             *
             * @return {Object|null}
             * @private
             */
            _resolveCategoryMenuItem: function () {
                var categoryUrl = this._resolveCategoryUrl(),
                    menu = $(this.options.menuContainer),
                    categoryMenuItem = null;

                if (categoryUrl && menu.length) {
                     menu.find(
                        this.options.categoryItemSelector +
                        ' > a[href="' + categoryUrl + '"]'
                    ).each(function (){
                         categoryMenuItem = $(this);
                     });

                }

                return categoryMenuItem;
            },

            /**
             * Returns category url.
             *
             * @return {String}
             * @private
             */
            _resolveCategoryUrl: function () {
                var categoryUrl;

                if (this.options.useCategoryPathInUrl) {
                    // In case category path is used in product url - resolve category url from current url.
                    categoryUrl = window.location.href.split('?')[0];
                    categoryUrl = categoryUrl.substring(0, categoryUrl.lastIndexOf('/')) +
                        this.options.categoryUrlSuffix;
                } else {
                    // In other case - try to resolve it from referrer (without parameters).
                    categoryUrl = document.referrer;

                    if (categoryUrl.indexOf('?') > 0) {
                        categoryUrl = categoryUrl.substr(0, categoryUrl.indexOf('?'));
                    }
                }

                return categoryUrl;
            }
        });

        return $.mage.breadcrumbs;
    };
});
