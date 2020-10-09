<?php

namespace SM\MobileApi\Api\Data\Catalog\Product\GroupedProduct;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface ProductItemsInterface
 *
 * @package SM\MobileApi\Api\Data\Catalog\Product\GroupedProduct
 */
interface ProductItemsInterface extends ExtensibleDataInterface
{
    const ID = 'id';
    const SKU = 'sku';
    const STOCK = 'stock';
    const IS_SALEABLE = 'is_saleable';
    const IS_AVAILABLE = 'is_available';
    const FINAL_PRICE = 'final_price';
    const PRICE = 'price';
    const PRODUCT_LABEL = 'product_label';
    const CONFIGURABLE_ATTRIBUTES = 'configurable_attributes';
    const DELIVERY_METHODS = 'delivery_methods';
    const STORE_INFO = 'store_info';
    const QTY_DEFAULT = 'qty';
    const POSITION = 'position';
    const NAME = 'name';
    const TYPE = 'type_id';
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
    public function getQtyDefault();

    /**
     * @param int $data
     * @return $this
     */
    public function setQtyDefault($data);

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
     * Get product type configurable
     *
     * @return \SM\MobileApi\Api\Data\Catalog\Product\Configurable\AttributeInterface[]
     */
    public function getConfigurableAttributes();

    /**
     * @param \SM\MobileApi\Api\Data\Catalog\Product\Configurable\AttributeInterface[] $data
     * @return $this
     */
    public function setConfigurableAttributes($data);

    /**
     * @return \SM\MobileApi\Api\Data\Catalog\Product\DeliveryInto[]
     */
    public function getDeliveryInto();

    /**
     * @param \SM\MobileApi\Api\Data\Catalog\Product\DeliveryInto[] $delivery
     * @return $this
     */
    public function setDeliveryInto($delivery);

    /**
     * Get Store information
     *
     * @return \SM\MobileApi\Api\Data\Catalog\Product\StoreInfoInterface[]
     */
    public function getStoresInfo();

    /**
     * @param \SM\MobileApi\Api\Data\Catalog\Product\StoreInfoInterface[] $data
     *
     * @return $this
     */
    public function setStoresInfo($data);
}
