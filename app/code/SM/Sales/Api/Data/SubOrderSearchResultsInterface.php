<?php
/**
 * @category Magento
 * @package SM\Sales\Api\Data
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Sales\Api\Data;

/**
 * Interface ParentOrderSearchResultsInterface
 * @package SM\Sales\Api\Data
 */
interface SubOrderSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * @return \SM\Sales\Api\Data\SubOrderDataInterface[]
     */
    public function getItems();

    /**
     * @param \SM\Sales\Api\Data\SubOrderDataInterface[] $items
     * @return \SM\Sales\Api\Data\SubOrderSearchResultsInterface
     */
    public function setItems(array $items);
}
