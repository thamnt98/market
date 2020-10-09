<?php
/**
 * @category Trans
 * @package  Trans_LocationCoverage
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\LocationCoverage\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Result Interface for city.
 * @api
 */
interface CitySearchResultInterface extends SearchResultsInterface
{
	/**
     * Get Items
     *
     * @return mixed
     */
    public function getItems();

    /**
     * Set Items
     *
     * @return array $items
     */
    public function setItems(array $items);
}