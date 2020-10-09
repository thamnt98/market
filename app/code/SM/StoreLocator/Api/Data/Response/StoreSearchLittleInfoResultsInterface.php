<?php

//declare(strict_types=1);

namespace SM\StoreLocator\Api\Data\Response;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @api
 */
interface StoreSearchLittleInfoResultsInterface extends SearchResultsInterface
{
    /**
     * @return \SM\StoreLocator\Api\Data\StoreLittleInfoInterface[]
     */
    public function getItems(): array;

    /**
     * @param \SM\StoreLocator\Api\Data\StoreLittleInfoInterface[] $items
     * @return self
     */
    public function setItems(array $items): self;
}
