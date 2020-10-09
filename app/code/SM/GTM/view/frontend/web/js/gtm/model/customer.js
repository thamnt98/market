define([
    'jquery',
    'eJs',
    'moment',
    'SM_GTM/js/gtm/model/device',
    'gtmSha256'
], function ($, $ejs, moment, device, sha256) {
    let customer = {};
    device = device.init();
    customer.customerRelatedEvents = ['login', 'register', 'logout', 'forgot-password', 'otp'];
    return customer;
});
