<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Api\Data;

/**
 * Interface ArticleSearchResultsInterface
 * @package SM\InspireMe\Api\Data
 */
interface ArticleSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get Article list.
     *
     * @return \SM\InspireMe\Api\Data\PostListingInterface[]
     */
    public function getItems();

    /**
     * Set Article list.
     *
     * @param \SM\InspireMe\Api\Data\PostListingInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
