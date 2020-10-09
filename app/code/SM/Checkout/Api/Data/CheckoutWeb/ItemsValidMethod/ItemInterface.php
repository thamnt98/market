<?php

namespace SM\Checkout\Api\Data\CheckoutWeb\ItemsValidMethod;

interface ItemInterface
{
    /**
     * @param int $itemId
     * @return $this
     */
    public function setItemId($itemId);

    /**
     * @return int
     */
    public function getItemId();

    /**
     * @param \SM\Checkout\Api\Data\CheckoutWeb\ItemsValidMethod\MethodInterface[] $validMethod
     * @return $this
     */
    public function setValidMethod($validMethod);

    /**
     * @return \SM\Checkout\Api\Data\CheckoutWeb\ItemsValidMethod\MethodInterface[]
     */
    public function getValidMethod();
}
