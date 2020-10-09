<?php


namespace SM\Sales\Api;

interface DigitalOrderRepositoryInterface
{
    /**
     * @param int $orderId
     * @return \SM\Sales\Api\Data\DigitalOrderDataInterface
     */
    public function getById($orderId);
}
