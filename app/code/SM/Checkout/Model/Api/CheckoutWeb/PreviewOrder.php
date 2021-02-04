<?php

namespace SM\Checkout\Model\Api\CheckoutWeb;

class PreviewOrder extends \Magento\Framework\Api\AbstractExtensibleObject implements \SM\Checkout\Api\Data\CheckoutWeb\PreviewOrderInterface
{
    const RELOAD = 'reload';
    const ORDER = 'order';
    const IS_SPLIT_ORDER = 'is_split_order';
    const PAYMENT_METHOD = 'payment_method';

    /**
     * {@inheritdoc}
     */
    public function setReload($reload)
    {
        return $this->setData(self::RELOAD, $reload);
    }

    /**
     * {@inheritdoc}
     */
    public function getReload()
    {
        return $this->_get(self::RELOAD);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrder($order)
    {
        return $this->setData(self::ORDER, $order);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return $this->_get(self::ORDER);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsSplitOrder($isSplitOrder)
    {
        return $this->setData(self::IS_SPLIT_ORDER, $isSplitOrder);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsSplitOrder()
    {
        return $this->_get(self::IS_SPLIT_ORDER);
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentMethod($paymentMethod)
    {
        return $this->setData(self::PAYMENT_METHOD, $paymentMethod);
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentMethod()
    {
        return $this->_get(self::PAYMENT_METHOD);
    }
}
