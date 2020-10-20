<?php

namespace SM\Review\Model;

use SM\Review\Api\Data\ReviewEditInterface;

/**
 * Class ReviewEdit
 * @package SM\Review\Model
 */
class ReviewEdit extends \Magento\Framework\Model\AbstractModel implements ReviewEditInterface
{


    protected function _construct()
    {
        $this->_init("SM\Review\Model\ResourceModel\ReviewEdit");
    }

    /**
     * @inheritDoc
     */
    public function setReviewId($value)
    {
        return $this->setData(self::REVIEW_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($value)
    {
        return $this->setData(self::CREATED_AT, $value);
    }

    /**
     * @inheritDoc
     */
    public function setTitle($value)
    {
        return $this->setData(self::TITLE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setDetail($value)
    {
        return $this->setData(self::DETAIL, $value);
    }

    /**
     * @inheritDoc
     */
    public function setVoteValue($value)
    {
        return $this->setData(self::VOTE_VALUE, $value);
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
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * @inheritDoc
     */
    public function getDetail()
    {
        return $this->getData(self::DETAIL);
    }

    /**
     * @inheritDoc
     */
    public function getVoteValue()
    {
        return $this->getData(self::VOTE_VALUE);
    }

    /**
     * @inheritDoc
     */
    public function setImageChanged($value)
    {
        return $this->setData(self::IMAGE_CHANGED, $value);
    }

    /**
     * @inheritDoc
     */
    public function getImageChanged()
    {
        return $this->getData(self::IMAGE_CHANGED);
    }

    /**
     * @inheritDoc
     */
    public function getImages()
    {
        return $this->getData(self::IMAGES);
    }

    /**
     * @inheritDoc
     */
    public function setImages($value)
    {
        return $this->setData(self::IMAGES, $value);
    }
}
