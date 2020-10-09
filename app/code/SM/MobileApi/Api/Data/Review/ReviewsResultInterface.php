<?php
namespace SM\MobileApi\Api\Data\Review;

/**
 * Interface for storing get reviews result
 */
interface ReviewsResultInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const OVERVIEW = 'overview';
    const REVIEWS = 'reviews';
    const REVIEW_COUNTER = 'review_counter';

    /**
     * Get review summary
     *
     * @return \SM\MobileApi\Api\Data\Review\OverviewInterface
     */
    public function getOverview();

    /**
     * @param \SM\MobileApi\Api\Data\Review\OverviewInterface $value
     * @return $this
     */
    public function setOverview($value);
    /**
     * Get reviews
     *
     * @return \SM\MobileApi\Api\Data\Review\ReviewInterface[]
     */
    public function getReviews();

    /**
     * @param \SM\MobileApi\Api\Data\Review\ReviewInterface[] $value
     * @return $this
     */
    public function setReviews($value);

    /**
     * Get reviews count
     *
     * @return int
     */
    public function getReviewCounter();

    /**
     * @param int $reviewCounter
     * @return $this
     */
    public function setReviewCounter($reviewCounter);
}
