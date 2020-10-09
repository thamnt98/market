<?php
/**
 * @category SM
 * @package SM_Review
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      dungnm<dungnm@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Review\Api\Data;

interface ReviewedInterface
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
     * @param \SM\Review\Api\Data\Product\ProductReviewedInterface[] $value
     * @return $this
     */
    public function setProducts($value);

    /**
     * @return \SM\Review\Api\Data\Product\ProductReviewedInterface[]
     */
    public function getProducts();
}
