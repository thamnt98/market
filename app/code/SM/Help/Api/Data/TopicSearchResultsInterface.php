<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Help\Api\Data;

/**
 * Interface TopicSearchResultsInterface
 * @package SM\Help\Api\Data
 */
interface TopicSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get Topics list.
     *
     * @return \SM\Help\Api\Data\TopicInterface[]
     */
    public function getItems();

    /**
     * Set Topics list.
     *
     * @param \SM\Help\Api\Data\TopicInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
