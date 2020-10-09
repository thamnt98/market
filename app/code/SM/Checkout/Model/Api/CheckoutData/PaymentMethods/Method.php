<?php


namespace SM\Checkout\Model\Api\CheckoutData\PaymentMethods;


use Magento\Framework\Api\AbstractSimpleObject;
use SM\Checkout\Api\Data\Checkout\PaymentMethods\MethodInterface;

class Method extends \Magento\Framework\Model\AbstractExtensibleModel implements MethodInterface
{

    public function getTitle()
    {
        return $this->getData('title');
    }

    public function setTitle($data)
    {
        return $this->setData('title', $data);
    }

    public function getDescription()
    {
        return $this->getData('description');
    }

    public function setDescription($data)
    {
        return $this->setData('description', $data);
    }

    public function getBanks()
    {
        return $this->getData('banks');
    }

    public function setBanks($data)
    {
        return $this->setData('banks', $data);
    }
    public function getType()
    {
        return $this->getData('type');
    }

    public function setType($data)
    {
        return $this->setData('type', $data);
    }

    public function getMinimumAmount()
    {
        return $this->getData('minimum_amount');
    }

    public function setMinimumAmount($data)
    {
        return $this->setData('minimum_amount', $data);
    }
}
