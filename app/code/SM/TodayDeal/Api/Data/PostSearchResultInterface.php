<?php

/**
 * @category SM
 * @package SM_TodayDeal
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Chinhvd <chinhvd@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\TodayDeal\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface PostSearchResultInterface extends SearchResultsInterface
{
    /**
     * Get post list.
     *
     * @return \SM\TodayDeal\Api\Data\CampaignListingMobileInterface[]
     */
    public function getItems();

    /**
     * Set post list.
     *
     * @param \SM\TodayDeal\Api\Data\CampaignListingMobileInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
