<?php


namespace SM\Review\Api\Data;

/**
 * Interface CheckResultDataInterface
 * @package SM\Review\Api\Data
 */
interface CheckResultDataInterface
{
    const IS_ALLOW = "is_allow";
    const ORDER_ID = "order_id";

    /**
     * @return int
     */
    public function getIsAllow();

    /**
     * @return int
     */
    public function getOrderId();

    /**
     * @param int $value
     * @return \SM\Review\Api\Data\CheckResultDataInterface
     */
    public function setIsAllow($value);
    /**
     * @param int $value
     * @return \SM\Review\Api\Data\CheckResultDataInterface
     */
    public function setOrderId($value);
}
