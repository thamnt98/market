<?php

//declare(strict_types=1);

namespace SM\StoreLocator\Model\Data\Response;

use SM\StoreLocator\Api\Data\Response\StoreSearchResultsInterface;
use SM\StoreLocator\Api\Data\StoreInterface;
use Magento\Framework\Api\SearchResults;

class StoreSearchResults extends SearchResults implements StoreSearchResultsInterface
{
    /**
     * @return StoreInterface[]
     * @codeCoverageIgnore
     */
    public function getItems(): array
    {
        return $this->_get(self::KEY_ITEMS) === null ? [] : $this->_get(self::KEY_ITEMS);
    }

    /**
     * @param StoreInterface[] $items
     * @return self
     * @codeCoverageIgnore
     */
    public function setItems(array $items): StoreSearchResultsInterface
    {
        return $this->setData(self::KEY_ITEMS, $items);
    }
}
