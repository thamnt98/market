<?php

namespace SM\Review\Api\Data;

/**
 * Interface ToBeReviewedInterface
 * @package SM\Review\Api\Data
 */
interface ToBeReviewedInterface
{
    const ORDER_ID = "order_id";
    const REFERENCE_NUMBER = "reference_number";
    const TIME_CREATED = "time_created";
    const PRODUCTS = "products";

    /**
     * @param int $value
     * @return $this
     */
    public function setOrderId($value);

    /**
     * @return int
     */
    public function getOrderId();

    /**
     * @param string $value
     * @return $this
     */
    public function setReferenceNumber($value);

    /**
     * @return string
     */
    public function getReferenceNumber();

    /**
     * @param string $value
     * @return $this
     */
    public function setTimeCreated($value);

    /**
     * @return string
     */
    public function getTimeCreated();

    /**
     * @param \SM\Review\Api\Data\Product\ProductToBeReviewedInterface[] $value
     * @return $this
     */
    public function setProducts($value);

    /**
     * @return \SM\Review\Api\Data\Product\ProductToBeReviewedInterface[]
     */
    public function getProducts();
}
