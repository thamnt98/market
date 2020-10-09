<?php
namespace SM\FlashSale\Api;
use SM\FlashSale\Api\Data\FlashSaleDateInterface;
interface FlashSaleEventInterface {
    /**
     * Get open flash sale event
     * @api
     * @return mixed[]
     */
    public function getEvent();

    /**
     * Get Flash sale event product + event end time + category id
     * @param int $limit
     * @param int $p
     * @return \SM\MobileApi\Api\Data\Product\ListInterface
     * @throws \Magento\Framework\Webapi\Exception
     * @api
     */
    public function getEventProduct($limit = 12, $p = 1);

    /**
     * Get Flash sale event product
     * @api
     * @return \SM\MobileApi\Api\Data\Product\ListInterface
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function getEventProductOnly();

    /**
     * Get Flash sale event end time
     * @api
     * @return \SM\FlashSale\Api\Data\FlashSaleDateInterface
     */
    public function getEventEndTime();

    /**
     * @param int $productId
     * @return \SM\FlashSale\Api\Data\FlashSaleDateInterface
     */
    public function getEventEndTimeByProduct($productId);
}
