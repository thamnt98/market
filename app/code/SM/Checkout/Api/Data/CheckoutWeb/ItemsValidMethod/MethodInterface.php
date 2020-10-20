<?php

namespace SM\Checkout\Api\Data\CheckoutWeb\ItemsValidMethod;

interface MethodInterface
{
    /**
     * @param string $methodCode
     * @return $this
     */
    public function setMethodCode($methodCode);

    /**
     * @return string
     */
    public function getMethodCode();
}
