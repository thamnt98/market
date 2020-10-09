<?php

namespace SM\MobileApi\Api\Data\Product;

/**
 * Interface for storing product details
 */
interface ProductInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const PRODUCT = 'product';

    /**
     * Get product information
     *
     * @return \SM\MobileApi\Api\Data\Product\ProductDetailsInterface
     */
    public function getProduct();

    /**
     * @param \SM\MobileApi\Api\Data\Product\ProductDetailsInterface $data
     *
     * @return $this
     */
    public function setProduct($data);

}
