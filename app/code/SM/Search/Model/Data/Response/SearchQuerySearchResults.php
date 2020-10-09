<?php

declare(strict_types=1);

namespace SM\Search\Model\Data\Response;

use Magento\Framework\Api\SearchResults;
use Magento\Search\Model\QueryInterface;
use SM\Search\Api\Data\Response\SearchQuerySearchResultsInterface;

class SearchQuerySearchResults extends SearchResults implements SearchQuerySearchResultsInterface
{
    /**
     * @return QueryInterface[]
     * @codeCoverageIgnore
     */
    public function getItems(): array
    {
        return $this->_get(self::KEY_ITEMS) === null ? [] : $this->_get(self::KEY_ITEMS);
    }

    /**
     * @param QueryInterface[] $items
     * @return self
     * @codeCoverageIgnore
     */
    public function setItems(array $items): SearchQuerySearchResultsInterface
    {
        return $this->setData(self::KEY_ITEMS, $items);
    }
}
