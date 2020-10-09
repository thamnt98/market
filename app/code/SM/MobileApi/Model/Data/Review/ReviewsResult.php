<?php

namespace SM\MobileApi\Model\Data\Review;

/**
 * Class ReviewsResult
 * @package SM\MobileApi\Model\Data\Review
 */
class ReviewsResult extends \Magento\Framework\Model\AbstractExtensibleModel implements \SM\MobileApi\Api\Data\Review\ReviewsResultInterface
{
    public function getOverview()
    {
        return $this->getData(self::OVERVIEW);
    }

    public function setOverview($value)
    {
        return $this->setData(self::OVERVIEW, $value);
    }

    public function getReviews()
    {
        return $this->getData(self::REVIEWS);
    }

    public function setReviews($value)
    {
        return $this->setData(self::REVIEWS, $value);
    }

    public function getReviewCounter()
    {
        return $this->getData(self::REVIEW_COUNTER);
    }

    public function setReviewCounter($reviewCounter)
    {
        return $this->setData(self::REVIEW_COUNTER, $reviewCounter);
    }
}
