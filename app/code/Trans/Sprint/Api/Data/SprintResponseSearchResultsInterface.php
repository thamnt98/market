<?php
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

namespace Trans\Sprint\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @api
 */
interface SprintResponseSearchResultsInterface extends SearchResultsInterface {
	/**
	 * Get sprint response list.
	 *
	 * @return \Trans\Sprint\Api\Data\SprintResponseInterface[]
	 */
	public function getItems();

	/**
	 * Set sprint response list.
	 *
	 * @param \Trans\Sprint\Api\Data\SprintResponseInterface[] $items
	 * @return $this
	 */
	public function setItems(array $items);
}
