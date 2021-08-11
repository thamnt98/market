<?php
/**
 * @category Trans
 * @package  Trans_DigitalProduct
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\DigitalProduct\Api;

/**
 * @api
 */
interface DigitalProductGetOperatorInterface {

	/**
	 * Operator PDAM
	 *
	 * @param  int $productId
	 * @return mixed[]
	 */
	public function pdam($productId);
}