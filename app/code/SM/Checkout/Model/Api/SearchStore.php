<?php

namespace SM\Checkout\Model\Api;

class SearchStore extends \Magento\Framework\Api\AbstractExtensibleObject implements \SM\Checkout\Api\Data\Checkout\SearchStoreInterface
{
    /**
     * {@inheritdoc}
     */
    public function setStore($data)
    {
        return $this->setData(self::STORE, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getStore()
    {
        return $this->_get(self::STORE);
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
