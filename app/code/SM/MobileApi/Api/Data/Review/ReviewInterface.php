<?php

namespace SM\MobileApi\Api\Data\Review;

/**
 * Interface for storing review data
 */
interface ReviewInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const NICKNAME = 'nickname';
    const CREATE_AT = 'create_at';
    const REVIEW = 'review';
    const CUSTOMER_IMAGE = 'customer_image';

    /**
     * Get author nickname
     *
     * @return string
     */
    public function getNickname();

    /**
     * @param string $value
     * @return $this
     */
    public function setNickname($value);

    /**
     * Get review submit time
     *
     * @return string
     */
    public function getCreateAt();

    /**
     * @param string $value
     * @return $this
     */
    public function setCreateAt($value);

    /**
     * Get ratings
     *
     * @return \SM\MobileApi\Api\Data\Review\RatingInterface[]
     */
    public function getReview();

    /**
     * @param \SM\MobileApi\Api\Data\Review\RatingInterface[] $value
     * @return $this
     */
    public function setReview($value);

    /**
     * Get customer image
     *
     * @return string
     */
    public function getCustomerImage();

    /**
     * @param string $image
     * @return $this
     */
    public function setCustomerImage($image);
}
