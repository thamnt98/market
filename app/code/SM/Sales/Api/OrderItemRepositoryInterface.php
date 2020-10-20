<?php
/**
 * @category Magento
 * @package SM\Sales\Api
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Sales\Api;

/**
 * Interface OrderItemRepositoryInterface
 * @package SM\Sales\Api
 */
interface OrderItemRepositoryInterface
{
    /**
     * @param int $cartId
     * @param int $itemId
     * @return \SM\Sales\Api\Data\ResultDataInterface
     */
    public function reorder($cartId, $itemId);

    /**
     * @param int $cartId
     * @param int $parentOrderId
     * @return \SM\Sales\Api\Data\ResultDataInterface
     */
    public function reorderAll($cartId, $parentOrderId);
}
