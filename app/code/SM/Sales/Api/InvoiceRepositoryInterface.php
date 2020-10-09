<?php


namespace SM\Sales\Api;

/**
 * Interface InvoiceRepositoryInterface
 * @package SM\Sales\Api
 */
interface InvoiceRepositoryInterface
{
    /**
     * @param int $orderId
     * @return \SM\Sales\Api\Data\Invoice\InvoiceInterface
     */
    public function getById($orderId);
}
