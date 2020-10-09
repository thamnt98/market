<?php

declare(strict_types=1);

namespace SM\Search\Model\Data\Response;

use Magento\Framework\Api\SearchResults;
use SM\Search\Api\Data\Response\SearchQueryCategorySearchResultsInterface;
use SM\Search\Api\Entity\SearchQueryCategoryInterface;

class SearchQueryCategorySearchResults extends SearchResults implements SearchQueryCategorySearchResultsInterface
{
    /**
     * @return SearchQueryCategoryInterface[]
     * @codeCoverageIgnore
     */
    public function getItems(): array
    {
        return $this->_get(self::KEY_ITEMS) === null ? [] : $this->_get(self::KEY_ITEMS);
    }

    /**
     * @param SearchQueryCategoryInterface[] $items
     * @return self
     * @codeCoverageIgnore
     */
    public function setItems(array $items): SearchQueryCategorySearchResultsInterface
    {
        return $this->setData(self::KEY_ITEMS, $items);
    }
}
