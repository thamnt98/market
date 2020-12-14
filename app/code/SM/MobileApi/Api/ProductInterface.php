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
     * @param int  $customerId
     * @param bool $layer
     *
     * @return \SM\MobileApi\Api\Data\Product\ListInterface
     */
    public function getList($category_id, $customerId, $layer = true);

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
     *
     * @param int $customerId
     * @return \SM\MobileApi\Api\Data\Product\ProductInterface
     */
    public function getDetailsBySKU($sku, $customerId);

    /**
     * Get product related products
     *
     * @param int $product_id
     * @return \SM\MobileApi\Api\Data\Product\ListInterface
     */
    public function getRelatedProducts($product_id);
}
