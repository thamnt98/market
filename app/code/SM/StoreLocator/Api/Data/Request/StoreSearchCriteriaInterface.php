<?php

//declare(strict_types=1);

namespace SM\StoreLocator\Api\Data\Request;

use Magento\Framework\Api\SearchCriteriaInterface;

interface StoreSearchCriteriaInterface extends SearchCriteriaInterface
{
    /**
     * @return \SM\StoreLocator\Api\Data\Request\SortOrderInterface[]
     */
    public function getSortOrders(): array;

    /**
     * @param \SM\StoreLocator\Api\Data\Request\SortOrderInterface[] $sortOrders
     * @return self
     */
    public function setSortOrders(array $sortOrders = null): self;
}
