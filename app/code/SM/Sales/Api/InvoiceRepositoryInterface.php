<?php


namespace SM\Sales\Api;

/**
 * Interface InvoiceRepositoryInterface
 * @package SM\Sales\Api
 */
interface InvoiceRepositoryInterface
{
    /**
     * @param int $customerId
     * @param int $orderId
     * @return string
     */
    public function getById(int $customerId, int $orderId);
}
