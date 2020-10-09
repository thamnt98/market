<?php


namespace SM\MobileApi\Api\Data\Catalog\Product\Review;


interface OverviewInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const PERCENT = 'percent';
    const REVIEW_COUNTER = 'review_counter';

    /**
     * Get percent number
     *
     * @return int
     */
    public function getPercent();

    /**
     * @param int $value
     * @return $this
     */
    public function setPercent($value);

    /**
     * Get number of reviews
     *
     * @return int
     */
    public function getReviewCounter();

    /**
     * @param int $value
     * @return $this
     */
    public function setReviewCounter($value);
}
