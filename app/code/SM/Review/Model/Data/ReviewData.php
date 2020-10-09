<?php
/**
 * @category Magento
 * @package SM\Review\Model\Data
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Review\Model\Data;

use Magento\Framework\DataObject;
use SM\Review\Api\Data\ReviewDataInterface;

class ReviewData extends DataObject implements ReviewDataInterface
{

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
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * @inheritDoc
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
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
    public function getNickname()
    {
        return $this->getData(self::NICKNAME);
    }

    /**
     * @inheritDoc
     */
    public function getRating()
    {
        return $this->getData(self::RATING);
    }

    /**
     * @inheritDoc
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
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
    public function setProductId($value)
    {
        return $this->setData(self::PRODUCT_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerId($value)
    {
        return $this->setData(self::CUSTOMER_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function setStoreId($value)
    {
        return $this->setData(self::STORE_ID, $value);
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
    public function setNickname($value)
    {
        return $this->setData(self::NICKNAME, $value);
    }

    /**
     * @inheritDoc
     */
    public function setRating($value)
    {
        return $this->setData(self::RATING, $value);
    }

    /**
     * @inheritDoc
     */
    public function setOrderId($value)
    {
        return $this->setData(self::ORDER_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function setImages($value)
    {
        return $this->setData(self::IMAGES, $value);
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
    public function setCreatedAt($value)
    {
        return $this->setData(self::CREATED_AT, $value);
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
    public function setStatusId($value)
    {
        return $this->setData(self::STATUS_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getStatusId()
    {
        return $this->getData(self::STATUS_ID);
    }

    /**
     * @inheritDoc
     */
    public function getProfileImage()
    {
        return $this->getData(self::PROFILE_IMAGE);
    }

    /**
     * @inheritDoc
     */
    public function setProfileImage($value)
    {
        return $this->setData(self::PROFILE_IMAGE, $value);
    }
}
