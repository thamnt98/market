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

namespace Trans\MasterPayment\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @api
 */
interface MasterPaymentSearchResultsInterface extends SearchResultsInterface {
	/**
	 * Get Master Payment refund list.
	 *
	 * @return \Trans\MasterPayment\Api\Data\MasterPaymentInterface[]
	 */
	public function getItems();

	/**
	 * Set Master Payment refund list.
	 *
	 * @param \Trans\MasterPayment\Api\Data\MasterPaymentInterface[] $items
	 * @return $this
	 */
	public function setItems(array $items);
}
