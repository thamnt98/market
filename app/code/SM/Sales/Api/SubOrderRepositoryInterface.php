<?php

namespace SM\Sales\Api;

/**
 * Interface SubOrderRepositoryInterface
 * @package SM\Sales\Api
 */
interface SubOrderRepositoryInterface
{
    /**
     * @param int $subOrderId
     * @return \SM\Sales\Api\Data\ParentOrderDataInterface
     */
    public function getById($subOrderId);

    /**
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @param int $customerId
     * @return \SM\Sales\Api\Data\SubOrderSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteria $searchCriteria, $customerId);

    /**
     * @param int $subOrderId
     * @return bool
     */
    public function setReceivedById($subOrderId);

    /**
     * @return string
     */
    public function getStatusLabel();

    /**
     * Function for testing
     *
     * @param int $orderId
     * @return bool
     */
    public function resetStatus($orderId);
}
