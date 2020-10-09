<?php
/**
 * @category Trans
 * @package  Trans_MasterPayment
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\MasterPayment\Api;

/**
 * @api
 */
interface PaymentInterface {

	/**
	 * Get Payment Data
	 *
	 * @param string $incrementId
	 * @return array
	 */
	public function getMasterPaymentData($incrementId);
}