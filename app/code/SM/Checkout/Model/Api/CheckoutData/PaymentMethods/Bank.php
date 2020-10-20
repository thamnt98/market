<?php


namespace SM\Checkout\Model\Api\CheckoutData\PaymentMethods;


use Magento\Framework\Api\AbstractSimpleObject;
use SM\Checkout\Api\Data\Checkout\PaymentMethods\BankInterface;

class Bank extends \Magento\Framework\Model\AbstractExtensibleModel implements BankInterface
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

    public function getCode()
    {
        return $this->getData('code');
    }

    public function setCode($data)
    {
        return $this->setData('code',$data);
    }

    public function getTerms()
    {
        return $this->getData('terms');
    }

    public function setTerms($data)
    {
        return $this->setData('terms', $data);
    }

    public function getLogo()
    {
        return $this->getData('logo');
    }

    public function setLogo($data)
    {
        return $this->setData('logo', $data);
    }

    public function getContent()
    {
        return $this->getData('content');
    }

    public function setContent($data)
    {
        return $this->setData('content', $data);
    }

    public function getMinimumAmount()
    {
        return $this->getData('minimum_amount');
    }

    public function setMinimumAmount($data)
    {
        return $this->setData('minimum_amount', $data);
    }

    /**
     * @inheritDoc
     */
    public function getContentObjects()
    {
        return $this->getData('content_objects');
    }

    /**
     * @inheritDoc
     */
    public function setContentObjects($value)
    {
        return $this->setData('content_objects', $value);
    }
}
