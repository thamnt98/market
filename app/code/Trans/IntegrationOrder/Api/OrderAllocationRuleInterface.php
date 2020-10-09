<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.co.id>
 *
 * Copyright © 2020 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Api;

/**
 * Interface OrderAllocationRuleInterface
 */
interface OrderAllocationRuleInterface {
	/**
	 * hit oms to get oar data
	 *
	 * @param \Trans\IntegrationOrder\Api\Data\IntegrationOrderAllocationRuleInterface[] $address
	 * @return mixed[]
	 */
	public function getOrderAllocationRule($address);
}
