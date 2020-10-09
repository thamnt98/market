<?php

namespace SM\Checkout\Api\Data\CheckoutWeb;

interface EstimateShippingInterface
{
    /**
     * @param bool $reload
     * @return $this
     */
    public function setReload($reload);

    /**
     * @return bool
     */
    public function getReload();

    /**
     * @param \SM\Checkout\Api\Data\CheckoutWeb\ItemsValidMethod\ItemInterface[] $itemsValidMethod
     * @return $this
     */
    public function setItemsValidMethod($itemsValidMethod);

    /**
     * @return \SM\Checkout\Api\Data\CheckoutWeb\ItemsValidMethod\ItemInterface[]
     */
    public function getItemsValidMethod();

    /**
     * @param bool $error
     * @return $this
     */
    public function setError($error);

    /**
     * @return bool
     */
    public function getError();

    /**
     * @param bool $isSplitOrder
     * @return $this
     */
    public function setIsSplitOrder($isSplitOrder);

    /**
     * @return bool
     */
    public function getIsSplitOrder();

    /**
     * @param string $stockMessage
     * @return $this
     */
    public function setStockMessage($stockMessage);

    /**
     * @return string
     */
    public function getStockMessage();
}
