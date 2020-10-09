<?php

namespace SM\Checkout\Model\Api;

class SearchStoreResponse extends \Magento\Framework\Api\AbstractExtensibleObject implements \SM\Checkout\Api\Data\Checkout\SearchStoreResponseInterface
{
    /**
     * {@inheritdoc}
     */
    public function setStoreList($data)
    {
        return $this->setData(self::STORE_LIST, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreList()
    {
        return $this->_get(self::STORE_LIST);
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentStore($distance)
    {
        return $this->setData(self::CURRENT_STORE, $distance);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentStore()
    {
        return $this->_get(self::CURRENT_STORE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentStoreFulFill($fulFill)
    {
        return $this->setData(self::CURRENT_STORE_FULFILL, $fulFill);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentStoreFulFill()
    {
        return $this->_get(self::CURRENT_STORE_FULFILL);
    }
}
