<?php


namespace SM\Review\Api\Data;

/**
 * Interface ReviewImageInterface
 * @package SM\Review\Api\Data
 */
interface ReviewImageInterface
{
    const REVIEW_ID = "review_id";
    const IMAGE = "image";
    const IS_EDIT = "is_edit";
    /**
     * @return int
     */
    public function getId();
    /**
     * @param $id
     * @return \SM\Review\Api\Data\ReviewImageInterface
     */
    public function setId($id);
    /**
    * @return int
     */
    public function getReviewId();
    /**
     * @return string
     */
    public function getImage();

    /**
     * @param int $reviewId
     * @return \SM\Review\Api\Data\ReviewImageInterface
     */
    public function setReviewId($reviewId);

    /**
     * @param string $image
     * @return \SM\Review\Api\Data\ReviewImageInterface
     */
    public function setImage($image);

    /**
     * @return int
     */
    public function getIsEdit();

    /**
     * @param int $value
     * @return \SM\Review\Api\Data\ReviewImageInterface
     */
    public function setIsEdit($value);


}
