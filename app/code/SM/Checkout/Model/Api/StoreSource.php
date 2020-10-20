<?php

namespace SM\Checkout\Model\Api;

class StoreSource extends \Magento\Framework\Api\AbstractExtensibleObject implements \SM\Checkout\Api\Data\Checkout\StorePickUp\SourceInterface
{
    const CODE = 'code';
    const NAME = 'name';
    const ADDRESS = 'address';
    const PHONE = 'phone';
    const DISTANCE = 'distance';

    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        return $this->setData(self::CODE, $code);
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->_get(self::CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setAddress($address)
    {
        return $this->setData(self::ADDRESS, $address);
    }

    /**
     * {@inheritdoc}
     */
    public function getAddress()
    {
        return $this->_get(self::ADDRESS);
    }

    /**
     * {@inheritdoc}
     */
    public function setPhone($phone)
    {
        return $this->setData(self::PHONE, $phone);
    }

    /**
     * {@inheritdoc}
     */
    public function getPhone()
    {
        return $this->_get(self::PHONE);
    }

    /**
     * {@inheritdoc}
     */
    public function setDistance($distance)
    {
        return $this->setData(self::DISTANCE, $distance);
    }

    /**
     * {@inheritdoc}
     */
    public function getDistance()
    {
        return $this->_get(self::DISTANCE);
    }
}
