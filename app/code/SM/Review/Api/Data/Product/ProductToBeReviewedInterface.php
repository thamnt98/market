<?php

namespace SM\Review\Api\Data\Product;

interface ProductToBeReviewedInterface
{
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
     * @param int $id
     * @return $this
     */
    public function setProductId($id);
    /**
     * @return int
     */
    public function getProductId();

}
