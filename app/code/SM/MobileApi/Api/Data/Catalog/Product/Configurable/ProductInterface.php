<?php

namespace SM\MobileApi\Api\Data\Catalog\Product\Configurable;

use Magento\Framework\Api\ExtensibleDataInterface;

interface ProductInterface extends ExtensibleDataInterface
{
    const ID = 'id';
    const SKU = 'sku';
    const STOCK = 'stock';
    const THUMBNAIL_IMAGE = 'thumbnail_image';
    const IS_SALEABLE = 'is_saleable';
    const IS_AVAILABLE = 'is_available';
    const FINAL_PRICE = 'final_price';
    const BACKORDERS = 'backorders';
    const PRODUCT_LABEL = 'product_label';
    const PRICE = 'price';

    /**
     * Get product ID
     *
     * @return int
     */
    public function getId();

    /**
     * @param int $data
     * @return $this
     */
    public function setId($data);

    /**
     * Get product sku
     *
     * @return string
     */
    public function getSku();

    /**
     * @param string $data
     * @return $this
     */
    public function setSku($data);

    /**
     * Get product qty
     *
     * @return float
     */
    public function getStock();

    /**
     * @param float $data
     * @return $this
     */
    public function setStock($data);

    /**
     * @return string
     */
    public function getThumbnailImage();

    /**
     * @param string $image
     * @return $this
     */
    public function setThumbnailImage($image);

    /**
     * Get product is saleable
     *
     * @return int
     */
    public function getIsSaleable();

    /**
     * @param int $data
     * @return $this
     */
    public function setIsSaleable($data);

    /**
     * Get product stock available
     *
     * @return int
     */
    public function getIsAvailable();

    /**
     * @param int $data
     * @return $this
     */
    public function setIsAvailable($data);

    /**
     * Get product stock backorder enable
     *
     * @return int
     */
    public function getBackorders();

    /**
     * @param int $data
     * @return $this
     */
    public function setBackorders($data);

    /**
     * Get product final price
     *
     * @return float
     */
    public function getFinalPrice();

    /**
     * @param float $data
     * @return $this
     */
    public function setFinalPrice($data);

    /**
     * Get product final price
     *
     * @return float
     */
    public function getPrice();

    /**
     * @param float $data
     * @return $this
     */
    public function setPrice($data);

    /**
     * Get product final price
     *
     * @return \SM\MobileApi\Api\Data\ProductLabel\ProductLabelInterface $data
     */
    public function getProductLabel();

    /**
     * @param \SM\MobileApi\Api\Data\ProductLabel\ProductLabelInterface $data
     * @return $this
     */
    public function setProductLabel($data);
}
