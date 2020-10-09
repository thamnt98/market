<?php

declare(strict_types=1);

namespace SM\Search\Api\Data\Response;

use Magento\Framework\Api\SearchResultsInterface;

interface SearchQueryPersonalSearchResultsInterface extends SearchResultsInterface
{

    /**
     * @return \SM\Search\Api\Entity\SearchQueryPersonalInterface[]
     */
    public function getItems(): array;

    /**
     * @param \SM\Search\Api\Entity\SearchQueryPersonalInterface[] $items
     * @return self
     */
    public function setItems(array $items): self;
}
