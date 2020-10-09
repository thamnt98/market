<?php

//declare(strict_types=1);

namespace SM\StoreLocator\Api\Data\Response;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @api
 */
interface StoreSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \SM\StoreLocator\Api\Data\StoreInterface[]
     */
    public function getItems(): array;

    /**
     * @param \SM\StoreLocator\Api\Data\StoreInterface[] $items
     * @return self
     */
    public function setItems(array $items): self;
}
