<?php

namespace SM\MobileApi\Api;

/**
 * Interface for managing product review
 * @package SM\MobileApi\Api
 */
interface ReviewInterface
{
    /**
     * Get product reviews
     *
     * @param int $product_id The product ID
     * @param int $limit
     * @param int $p
     * @return \SM\MobileApi\Api\Data\Review\ReviewsResultInterface
     */
    public function getReviews($product_id, $limit, $p);

    /**
     * Submit a review
     *
     * @return \SM\MobileApi\Api\Data\Review\SubmitReviewResultInterface
     */
    public function saveReview();
}
