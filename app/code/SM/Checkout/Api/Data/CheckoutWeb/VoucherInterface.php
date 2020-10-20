<?php

namespace SM\Checkout\Api\Data\CheckoutWeb;

interface VoucherInterface
{
    /**
     * @param string $code
     * @return $this
     */
    public function setCode($code);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param float $amount
     * @return $this
     */
    public function setAmount($amount);

    /**
     * @return float
     */
    public function getAmount();
}
