<?php


namespace SM\Review\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface ToBeReviewedSearchResultsInterface
 * @package SM\Review\Api\Data
 */
interface ToBeReviewedSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \SM\Review\Api\Data\ToBeReviewedInterface[]
     */
    public function getItems();

    /**
     * @param \SM\Review\Api\Data\ToBeReviewedInterface[] $items
     * @return \SM\Review\Api\Data\ToBeReviewedInterface[]
     */
    public function setItems(array $items);
}
