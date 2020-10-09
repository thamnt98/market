<?php

namespace SM\Review\Model;

use SM\Review\Api\Data\ReviewImageInterface;

/**
 * Class ReviewImage
 * @package SM\Review\Model
 */
class ReviewImage extends \Magento\Framework\Model\AbstractModel implements ReviewImageInterface
{
    protected function _construct()
    {
        $this->_init("SM\Review\Model\ResourceModel\ReviewImage");
    }

    /**
     * @inheritDoc
     */
    public function getReviewId()
    {
        return $this->getData(self::REVIEW_ID);
    }

    /**
     * @inheritDoc
     */
    public function getImage()
    {
        return $this->getData(self::IMAGE);
    }

    /**
     * @inheritDoc
     */
    public function setReviewId($reviewId)
    {
        return $this->setData(self::REVIEW_ID, $reviewId);
    }

    /**
     * @inheritDoc
     */
    public function setImage($image)
    {
        return $this->setData(self::IMAGE, $image);
    }

    /**
     * @inheritDoc
     */
    public function getIsEdit()
    {
        return $this->getData(self::IS_EDIT);
    }

    /**
     * @inheritDoc
     */
    public function setIsEdit($value)
    {
        return $this->setData(self::IS_EDIT, $value);
    }
}
