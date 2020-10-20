<?php

namespace SM\Review\Api\Data\Product;

interface ProductReviewedInterface
{
    const REVIEW_ID = "review_id";

    /**
     * @param int $value
     * @return \SM\Review\Api\Data\Product\ProductReviewedInterface
     */
    public function setReviewId($value);

    /**
     * @return int
     */
    public function getReviewId();

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
     * @param string $url
     * @return $this
     */
    public function setProductImage($url);

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
     * @param int $percent
     * @return $this
     */
    public function setPercentVote($percent);

    /**
     * @return int
     */
    public function getPercentVote();

    /**
     * @param string $title
     * @return $this
     */
    public function setTitleVote($title);

    /**
     * @return string
     */
    public function getTitleVote();
    /**
     * @param int $id
     * @return $this
     */
    public function setProductId($id);
    /**
     * @return int
     */
    public function getProductId();
}
