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
 * Class TopicSearchResultsInterface
 * @package SM\InspireMe\Api\Data
 */
interface TopicSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get Topic list.
     *
     * @return \Mirasvit\Blog\Api\Data\CategoryInterface[]
     */
    public function getItems();

    /**
     * Set Topic list.
     *
     * @param \Mirasvit\Blog\Api\Data\CategoryInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}