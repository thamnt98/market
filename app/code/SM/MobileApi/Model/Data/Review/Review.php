<?php

namespace SM\MobileApi\Model\Data\Review;

/**
 * Class Review
 * @package SM\MobileApi\Model\Data\Review
 */
class Review extends \Magento\Framework\Model\AbstractExtensibleModel implements \SM\MobileApi\Api\Data\Review\ReviewInterface
{
    public function getNickname()
    {
        return $this->getData(self::NICKNAME);
    }

    public function setNickname($value)
    {
        return $this->setData(self::NICKNAME, $value);
    }

    public function getCreateAt()
    {
        return $this->getData(self::CREATE_AT);
    }

    public function setCreateAt($value)
    {
        return $this->setData(self::CREATE_AT, $value);
    }

    public function getReview()
    {
        return $this->getData(self::REVIEW);
    }

    public function setReview($value)
    {
        return $this->setData(self::REVIEW, $value);
    }

    public function getCustomerImage()
    {
        return $this->getData(self::CUSTOMER_IMAGE);
    }

    public function setCustomerImage($value)
    {
        return $this->setData(self::CUSTOMER_IMAGE, $value);
    }
}
