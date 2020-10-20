<?php

namespace SM\Checkout\Api;

interface OrderInterface
{
    /**
     * @param int $customerId
     * @param int $orderId
     * @return mixed
     */
    public function getStatus($customerId,$orderId);
}
