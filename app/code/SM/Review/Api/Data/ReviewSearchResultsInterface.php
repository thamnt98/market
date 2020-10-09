<?php
/**
 * @category Magento
 * @package SM\Review\Api\Data
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Review\Api\Data;

/**
 * Interface ReviewSearchResultsInterface
 * @package SM\Review\Api\Data
 */
interface ReviewSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
    * @return \SM\Review\Api\Data\ReviewDataInterface[]
    */
    public function getItems();

    /**
     * @param \SM\Review\Api\Data\ReviewDataInterface[] $items
     * @return \SM\Review\Api\Data\ReviewSearchResultsInterface
     */
    public function setItems(array $items);
}
