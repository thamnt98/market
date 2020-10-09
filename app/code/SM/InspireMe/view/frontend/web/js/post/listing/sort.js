/*
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

define([
    'jquery',
    'gtmInspireMe',
    'jquery-ui-modules/widget',
], function ($, gtm) {
    'use strict';

    /**
     * SortInspirePost Widget - this widget is setting cookie and submitting form according to toolbar controls
     */
    $.widget('mage.sortInspirePost', {

        options: {
            orderAsc: '#order-asc',
            orderDesc: '#order-desc',
            directionDesc: 'desc',
            direction: 'kb_article_list_dir',
            url: ''
        },

        /** @inheritdoc */
        _create: function () {
            this._bind($(this.options.orderAsc), this.options.direction, this.options.directionDesc);
            this._bind($(this.options.orderDesc), this.options.direction, this.options.directionDesc);
        },

        /** @inheritdoc */
        _bind: function (element, paramName, defaultValue) {
            element.on('click', {
                element  : element,
                paramName: paramName,
                'default': defaultValue
            }, $.proxy(this._process, this));
        },

        /**
         * @param {jQuery.Event} event
         * @private
         */
        _process: function (event) {
            if (event.data.element.hasClass('checked')) {
                event.preventDefault();
            } else {
                $(this.options.orderAsc).toggleClass('checked');
                $(this.options.orderDesc).toggleClass('checked');
                let data = [];
                data['sort_by'] = event.data.element.find('span').text();
                //Todo Remove Debug
                console.log(data);
                gtm.push('sortBy_inspire_me',data);
                let self = this;
                setTimeout(function () {
                    self.changeUrl(
                        event.data.paramName,
                        $(event.currentTarget).data('value'),
                        event.data.default
                    );
                },2000);
            }
        },

        /**
         * @param {String} paramName
         * @param {*} paramValue
         * @param {*} defaultValue
         */
        changeUrl: function (paramName, paramValue, defaultValue) {
            var decode = window.decodeURIComponent,
                urlPaths = this.options.url.split('?'),
                baseUrl = urlPaths[0],
                urlParams = urlPaths[1] ? urlPaths[1].split('&') : [],
                paramData = {},
                parameters, i;

            for (i = 0; i < urlParams.length; i++) {
                parameters = urlParams[i].split('=');
                paramData[decode(parameters[0])] = parameters[1] !== undefined ?
                    decode(parameters[1].replace(/\+/g, '%20')) :
                    '';
            }
            paramData[paramName] = paramValue;

            if (paramValue == defaultValue) { //eslint-disable-line eqeqeq
                delete paramData[paramName];
            }
            paramData = $.param(paramData);
            location.href = baseUrl + (paramData.length ? '?' + paramData : '');
        }
    });

    return $.mage.sortInspirePost;
});
