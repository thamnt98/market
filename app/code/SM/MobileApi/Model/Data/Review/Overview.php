<?php

namespace SM\MobileApi\Model\Data\Review;

/**
 * Class Overview
 * @package SM\MobileApi\Model\Data\Review
 */
class Overview extends \Magento\Framework\Model\AbstractExtensibleModel implements \SM\MobileApi\Api\Data\Review\OverviewInterface
{
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    public function setTitle($value)
    {
        return $this->setData(self::TITLE, $value);
    }

    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    public function setCode($value)
    {
        return $this->setData(self::CODE, $value);
    }

    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    public function setType($value)
    {
        return $this->setData(self::TYPE, $value);
    }

    public function getRatingSummaryPercent()
    {
        return $this->getData(self::RATING_SUMMARY_PERCENT);
    }

    public function setRatingSummaryPercent($rating)
    {
        return $this->setData(self::RATING_SUMMARY_PERCENT, $rating);
    }

    public function getRatingSummaryAmount()
    {
        return $this->getData(self::RATING_SUMMARY_AMOUNT);
    }

    public function setRatingSummaryAmount($rating)
    {
        return $this->setData(self::RATING_SUMMARY_AMOUNT, $rating);
    }

    public function getProductName()
    {
        return $this->getData(self::PRODUCT_NAME);
    }

    public function setProductName($name)
    {
        return $this->setData(self::PRODUCT_NAME, $name);
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
