<?php

namespace SM\Checkout\Api\Data\CheckoutWeb;

interface SearchStoreInterface
{
    /**
     * @param \SM\Checkout\Api\Data\CheckoutWeb\SourceStoreInterface[] $shortestStoreList
     * @return $this
     */
    public function setShortestStoreList($shortestStoreList);

    /**
     * @return \SM\Checkout\Api\Data\CheckoutWeb\SourceStoreInterface[]
     */
    public function getShortestStoreList();

    /**
     * @param \SM\Checkout\Api\Data\CheckoutWeb\SourceStoreInterface $currentStore
     * @return $this
     */
    public function setCurrentStore($currentStore);

    /**
     * @return \SM\Checkout\Api\Data\CheckoutWeb\SourceStoreInterface
     */
    public function getCurrentStore();
}
