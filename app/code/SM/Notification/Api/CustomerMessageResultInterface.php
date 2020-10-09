<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: September, 14 2020
 * Time: 4:15 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Api;

interface CustomerMessageResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * @return \SM\Notification\Api\Data\CustomerMessageInterface[]
     */
    public function getItems();

    /**
     * @param \SM\Notification\Api\Data\CustomerMessageInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items);
}
