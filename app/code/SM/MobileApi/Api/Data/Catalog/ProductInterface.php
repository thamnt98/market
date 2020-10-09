<?php

namespace SM\MobileApi\Api\Data\Catalog;

/**
 * Interface for storing product data
 */
interface ProductInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const PRODUCT_ID = 'product_id';
    const SKU = 'sku';
    const NAME = 'name';
    const TYPE = 'type';
    const POSITION = 'position';
    const FINAL_PRICE = 'final_price';
    const MIN_PRICE = 'min_price';
    const MAX_PRICE = 'max_price';
    const MINIMAL_PRICE = 'minimal_price';
    const TIER_PRICE = 'tier_price';
    const PRICE = 'price';
    const MEDIA_URLS = 'media_urls';
    const TYPE_ID = 'type_id';
    const DESCRIPTION = 'description';
    const SHORT_DESCRIPTION = 'short_description';
    const STATUS = 'status';
    const IS_IN_STOCK = 'is_in_stock';
    const IS_SALEABLE = 'is_saleable';
    const ADDITIONAL_INFORMATION = 'additional_information';
    const CONFIGURABLE_ATTRIBUTES = 'configurable_attributes';
    const BUNDLE_ITEMS = 'bundle_items';
    const GROUPED_ITEMS = 'grouped_items';
    const OPTIONS = 'options';

    /**
     * Get Product Id
     *
     * @return integer
     */
    public function getProductId();

    /**
     * Set Product Id
     *
     * @param $id
     *
     * @return $this
     */
    public function setProductId($id);

    /**
     * Get Product's SKU
     *
     * @return string
     */
    public function getSku();

    /**
     * Set Product's SKU
     *
     * @param $sku
     *
     * @return $this
     */
    public function setSku($sku);

    /**
     * Get Product's Name
     *
     * @return string
     */
    public function getName();

    /**
     * set Product's Name
     *
     * @param $name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * Get Product position
     *
     * @return integer
     */
    public function getPosition();

    /**
     * Set Product position
     *
     * @param $pos
     *
     * @return $this
     */
    public function setPosition($pos);

    /**
     * Get Final price
     *
     * @return float
     */
    public function getFinalPrice();

    /**
     * Set Final price
     *
     * @param $price
     *
     * @return $this
     */
    public function setFinalPrice($price);

    /**
     * Get Min price
     *
     * @return float
     */
    public function getMinPrice();

    /**
     * Set Min price
     *
     * @param $price
     *
     * @return $this
     */
    public function setMinPrice($price);

    /**
     * Get Max price
     *
     * @return float
     */
    public function getMaxPrice();

    /**
     * Set Max price
     *
     * @param $price
     *
     * @return $this
     */
    public function setMaxPrice($price);

    /**
     * Get Minimal price
     *
     * @return float
     */
    public function getMinimalPrice();

    /**
     * Set Minimal price
     *
     * @param $price
     *
     * @return $this
     */
    public function setMinimalPrice($price);

    /**
     * Get Tier prices
     *
     * @return \SM\MobileApi\Api\Data\Catalog\Product\TierPriceInterface[]
     */
    public function getTierPrice();

    /**
     * Set Tier price
     *
     * @param \SM\MobileApi\Api\Data\Catalog\Product\TierPriceInterface[]
     *
     * @return $this
     */
    public function setTierPrice($price);

    /**
     * Get Price
     *
     * @return float
     */
    public function getPrice();

    /**
     * Set Price
     *
     * @param $price
     *
     * @return $this
     */
    public function setPrice($price);

    /**
     * Get Media Urls
     *
     * @return \SM\MobileApi\Api\Data\Catalog\Product\ProductMediaInterface
     */
    public function getMediaUrls();

    /**
     * Set Media Urls
     *
     * @param \SM\MobileApi\Api\Data\Catalog\Product\ProductMediaInterface $data
     *
     * @return $this
     */
    public function setMediaUrls($data);

    /**
     * Get Type Id
     *
     * @return string
     */
    public function getTypeId();

    /**
     * set Type Id
     *
     * @param $type
     *
     * @return $this
     */
    public function setTypeId($type);

    /**
     * Get Type
     *
     * @return string
     */
    public function getType();

    /**
     * set Type
     *
     * @param $type
     *
     * @return $this
     */
    public function setType($type);

    /**
     * Get Description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Set Description
     *
     * @param $description
     *
     * @return $this
     */
    public function setDescription($description);

    /**
     * Get Short Description
     *
     * @return string
     */
    public function getShortDescription();

    /**
     * Set Short Description
     *
     * @param $description
     *
     * @return $this
     */
    public function setShortDescription($description);

    /**
     * Get Status
     *
     * @return integer
     */
    public function getStatus();

    /**
     * Set Status
     *
     * @param $status
     *
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get Is in stock
     *
     * @return integer
     */
    public function getIsInStock();

    /**
     * Set Is in stock
     *
     * @param $isStock
     *
     * @return $this
     */
    public function setIsInStock($isStock);

    /**
     * Get Is Saleable
     *
     * @return integer
     */
    public function getIsSaleable();

    /**
     * Set Is Saleable
     *
     * @param $isSaleable
     *
     * @return $this
     */
    public function setIsSaleable($isSaleable);

    /**
     * Get Additional Information
     *
     * @return \SM\MobileApi\Api\Data\Catalog\Product\AttributeInterface[]
     */
    public function getAdditionalInformation();

    /**
     * Set Additional Information
     *
     * @param \SM\MobileApi\Api\Data\Catalog\Product\AttributeInterface[] $data
     *
     * @return $this
     */
    public function setAdditionalInformation($data);

    /**
     * Get product custom options
     *
     * @return \SM\MobileApi\Api\Data\Catalog\Product\OptionInterface[]
     */
    public function getOptions();

    /**
     * @param \SM\MobileApi\Api\Data\Catalog\Product\OptionInterface[] $data
     *
     * @return $this
     */
    public function setOptions($data);
}
