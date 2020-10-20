<?php


namespace SM\Review\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface ReviewImageSearchResultsInterface
 * @package SM\Review\Api\Data
 */
interface ReviewImageSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \SM\Review\Api\Data\ReviewImageInterface[]
     */
    public function getItems();

    /**
     * @param \SM\Review\Api\Data\ReviewImageInterface[] $items
     * @return \SM\Review\Api\Data\ReviewImageInterface[]
     */
    public function setItems(array $items);
}
