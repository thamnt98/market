define([
    'jquery',
    'eJs',
    'moment',
    'Magento_Ui/js/lib/view/utils/async',
    'SM_GTM/js/gtm/action/refresh-data'
], function ($, ejs, moment, async, refreshData) {
    let eventCreate = {};

    function toJson(data)
    {
        let obj;

        if (typeof data === 'object') {
            obj = Object.assign({}, data);
        } else {
            obj = JSON.parse(data.trim());
        }

        for (let key in obj) {
            if (obj.hasOwnProperty(key)) {
                if (typeof obj[key] === 'object') {
                    obj[key] = toJson(obj[key]);
                } else if (/function\s*\(.*\)\s*{.*}/.test(obj[key])) { // property is function.
                    obj[key] = eval("(" + obj[key] + ")");
                } else if (typeof obj[key] === 'string') {
                    obj[key] = $('<textarea />').html(obj[key]).text();
                }
            }
        }

        return obj;
    }

    return (selector, domEvent, template) => {
        let pushDataObjClosure = (e) => {
            if (e instanceof $.Event &&
                $(e.currentTarget).attr('href') &&
                e.type === 'click'
            ) {
                e.preventDefault();
            }
                try {
                    let temporaryObject = Object.assign({}, dataLayerSourceObjects), data;

                    if (e instanceof $.Event) {
                        let currentData = $(e.currentTarget).data('gtm'),
                            dataName = $(e.currentTarget).data('gtm-name');

                        if (dataName && currentData) {
                            temporaryObject[dataName] = currentData;
                        }
                    }

                    temporaryObject.moment = moment;
                    temporaryObject.e = e;
                    data = ejs.render(template, temporaryObject, {"async": true});
                    if (data instanceof Promise) {
                        data.then(function (result) {
                            result = toJson(result);
                            window.dataLayer.push(result);
                        });
                    } else {
                        data = toJson(data);
                        window.dataLayer.push(data);
                    }
                } catch (err) {
                    // TODO: Remove debug
                    console.error('Error during passing the dataLayerObject. Please check the template configuration.');
                    console.debug('Dom Selector: ' + selector);
                    console.debug('Dom Event: ' + domEvent);
                    console.debug('Template: ' + template);
                }

                if (e instanceof $.Event &&
                    $(e.currentTarget).attr('href') &&
                    e.type === 'click'
                ) {
                    window.location.href = $(e.currentTarget).attr('href');
                }
        };

        if (selector === document || selector === window) {
            if (domEvent === 'ready') {
                $(selector).ready(pushDataObjClosure);
            } else {
                $(selector).on(domEvent, pushDataObjClosure);
            }
        } else {
            async.async(selector, function () {
                if (!eventCreate[selector]) {
                    eventCreate[selector] = 0;
                }

                let current = $($(selector)[eventCreate[selector]]);
                // Document ready is not a normal event.
                if (domEvent === 'ready') {
                    let currentData = $(current).data('gtm'),
                        dataName = $(current).data('gtm-name');

                    if (dataName && currentData) {
                        dataLayerSourceObjects[dataName] = currentData;
                    }

                    $(document).ready(pushDataObjClosure);
                } else {
                    $(current).on(domEvent, pushDataObjClosure);
                }

                eventCreate[selector]++;
            });
        }
    };
});
