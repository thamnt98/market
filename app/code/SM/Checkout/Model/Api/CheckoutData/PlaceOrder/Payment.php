<?php

namespace SM\Checkout\Model\Api\CheckoutData\PlaceOrder;

use SM\Checkout\Api\Data\Checkout\PlaceOrder\PaymentInterface;

class Payment extends \Magento\Framework\Model\AbstractExtensibleModel implements PaymentInterface
{

    const HOW_TO_PAY_OBJECTS = 'how_to_pay_object';

    public function getPaymentMethod()
    {
        return $this->getData('payment_method');
    }

    public function setPaymentMethod($data)
    {
        return $this->setData('payment_method', $data);
    }

    public function getStatus()
    {
        return $this->getData('status');
    }

    public function setStatus($data)
    {
        return $this->setData('status', $data);
    }

    public function getMessage()
    {
        return $this->getData('message');
    }

    public function setMessage($data)
    {
        return $this->setData('message', $data);
    }

    public function getRedirectUrl()
    {
        return $this->getData('redirectUrl');
    }

    public function setRedirectUrl($data)
    {
        return $this->setData('redirectUrl', $data);
    }

    public function getAccountNumber()
    {
        return $this->getData('account_number');
    }

    public function setAccountNumber($data)
    {
        return $this->setData('account_number', $data);
    }

    public function getExpiredTime()
    {
        return $this->getData('expired_time');
    }

    public function setExpiredTime($data)
    {
        return $this->setData('expired_time', $data);
    }

    public function getHowToPay()
    {
        return $this->getData('how_to_pay');
    }

    public function setHowToPay($data)
    {
        return $this->setData('how_to_pay', $data);
    }

    /**
     * @inheritDoc
     */
    public function getHowToPayObjects()
    {
        return $this->getData(self::HOW_TO_PAY_OBJECTS);
    }

    /**
     * @inheritDoc
     */
    public function setHowToPayObjects($value)
    {
        return $this->setData(self::HOW_TO_PAY_OBJECTS, $value);
    }

    public function getTotalAmount()
    {
        return $this->getData('total_amount');
    }

    public function setTotalAmount($data)
    {
        return $this->setData('total_amount', $data);
    }

    public function getReferenceNumber()
    {
        return $this->getData('reference_number');
    }

    public function setReferenceNumber($data)
    {
        return $this->setData('reference_number', $data);
    }

    public function getRelateUrl()
    {
        return $this->getData('relate_url');
    }

    public function setRelateUrl($data)
    {
        return $this->setData('relate_url', $data);
    }

    public function getBank()
    {
        return $this->getData('bank');
    }

    public function setBank($data)
    {
        return $this->setData('bank', $data);
    }
}
