<?php


namespace SM\Review\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface ReviewedSearchResultsInterface
 * @package SM\Review\Api\Data
 */
interface ReviewedSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \SM\Review\Api\Data\ReviewedInterface[]
     */
    public function getItems();

    /**
     * @param \SM\Review\Api\Data\ReviewedInterface[] $items
     * @return \SM\Review\Api\Data\ReviewedInterface[]
     */
    public function setItems(array $items);
}
