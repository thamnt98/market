<?php
/**
 * @category Magento
 * @package SM\Review\Api\Data
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Review\Api\Data;

/**
 * Interface ReviewDataInterface
 * @package SM\Review\Api\Data
 */
interface ReviewDataInterface
{
    const REVIEW_ID = "review_id";
    const PRODUCT_ID = "product_id";
    const CUSTOMER_ID = "customer_id";
    const STORE_ID = "store_id";
    const TITLE = "title";
    const DETAIL = "detail";
    const NICKNAME = "nickname";
    const RATING = "rating";
    const ORDER_ID = "order_id";
    const IMAGES = "images";
    const CREATED_AT = "created_at";
    const STATUS_ID = "status_id";
    const PROFILE_IMAGE = "profile_image";

    /**
     * @return string
     */
    public function getProfileImage();

    /**
     * @param string $value
     * @return $this
     */
    public function setProfileImage($value);
    /**
     * @param int $value
     * @return $this
     */
    public function setStatusId($value);
    /**
     * @return int
     */
    public function getStatusId();
    /**
     * @param string $value
     * @return $this
     */
    public function setCreatedAt($value);
    /**
     * @return string
     */
    public function getCreatedAt();
    /**
     * @param string[]
     * @return $this
     */
    public function setImages($value);
    /**
     * @return string[]
     */
    public function getImages();
    /**
     * @return int
     */
    public function getReviewId();

    /**
     * @return int
     */
    public function getProductId();

    /**
     * @return int
     */
    public function getCustomerId();

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return string
     */
    public function getDetail();

    /**
     * @return string
     */
    public function getNickname();

    /**
     * @return int
     */
    public function getRating();

    /**
     * @return int
     */
    public function getOrderId();

    /**
     * @param int $value
     * @return $this
     */
    public function setReviewId($value);

    /**
     * @param int $value
     * @return $this
     */
    public function setProductId($value);

    /**
     * @param int $value
     * @return $this
     */
    public function setCustomerId($value);

    /**
     * @param int $value
     * @return $this
     */
    public function setStoreId($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setTitle($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setDetail($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setNickname($value);

    /**
     * @param int $value
     * @return $this
     */
    public function setRating($value);

    /**
     * @param int $value
     * @return $this
     */
    public function setOrderId($value);
}
