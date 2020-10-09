<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Sprint\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @api
 */
interface BankSearchResultsInterface extends SearchResultsInterface {
	/**
	 * Get sprint response list.
	 *
	 * @return \Trans\Sprint\Api\Data\BankInterface[]
	 */
	public function getItems();

	/**
	 * Set sprint response list.
	 *
	 * @param \Trans\Sprint\Api\Data\BankInterface[] $items
	 * @return $this
	 */
	public function setItems(array $items);
}
