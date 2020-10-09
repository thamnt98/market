require(['jquery', 'SM_GTM/js/gtm/model/customer','domReady', 'jquery/jquery-storageapi'], function ($, $customerModel,domReady) {
    const customerRelatedEvents = $customerModel.customerRelatedEvents;

    for (const customerEvent in customerRelatedEvents) {
        const customerEventTriggered = 'customer:' + customerRelatedEvents[customerEvent];
        $(document).on(customerEventTriggered, (e, additionalData) => {
            // Set customer Identifier to Storage
            if (typeof(additionalData) !== 'undefined' && additionalData !== null) {
                $.localStorage.set('customer_identifier', additionalData.customer_identifier ? additionalData.customer_identifier : null);
            }
            // Flagging the custom variable to force pull customer information.
            $.localStorage.set(customerRelatedEvents[customerEvent] + '-success', true);
        });
    }
});
