<?php

namespace SM\Checkout\Model\Api\CheckoutData\Voucher;

use Magento\Framework\Api\AbstractSimpleObject;

class Voucher extends AbstractSimpleObject implements \SM\Checkout\Api\Data\Checkout\Voucher\VoucherInterface
{
    /**
     * {@inheritdoc}
     */
    public function setVoucher($voucher)
    {
        return $this->setData(self::VOUCHER, $voucher);
    }

    /**
     * @inheritDoc
     */
    public function getVoucher()
    {
        return $this->_get(self::VOUCHER);
    }

    /**
     * {@inheritdoc}
     */
    public function setApply($apply)
    {
        return $this->setData(self::APPLY, $apply);
    }

    /**
     * @inheritDoc
     */
    public function getApply()
    {
        return $this->_get(self::APPLY);
    }

    /**
     * {@inheritdoc}
     */
    public function setAmount($amount)
    {
        return $this->setData(self::AMOUNT, $amount);
    }

    /**
     * @inheritDoc
     */
    public function getAmount()
    {
        return $this->_get(self::AMOUNT);
    }

    public function getBasketId()
    {
        return $this->_get(self::BASKET_ID);
    }

    public function setBasketId($value)
    {
        return $this->setData(self::BASKET_ID, $value);
    }

    public function getBasketValue()
    {
        return $this->_get(self::BASKET_VALUE);
    }

    public function setBasketValue($value)
    {
        return $this->setData(self::BASKET_VALUE, $value);
    }

    public function getBasketQuantity()
    {
        return $this->_get(self::BASKET_QUANTITY);
    }

    public function setBasketQuantity($value)
    {
        return $this->setData(self::BASKET_QUANTITY, $value);
    }

    public function getVoucherName()
    {
        return $this->_get(self::VOUCHER_NAME);
    }

    public function setVoucherName($value)
    {
        return $this->setData(self::VOUCHER_NAME, $value);
    }

    public function getVoucherId()
    {
        return $this->_get(self::VOUCHER_ID);
    }

    public function setVoucherId($value)
    {
        return $this->setData(self::VOUCHER_ID, $value);
    }

    public function getVoucherDescription()
    {
        return $this->_get(self::VOUCHER_DESCRIPTION);
    }

    public function setVoucherDescription($value)
    {
        return $this->setData(self::VOUCHER_DESCRIPTION, $value);
    }

    public function getVoucherValidation()
    {
        return $this->_get(self::VOUCHER_VALIDATION);
    }

    public function setVoucherValidation($value)
    {
        return $this->setData(self::VOUCHER_VALIDATION, $value);
    }

    public function getVoucherStatus()
    {
        return $this->_get(self::VOUCHER_STATUS);
    }

    public function setVoucherStatus($value)
    {
        return $this->setData(self::VOUCHER_STATUS, $value);
    }
}
