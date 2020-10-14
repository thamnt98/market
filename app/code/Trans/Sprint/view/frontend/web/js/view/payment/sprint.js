/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list',
        'uiLayout',
        'uiRegistry',
        'jquery'
    ],
    function(
        Component,
        rendererList,
        layout,
        registry,
        $
    ) {
        'use strict';

        var ccfullGroupName = 'vapaymentGroup';

        layout([{
            name: ccfullGroupName,
            component: 'Trans_Sprint/js/model/payment/method-group-vapayment',
        }]);

        registry.get(ccfullGroupName, function(vapaymentGroup) {
            rendererList.push({
                type: 'sprint_bca_va',
                component: 'Trans_Sprint/js/view/payment/method-renderer/sprint-method',
                group: vapaymentGroup
            }, {
                type: 'sprint_permata_va',
                component: 'Trans_Sprint/js/view/payment/method-renderer/sprint-method',
                group: vapaymentGroup
            });
        });

        var installmentGroupName = 'installmentGroup';

        layout([{
            name: installmentGroupName,
            component: 'Trans_Sprint/js/model/payment/method-group-installment',
        }]);

        registry.get(installmentGroupName, function(installmentGroup) {
            rendererList.push({
                type: 'sprint_bca_cc',
                component: 'Trans_Sprint/js/view/payment/method-renderer/sprintinstallment-method',
                group: installmentGroup
            });
        });

        var ccfullGroupName = 'ccfullpaymentGroup';

        layout([{
            name: ccfullGroupName,
            component: 'Trans_Sprint/js/model/payment/method-group-ccfullpayment',
        }]);

        registry.get(ccfullGroupName, function(ccfullpaymentGroup) {
            rendererList.push({
                type: 'sprint_allbankfull_cc',
                component: 'Trans_Sprint/js/view/payment/method-renderer/sprint-method',
                group: ccfullpaymentGroup
            });

            rendererList.push({
                type: 'sprint_mega_cc',
                component: 'Trans_Sprint/js/view/payment/method-renderer/sprint-method',
                group: ccfullpaymentGroup
            });
        });

        rendererList.push({
            type: 'sprint_allbank_debit',
            component: 'Trans_Sprint/js/view/payment/method-renderer/sprint-method'
        });
        /** Add view logic here if needed */
        return Component.extend({});
    }
);