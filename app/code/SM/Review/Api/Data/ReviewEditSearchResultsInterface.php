<?php


namespace SM\Review\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface ReviewEditSearchResultsInterface
 * @package SM\Review\Api\Data
 */
interface ReviewEditSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \SM\Review\Api\Data\ReviewEditInterface[]
     */
    public function getItems();

    /**
     * @param \SM\Review\Api\Data\ReviewEditInterface[] $items
     * @return \SM\Review\Api\Data\ReviewEditInterface[]
     */
    public function setItems(array $items);
}
