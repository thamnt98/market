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
 * Interface ParentOrderRepositoryInterface
 * @package SM\Sales\Api
 */
interface ParentOrderRepositoryInterface
{
    const STATUS_COMPLETE = "complete";
    const STATUS_CANCELED = "canceled";
    const STATUS_ORDER_CANCELED = "order_canceled";
    const STATUS_PENDING = "pending";
    const STATUS_PENDING_PAYMENT = "pending_payment";
    const STATUS_PROCESSING = "processing";
    const STATUS_IN_PROCESS = "in_process";
    const STATUS_IN_DELIVERY = "in_delivery";
    const STATUS_FAILED_DELIVERY = "failed_delivery";
    const STATUS_DELIVERED = "delivered";
    const IN_PROCESS_WAITING_FOR_PICKUP = "in_process_waiting_for_pickup";
    const PICK_UP_BY_CUSTOMER = "pick_up_by_customer";

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param int $customerId
     * @return \SM\Sales\Api\Data\ParentOrderSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria, $customerId);

    /**
     * @param int $orderId
     * @param int $customerId
     * @return \SM\Sales\Api\Data\ParentOrderDataInterface
     */
    public function getById($customerId, $orderId);

    /**
     * @return \SM\Sales\Api\Data\ReorderQuickly\OrderDataInterface[]
     */
    public function getListReorderQuickly();
}
