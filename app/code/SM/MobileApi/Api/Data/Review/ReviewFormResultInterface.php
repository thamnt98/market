<?php
namespace SM\MobileApi\Api\Data\Review;

/**
 * Interface for storing review form fields
 */
interface ReviewFormResultInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ALLOW_GUEST_REVIEW = 'allow_guest_review';
    const REVIEWS = 'reviews';

    /**
     * Get allow guest can submit review
     *
     * @return bool
     */
    public function getAllowGuestReview();

    /**
     * @param bool $value
     * @return $this
     */
    public function setAllowGuestReview($value);

    /**
     * Get form fields
     *
     * @return \SM\MobileApi\Api\Data\Review\RatingInterface[]
     */
    public function getReviews();

    /**
     * @param \SM\MobileApi\Api\Data\Review\RatingInterface[] $value
     * @return $this
     */
    public function setReviews($value);
}
