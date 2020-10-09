<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Sprint\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @api
 */
interface SprintCustomerTokenizationSearchResultsInterface extends SearchResultsInterface
{
	/**
	 * Get data response list.
	 *
	 * @return \Trans\Sprint\Api\Data\SprintCustomerTokenizationInterface[]
	 */
	public function getItems();

	/**
	 * Set data response list.
	 *
	 * @param \Trans\Sprint\Api\Data\SprintCustomerTokenizationInterface[] $items
	 * @return $this
	 */
	public function setItems(array $items);
}
