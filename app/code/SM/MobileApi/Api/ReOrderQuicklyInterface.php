<?php

namespace SM\MobileApi\Api;

interface ReOrderQuicklyInterface
{

    /**
     * @param $customerId
     * @return mixed
     */
    public function getOrderHistory($customerId);

    /**
     * @param $orderId
     * @return mixed
     */
    public function getOrderDetail($orderId);

    /**
     * @param $orderId
     * @return mixed
     */
    public function reOrder($orderId);

    /**
     * @param int $customerId
     * @param int $pageSize
     * @param int $currentPage
     * @return \SM\MobileApi\Api\Data\Order\ListOrdersInterface;
     */
    public function getOrdersCanReorder($customerId, $pageSize=12, $currentPage=1);
}
