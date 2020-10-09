<?php

declare(strict_types=1);

namespace SM\Search\Model\Data\Response;

use Magento\Framework\Api\SearchResults;
use SM\Search\Api\Data\Response\SearchQueryPersonalSearchResultsInterface;
use SM\Search\Api\Entity\SearchQueryPersonalInterface;

class SearchQueryPersonalSearchResults extends SearchResults implements SearchQueryPersonalSearchResultsInterface
{
    /**
     * @return SearchQueryPersonalInterface[]
     * @codeCoverageIgnore
     */
    public function getItems(): array
    {
        return $this->_get(self::KEY_ITEMS) === null ? [] : $this->_get(self::KEY_ITEMS);
    }

    /**
     * @param SearchQueryPersonalInterface[] $items
     * @return self
     * @codeCoverageIgnore
     */
    public function setItems(array $items): SearchQueryPersonalSearchResultsInterface
    {
        return $this->setData(self::KEY_ITEMS, $items);
    }
}
