<?php
namespace SM\Review\Api\Data\Product;

interface ReviewDetailInterface
{
    const PRODUCT_ID = "product_id";
    const IMAGES = "images";

    /**
     * @return \SM\Review\Api\Data\ReviewImageInterface[]
     */
    public function getImages();

    /**
     * @param \SM\Review\Api\Data\ReviewImageInterface[] $value
     * @return $this
     */
    public function setImages($value);
    /**
     * @return int
     */
    public function getReviewId();

    /**
     * @param int $value
     * @return $this
     */
    public function setReviewId($value);

    /**
     * @param string $image
     * @return $this
     */
    public function setProductImage($image);

    /**
     * @return string
     */
    public function getProductImage();
    /**
     * @param string $url
     * @return $this
     */
    public function setProductUrl($url);

    /**
     * @return string
     */
    public function getProductUrl();

    /**
     * @param string $name
     * @return $this
     */
    public function setProductName($name);

    /**
     * @return string
     */
    public function getProductName();

    /**
     * @param int $percent
     * @return $this
     */
    public function setVotePercent($percent);

    /**
     * @return int
     */
    public function getVotePercent();

    /**
     * @param string $title
     * @return $this
     */
    public function setVoteTitle($title);

    /**
     * @return string
     */
    public function getVoteTitle();

    /**
     * @param string $comment
     * @return $this
     */
    public function setVoteComment($comment);

    /**
     * @return string
     */
    public function getVoteComment();

    /**
     * @param mixed[] $arrImage
     * @return $this
     */
    public function setReviewImage($arrImage);

    /**
     * @return mixed[]
     */
    public function getReviewImage();

    /**
     * @return string
     */
    public function getPostReview();

    /**
     * @return int
     */
    public function getProductId();

    /**
     * @param int $value
     * @return $this
     */
    public function setProductId($value);

}
