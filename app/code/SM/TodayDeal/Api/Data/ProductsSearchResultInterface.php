<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\TodayDeal\Api\Data;

/**
 * Interface ProductsSearchResultInterface
 * @package SM\TodayDeal\Api\Data
 */
interface ProductsSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get products list.
     *
     * @return \SM\MobileApi\Api\Data\Product\ListItemInterface[]
     */
    public function getItems();

    /**
     * Set products list.
     *
     * @param \SM\MobileApi\Api\Data\Product\ListItemInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
