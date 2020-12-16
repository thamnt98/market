/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        var config = window.checkoutConfig.payment.trans_mepay.providers;
        rendererList.push(
          {
            type: 'trans_mepay_cc',
            component: 'Trans_Mepay/js/view/payment/method-renderer/trans_mepay'
          }
        );

        rendererList.push(
          {
            type: 'trans_mepay_debit',
            component: 'Trans_Mepay/js/view/payment/method-renderer/trans_mepay'
          }
        );
        
        rendererList.push(
          {
            type: 'trans_mepay_va',
            component: 'Trans_Mepay/js/view/payment/method-renderer/trans_mepay'
          }
        );
        
        rendererList.push(
          {
            type: 'trans_mepay_qris',
            component: 'Trans_Mepay/js/view/payment/method-renderer/trans_mepay'
          }
        );

        /** Add view logic here if needed */
        return Component.extend({});
    }
);
