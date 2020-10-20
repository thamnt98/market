<?php

namespace SM\Checkout\Api\Data\Checkout;

interface SearchStoreResponseInterface
{
    const STORE_LIST = 'store_list';
    const CURRENT_STORE = 'current_store';
    const CURRENT_STORE_FULFILL = 'current_store_fulfill';

    /**
     * @param \SM\Checkout\Api\Data\Checkout\SearchStoreInterface[] $data
     * @return $this
     */
    public function setStoreList($data);

    /**
     * @return \SM\Checkout\Api\Data\Checkout\SearchStoreInterface[]
     */
    public function getStoreList();

    /**
     * @param \SM\Checkout\Api\Data\Checkout\SearchStoreInterface $store
     * @return $this
     */
    public function setCurrentStore($store);

    /**
     * @return \SM\Checkout\Api\Data\Checkout\SearchStoreInterface
     */
    public function getCurrentStore();

    /**
     * @param bool $fulFill
     * @return $this
     */
    public function setCurrentStoreFulFill($fulFill);

    /**
     * @return bool
     */
    public function getCurrentStoreFulFill();
}
