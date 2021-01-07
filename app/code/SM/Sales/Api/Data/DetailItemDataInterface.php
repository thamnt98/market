<?php

namespace SM\Sales\Api\Data;

/**
 * Interface DetailItemDataInterface
 * @package SM\Sales\Api\Data
 */
interface DetailItemDataInterface
{
    const PRODUCT_NAME = "product_name";
    const QUANTITY = "quantity";
    const PRICE = "price";
    const TOTAL = "total";
    const IMAGE_URL = "image_url";
    const URL = "url";
    const SKU = "sku";
    const ITEM_ID = "item_id";
    const HAS_OPTIONS = "has_options";
    const OPTIONS = "options";
    const BUY_REQUEST = "buy_request";
    const PRODUCT_OPTION = "product_option";
    const PRODUCT_TYPE = "product_type";
    const FRESH_PRODUCT = "fresh_product";
    const IS_AVAILABLE = "is_available";

    /**
     * @param int $value
     * @return $this
     */
    public function setItemId($value);

    /**
     * @return int
     */
    public function getItemId();

    /**
     * @param int $value
     * @return $this
     */
    public function setTotal($value);

    /**
     * @return int
     */
    public function getTotal();
    /**
     * @return string
     */
    public function getSku();

    /**
     * @param string $value
     * @return $this
     */
    public function setSku($value);

    /**
     * @return string
     */
    public function getProductName();

    /**
     * @param string $value
     * @return $this
     */
    public function setProductName($value);

    /**
     * @return int
     */
    public function getQuantity();

    /**
     * @param int $value
     * @return $this
     */
    public function setQuantity($value);

    /**
     * @return int
     */
    public function getPrice();

    /**
     * @param int $value
     * @return $this
     */
    public function setPrice($value);

    /**
     * @return string
     */
    public function getImageUrl();

    /**
     * @param string $value
     * @return $this
     */
    public function setImageUrl($value);

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param string $value
     * @return $this
     */
    public function setUrl($value);

    /**
     * @param \SM\Installation\Api\Data\InstallationServiceInterface $data
     * @return $this
     */
    public function setInstallationService($data);

    /**
     * @return \SM\Installation\Api\Data\InstallationServiceInterface
     */
    public function getInstallationService();

    /**
     * @return \SM\Sales\Api\Data\ItemOptionDataInterface[]
     */
    public function getOptions();

    /**
     * @param \SM\Sales\Api\Data\ItemOptionDataInterface[] $value
     * @return $this
     */
    public function setOptions($value);

    /**
     * @return int
     */
    public function getHasOptions();

    /**
     * @param int $value
     * @return $this
     */
    public function setHasOptions($value);

    /**
     * @param string $value
     * @return $this
     */
    public function setBuyRequest($value);

    /**
     * @return \Magento\Catalog\Api\Data\ProductOptionInterface
     */
    public function getProductOption();

    /**
     * @param \Magento\Catalog\Api\Data\ProductOptionInterface $value
     * @return $this
     */
    public function setProductOption($value);

    /**
     * @return string
     */
    public function getProductType();

    /**
     * @param string $value
     * @return $this
     */
    public function setProductType($value);

    /**
     * @param \SM\FreshProductApi\Api\Data\FreshProductInterface $value
     * @return $this
     */
    public function setFreshProduct($value);

    /**
     * @return \SM\FreshProductApi\Api\Data\FreshProductInterface
     */
    public function getFreshProduct();

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsAvailable($value);


    /**
     * @return bool
     */
    public function getIsAvailable();
}
