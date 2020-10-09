<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.co.id>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Api;

/**
 * @api
 * Interface PaymentStatusInterface
 */
interface PaymentStatusInterface {

	/**
	 * Send Status Payment to OMS
	 *
	 * @param string $refNumber
	 * @param int $paymentStatus
	 * @return string
	 */
	public function sendStatusPayment($refNumber, $paymentStatus);
}
