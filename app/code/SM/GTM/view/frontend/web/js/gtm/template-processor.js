require([
        'jquery',
        'SM_GTM/js/gtm/model/events',
        'SM_GTM/js/gtm/action/refresh-data',
        'SM_GTM/js/gtm/action/customer-cache',
        'domReady!'
    ],
    function ($, eventProcess, refreshData) {
        refreshData.refresh().success(function () {
            for (let gtmEvent in dataLayerTemplates) {
                let templateDeclaration = dataLayerTemplates[gtmEvent];
                // Bind events to DOM elements
                let eventTrigger = {};
                let template = '';

                try {
                    eventTrigger = JSON.parse(templateDeclaration.event_trigger);
                    switch (eventTrigger.selector) {
                        case 'window':
                            eventTrigger.selector = window;
                            break;
                        case 'document':
                            eventTrigger.selector = document;
                            break;
                    }
                } catch (e) {
                    console.error('Error during parse eventTrigger. Please check the format in configuration');
                    console.debug(templateDeclaration.event_trigger);
                    continue;
                }

                template = templateDeclaration.template;
                eventProcess(eventTrigger.selector, eventTrigger.event, template);
            }
        });
    });
