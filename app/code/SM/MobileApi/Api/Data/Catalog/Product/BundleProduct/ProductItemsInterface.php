<?php

namespace SM\MobileApi\Api\Data\Catalog\Product\BundleProduct;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface ProductItemsInterface
 *
 * @package SM\MobileApi\Api\Data\Catalog\Product\BundleProduct
 */
interface ProductItemsInterface extends ExtensibleDataInterface
{
    const ID = 'id';
    const SELECTION_ID = 'selection_id';
    const SKU = 'sku';
    const STOCK = 'stock';
    const IS_SALEABLE = 'is_saleable';
    const IS_AVAILABLE = 'is_available';
    const FINAL_PRICE = 'final_price';
    const BACKORDERS = 'backorders';
    const PRODUCT_LABEL = 'product_label';
    const SELECTION_QTY = 'selection_qty';
    const NAME = 'name';
    const TYPE = 'type_id';
    const POSITION = 'position';
    const IS_DEFAULT = 'is_default';
    const CONFIGURABLE_ATTRIBUTES = 'configurable_attributes';
    const PRICE = 'price';
    const IMAGE = 'image';
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
     * @return int
     */
    public function getSelectionId();

    /**
     * @param int $data
     * @return $this
     */
    public function setSelectionId($data);

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
     * @return string
     */
    public function getImage();

    /**
     * @param string $image
     * @return $this
     */
    public function setImage($image);

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
     * Get product label
     *
     * @return \SM\MobileApi\Api\Data\ProductLabel\ProductLabelInterface
     */
    public function getProductLabel();

    /**
     * @param \SM\MobileApi\Api\Data\ProductLabel\ProductLabelInterface $data
     * @return $this
     */
    public function setProductLabel($data);
    /**
     * Get product qty default
     *
     * @return int
     */
    public function getSelectionQty();

    /**
     * @param int $data
     * @return $this
     */
    public function setSelectionQty($data);

    /**
     * Get product Position
     *
     * @return int
     */
    public function getPosition();

    /**
     * @param int $data
     * @return $this
     */
    public function setPosition($data);

    /**
     * Get product name
     *
     * @return string
     */
    public function getName();

    /**
     * @param string $data
     * @return $this
     */
    public function setName($data);

    /**
     * Get product type
     *
     * @return string
     */
    public function getType();

    /**
     * @param string $data
     * @return $this
     */
    public function setType($data);

    /**
     * Check is default option
     *
     * @return int
     */
    public function getIsDefault();

    /**
     * @param int $data
     * @return $this
     */
    public function setIsDefault($data);

    /**
     * Get product child type configurable
     *
     * @return \SM\MobileApi\Api\Data\Catalog\Product\Configurable\AttributeInterface[]
     */
    public function getConfigurableAttributes();

    /**
     * @param \SM\MobileApi\Api\Data\Catalog\Product\Configurable\AttributeInterface[] $data
     * @return $this
     */
    public function setConfigurableAttributes($data);
}
