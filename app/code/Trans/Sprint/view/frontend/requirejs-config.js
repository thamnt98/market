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

var config = {
	config: {
		mixins: {
			'Magento_Checkout/js/model/place-order': {
				'Trans_Sprint/js/model/place-order-mixin': true
			},
			'Magento_Checkout/js/view/payment/list': {
				'Trans_Sprint/js/view/payment/list': true
			},
			'Magento_Checkout/js/view/payment/default': {
                'Trans_Sprint/js/view/payment/default': true
            }
		}
	}
};