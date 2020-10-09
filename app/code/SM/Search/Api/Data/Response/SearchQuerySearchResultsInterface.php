<?php

declare(strict_types=1);

namespace SM\Search\Api\Data\Response;

use Magento\Framework\Api\SearchResultsInterface;

interface SearchQuerySearchResultsInterface extends SearchResultsInterface
{

    /**
     * @return \Magento\Search\Model\QueryInterface[]
     */
    public function getItems(): array;

    /**
     * @param \Magento\Search\Model\QueryInterface[]
     * @return self
     */
    public function setItems(array $items): self;
}
