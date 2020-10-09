<?php

//declare(strict_types=1);

namespace SM\StoreLocator\Model\Data\Request;

use SM\StoreLocator\Api\Data\Request\StoreSearchCriteriaInterface;
use SM\StoreLocator\Model\Data\Request\StoreSearchCriteria\SortOrder;
use Magento\Framework\Api\SearchCriteria;

class StoreSearchCriteria extends SearchCriteria implements StoreSearchCriteriaInterface
{
    /**
     * @return SortOrder[]
     * @codeCoverageIgnore
     */
    public function getSortOrders(): array
    {
        return $this->_get(self::SORT_ORDERS) === null ? [] : $this->_get(self::SORT_ORDERS);
    }

    /**
     * @param SortOrder[] $items
     * @return $this
     * @codeCoverageIgnore
     */
    public function setSortOrders(array $items = null): StoreSearchCriteriaInterface
    {
        return $this->setData(self::SORT_ORDERS, $items);
    }
}
