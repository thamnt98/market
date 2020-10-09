<?php

declare(strict_types=1);

namespace SM\Search\Api\Data\Response;

use Magento\Framework\Api\SearchResultsInterface;

interface SearchQueryCategorySearchResultsInterface extends SearchResultsInterface
{

    /**
     * @return \SM\Search\Api\Entity\SearchQueryCategoryInterface[]
     */
    public function getItems(): array;

    /**
     * @param \SM\Search\Api\Entity\SearchQueryCategoryInterface[] $items
     * @return self
     */
    public function setItems(array $items): self;
}
