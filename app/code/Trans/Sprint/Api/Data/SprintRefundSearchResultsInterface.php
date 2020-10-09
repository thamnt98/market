<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Sprint\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @api
 */
interface SprintRefundSearchResultsInterface extends SearchResultsInterface {
	/**
	 * Get sprint refund list.
	 *
	 * @return \Trans\Sprint\Api\Data\SprintRefundInterface[]
	 */
	public function getItems();

	/**
	 * Set sprint refund list.
	 *
	 * @param \Trans\Sprint\Api\Data\SprintRefundInterface[] $items
	 * @return $this
	 */
	public function setItems(array $items);
}
