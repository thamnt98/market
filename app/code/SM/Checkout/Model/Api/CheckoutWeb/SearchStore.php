<?php

namespace SM\Checkout\Model\Api\CheckoutWeb;

class SearchStore extends \Magento\Framework\Api\AbstractExtensibleObject implements \SM\Checkout\Api\Data\CheckoutWeb\SearchStoreInterface
{
    const SHORTEST_STORE_LIST = 'shortest_store_list';
    const CURRENT_STORE = 'current_store';

    /**
     * {@inheritdoc}
     */
    public function setShortestStoreList($shortestStoreList)
    {
        return $this->setData(self::SHORTEST_STORE_LIST, $shortestStoreList);
    }

    /**
     * {@inheritdoc}
     */
    public function getShortestStoreList()
    {
        return $this->_get(self::SHORTEST_STORE_LIST);
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentStore($currentStore)
    {
        return $this->setData(self::CURRENT_STORE, $currentStore);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentStore()
    {
        return $this->_get(self::CURRENT_STORE);
    }
}
