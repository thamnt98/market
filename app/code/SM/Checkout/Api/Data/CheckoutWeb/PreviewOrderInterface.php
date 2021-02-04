<?php

namespace SM\Checkout\Api\Data\CheckoutWeb;

interface PreviewOrderInterface
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
     * @param \SM\Checkout\Api\Data\Checkout\Estimate\PreviewOrderInterface[] $order
     * @return $this
     */
    public function setOrder($order);

    /**
     * @return \SM\Checkout\Api\Data\Checkout\Estimate\PreviewOrderInterface[]
     */
    public function getOrder();

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
     * @param \SM\Checkout\Api\Data\CheckoutWeb\PaymentMethodInterface[] $paymentMethod
     * @return $this
     */
    public function setPaymentMethod($paymentMethod);

    /**
     * @return \SM\Checkout\Api\Data\CheckoutWeb\PaymentMethodInterface[]
     */
    public function getPaymentMethod();
}
