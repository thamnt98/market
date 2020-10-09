<?php

namespace SM\MobileApi\Model\Data\Review;

/**
 * Class ReviewFormResult
 * @package SM\MobileApi\Model\Data\Review
 */
class ReviewFormResult extends \Magento\Framework\Model\AbstractExtensibleModel implements \SM\MobileApi\Api\Data\Review\ReviewFormResultInterface
{
    public function getAllowGuestReview()
    {
        return $this->getData(self::ALLOW_GUEST_REVIEW);
    }

    public function setAllowGuestReview($value)
    {
        return $this->setData(self::ALLOW_GUEST_REVIEW, $value);
    }

    public function getReviews()
    {
        return $this->getData(self::REVIEWS);
    }

    public function setReviews($value)
    {
        return $this->setData(self::REVIEWS, $value);
    }
}
