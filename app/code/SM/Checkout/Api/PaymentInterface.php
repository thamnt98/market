<?php


namespace SM\Checkout\Api;

interface PaymentInterface
{

    /**
     * @param string $customerId
     * @param string $paymentMethod
     * @return string
     */
    public function getInstalmentTerm($customerId,$paymentMethod);
}
