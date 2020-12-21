<?php

namespace SM\MobileApi\Api\Data\Product;

interface ListItemInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const PRODUCT_ID = 'product_id';
    const SKU = 'sku';
    const NAME = 'name';
    const TYPE = 'type';
    const TYPE_ID = 'type_id';
    const PRODUCT_URL = 'product_url';
    const CATEGORY_NAMES = 'category_names';
    const FINAL_PRICE = 'final_price';
    const MIN_PRICE = 'min_price';
    const MAX_PRICE = 'max_price';
    const MINIMAL_PRICE = 'minimal_price';
    const PRICE = 'price';
    const IMAGE = 'image';
    const DESCRIPTION = 'description';
    const SHORT_DESCRIPTION = 'short_description';
    const STOCK = 'stock';
    const IS_IN_STOCK = 'is_in_stock';
    const IS_SALEABLE = 'is_saleable';
    const IS_AVAILABLE = 'is_available';
    const REQUIRED_PRICE_CALCULATION = 'required_price_calculation';
    const HAS_REQUIRED_OPTIONS = 'has_required_options';
    const REVIEW_ENABLE = 'review_enable';
    const REVIEW = 'review';
    const BUNDLE_ATTRIBUTES = 'bundle_attributes';
    const CONFIG_CHILD_COUNT = 'config_child_count';
    const PRODUCT_LABEL = 'product_label';

    const PRODUCT_SIZE = 'product_size';
    const PRODUCT_VOLUME = 'product_volume';
    const PRODUCT_WEIGHT = 'product_weight';
    const PRODUCT_BRAND = 'product_brand';
    const PRODUCT_VARIANT = 'product_variant';
    const DISCOUNT_PRICE = 'discount_price';
    const INITIAL_PRICE = 'initial_price';

    const GTM_DATA = 'gtm_data';

    const IS_FLASH_SALE = 'is_flash_sale';
    const FLASH_SALE_QTY  = 'flash_sale_qty';
    const FLASH_SALE_QTY_PER_CUSTOMER  = 'flash_sale_qty_per_customer';
    const FLASH_SALE_QTY_AVAILABLE = 'flash_sale_qty_available';

    const ITEM_ID = 'item_id';
    const ITEM_QTY= 'item_qty';

    const IS_ALCOHOL = "is_alcohol";
    const IS_TOBACCO = "is_tobacco";

    const FRESH_PRODUCT = "fresh_product";
    const DISCOUNT_PERCENT = "discount_percent";

    /**
     * Get Product Id
     *
     * @return integer
     */
    public function getId();

    /**
     * Set Product Id
     *
     * @param $id
     *
     * @return \SM\MobileApi\Api\Data\Product\ListItemInterface
     */
    public function setId($id);

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
     * Get product URL
     *
     * @return string
     */
    public function getProductUrl();

    /**
     * @param string $data
     *
     * @return $this
     */
    public function setProductUrl($data);

    /**
     * @return string[]
     */
    public function getCategoryNames();

    /**
     * @param string[] $categoryNames
     * @return $this
     */
    public function setCategoryNames($categoryNames);

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
     * @return string
     */
    public function getImage();

    /**
     * Set Media Urls
     *
     * @param string $data
     *
     * @return $this
     */
    public function setImage($data);

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
     * Get product stock qty
     *
     * @return float
     */
    public function getStock();

    /**
     * @param float $data
     *
     * @return $this
     */
    public function setStock($data);

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
     * Get Is Available, used for display stock label
     *
     * @return int
     */
    public function getIsAvailable();

    /**
     * @param int $data
     *
     * @return $this
     */
    public function setIsAvailable($data);

    /**
     * Get is required price calculation
     *
     * @return bool
     */
    public function getRequiredPriceCalculation();

    /**
     * @param bool $data
     *
     * @return $this
     */
    public function setRequiredPriceCalculation($data);

    /**
     * Get is review enabled
     *
     * @return bool
     */
    public function getReviewEnable();

    /**
     * @param bool $data
     *
     * @return $this
     */
    public function setReviewEnable($data);

    /**
     * @return \SM\MobileApi\Api\Data\Catalog\Product\Review\OverviewInterface
     */
    public function getReview();

    /**
     * @param \SM\MobileApi\Api\Data\Catalog\Product\Review\OverviewInterface $data
     * @return $this
     */
    public function setReview($data);

    /**
     * @return int
     */
    public function getConfigChildCount();

    /**
     * @param int $data
     * @return $this
     */
    public function setConfigChildCount($data);

    /**
     * @return \SM\MobileApi\Api\Data\ProductLabel\ProductLabelInterface
     */
    public function getProductLabel();

    /**
     * @param \SM\MobileApi\Api\Data\ProductLabel\ProductLabelInterface $data
     * @return $this
     */
    public function setProductLabel($data);

    /**
     * @return int
     */
    public function getIsFlashSale();

    /**
     * @param int $data
     * @return $this
     */
    public function setIsFlashSale($data);

    /**
     * @return int
     */
    public function getFlashSaleQty();

    /**
     * @param $data
     * @return $this
     */
    public function setFlashSaleQty($data);

    /**
     * @return int
     */
    public function getFlashSaleQtyPerCustomer();

    /**
     * @param int $data
     * @return $this
     */
    public function setFlashSaleQtyPerCustomer($data);

    /**
     * @return int
     */
    public function getFlashSaleQtyAvailable();

    /**
     * @param int $data
     * @return $this
     */
    public function setFlashSaleQtyAvailable($data);

    /**
     * @return \SM\MobileApi\Api\Data\GTM\GTMInterface
     */
    public function getGtmData();

    /**
     * @param \SM\MobileApi\Api\Data\GTM\GTMInterface $data
     * @return $this
     */
    public function setGtmData($data);

    /**
     * @return int
     */
    public function getItemId();

    /**
     * @param int $data
     * @return $this
     */
    public function setItemId($data);

    /**
     * @return int
     */
    public function getItemQty();

    /**
     * @param int $data
     * @return $this
     */
    public function setItemQty($data);


    /**
     * @return bool
     */
    public function getIsAlcohol();

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsAlcohol($value);

    /**
     * @return bool
     */
    public function getIsTobacco();

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsTobacco($value);

    /**
     * @return \SM\FreshProductApi\Api\Data\FreshProductInterface
     */
    public function getFreshProduct();

    /**
     * @param \SM\FreshProductApi\Api\Data\FreshProductInterface $value
     * @return $this
     */
    public function setFreshProduct($value);

    /**
     * @return int
     */
    public function getDiscountPercent();

    /**
     * @param int $value
     * @return int
     */
    public function setDiscountPercent($value);
}
