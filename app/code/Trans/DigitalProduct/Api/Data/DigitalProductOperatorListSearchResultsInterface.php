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

namespace Trans\DigitalProduct\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @api
 */
interface DigitalProductOperatorListSearchResultsInterface extends SearchResultsInterface {
	/**
	 * Get DigitalProduct response list.
	 *
	 * @return \Trans\DigitalProduct\Api\Data\DigitalProductResponseInterface[]
	 */
	public function getItems();

	/**
	 * Set DigitalProduct response list.
	 *
	 * @param \Trans\DigitalProduct\Api\Data\DigitalProductResponseInterface[] $items
	 * @return $this
	 */
	public function setItems(array $items);
}