<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Customer
 *
 * Date: June, 08 2020
 * Time: 2:33 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Customer\Api\Data;

interface CustomerDeviceSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get Devices.
     *
     * @return \SM\Customer\Api\Data\CustomerDeviceInterface[]
     */
    public function getItems();

    /**
     * Set Devices.
     *
     * @param \SM\Customer\Api\Data\CustomerDeviceInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items);
}
