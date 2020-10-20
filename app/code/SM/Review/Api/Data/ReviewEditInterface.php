<?php

namespace SM\Review\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface ReviewEditInterface
 * @package SM\Review\Api\Data
 */
interface ReviewEditInterface extends ExtensibleDataInterface
{
    const ENTITY_ID = "entity_id";
    const REVIEW_ID  = "review_id";
    const CREATED_AT = "created_at";
    const TITLE = "title";
    const DETAIL = "detail";
    const VOTE_VALUE = "vote_value";
    const IMAGE_CHANGED = "image_changed";
    const IMAGES = "images";

    /**
     * @return \SM\Review\Api\Data\ReviewImageInterface[]
     */
    public function getImages();

    /**
     * @param \SM\Review\Api\Data\ReviewImageInterface[] $value
     * @return \SM\Review\Api\Data\ReviewEditInterface
     */
    public function setImages($value);
    /**
     * @param int $value
     * @return \SM\Review\Api\Data\ReviewEditInterface
     */
    public function setEntityId($value);

    /**
     * @param int $value
     * @return \SM\Review\Api\Data\ReviewEditInterface
     */
    public function setReviewId($value);

    /**
     * @param string $value
     * @return \SM\Review\Api\Data\ReviewEditInterface
     */
    public function setCreatedAt($value);

    /**
     * @param string $value
     * @return \SM\Review\Api\Data\ReviewEditInterface
     */
    public function setTitle($value);

    /**
     * @param string $value
     * @return \SM\Review\Api\Data\ReviewEditInterface
     */
    public function setDetail($value);

    /**
     * @param int $value
     * @return \SM\Review\Api\Data\ReviewEditInterface
     */
    public function setVoteValue($value);

    /**
     * @param int $value
     * @return \SM\Review\Api\Data\ReviewEditInterface
     */
    public function setImageChanged($value);
    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @return int
     */
    public function getReviewId();

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return string
     */
    public function getDetail();

    /**
     * @return int
     */
    public function getVoteValue();

    /**
     * @return int
     */
    public function getImageChanged();
}
