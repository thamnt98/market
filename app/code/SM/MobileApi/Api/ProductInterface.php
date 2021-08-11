<?php

namespace SM\MobileApi\Api;


/**
 * Interface for managing category information
 */
interface ProductInterface
{
    /**
     * Get category assigned products
     *
     * @param int  $category_id
     * @param int  $limit
     * @param int  $p
     * @param bool $layer
     * @param int  $customerId
     *
     * @return \SM\MobileApi\Api\Data\Product\ListInterface
     */
    public function getList($category_id, $limit = 12, $p = 1, $layer = true, $customerId = 0);

    /**
     * Get product details
     *
     * @param int $product_id
     *
     * @return \SM\MobileApi\Api\Data\Product\ProductInterface
     */
    public function getDetails($product_id);

    /**
     * Get product details
     *
     * @param string $sku
     * @return \SM\MobileApi\Api\Data\Product\ProductInterface
     */
    public function getDetailsBySKU($sku);

    /**
     * Get product related products
     *
     * @param int $product_id
     * @return \SM\MobileApi\Api\Data\Product\ListInterface
     */
    public function getRelatedProducts($product_id);
}