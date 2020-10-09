<?php

namespace SM\MobileApi\Api\Data\Product;

/**
 * Interface for storing product data
 */
interface ProductDetailsInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ID = 'product_id';
    const SKU = 'sku';
    const NAME = 'name';
    const TYPE = 'type';
    const TYPE_ID = 'type_id';
    const FINAL_PRICE = 'final_price';
    const MIN_PRICE = 'min_price';
    const MAX_PRICE = 'max_price';
    const MINIMAL_PRICE = 'minimal_price';
    const TIER_PRICE = 'tier_price';
    const PRICE = 'price';
    const IMAGE = 'image';
    const GALLERY = 'gallery';
    const DESCRIPTION = 'description';
    const SHORT_DESCRIPTION = 'short_description';
    const STOCK = 'stock';
    const IS_IN_STOCK = 'is_in_stock';
    const IS_SALEABLE = 'is_saleable';
    const IS_AVAILABLE = 'is_available';
    const CONFIGURABLE_ATTRIBUTES = 'configurable_attributes';
    const BUNDLE_ITEMS = 'bundle_items';
    const GROUPED_ITEMS = 'grouped_items';
    const OPTIONS = 'options';
    const DELIVERY_METHODS = 'delivery_methods';
    const REQUIRED_PRICE_CALCULATION = 'required_price_calculation';
    const HAS_REQUIRED_OPTIONS = 'has_required_options';
    const PRODUCT_URL = 'product_url';
    const REVIEW_ENABLE = 'review_enable';
    const REVIEW = 'review';
    const CSS_DESCRIPTION_MOBI = "css_description_mobi";

    //use for wish list
    const MEDIA_URLS = 'media_urls';

    const STORE_INFO = 'store_info';
    const SPECIFICATIONS = 'specifications';
    const DELIVERY_RETURN = 'delivery_return';
    const PRODUCT_LABEL = 'product_label';
    const INSTALLATION = 'installation';

    const COUPON_LABEL = 'coupon_label';
    const COUPON_TOOLTIP = 'coupon_tooltip';
    const GTM_DATA = "gtm_data";

    const IS_ALCOHOL = "is_alcohol";
    const IS_TOBACCO = "is_tobacco";

    const FRESH_PRODUCT = "fresh_product";
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
     * @return $this
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
     * Set Product's Name
     *
     * @param $name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * Get Type Id
     *
     * @return string
     */
    public function getTypeId();

    /**
     * Set Type Id
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
     * Set Type
     *
     * @param $type
     *
     * @return $this
     */
    public function setType($type);

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
     * Get main image
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
     * Get image gallery
     *
     * @return \SM\MobileApi\Api\Data\Catalog\Product\ImageInterface[]
     */
    public function getGallery();

    /**
     * @param \SM\MobileApi\Api\Data\Catalog\Product\ImageInterface[] $data
     *
     * @return $this
     */
    public function setGallery($data);

    /**
     * @return \SM\MobileApi\Api\Data\Catalog\Product\Configurable\AttributeInterface[]
     */
    public function getConfigurableAttributes();

    /**
     * @param \SM\MobileApi\Api\Data\Catalog\Product\Configurable\AttributeInterface[] $data
     * @return mixed
     */
    public function setConfigurableAttributes($data);

    /**
     * Get css description for mobile
     *
     * @return \SM\MobileApi\Api\Data\Catalog\Product\GroupedProduct\ProductItemsInterface[]
     */
    public function getGroupedItems();

    /**
     * @param \SM\MobileApi\Api\Data\Catalog\Product\GroupedProduct\ProductItemsInterface[] $data
     *
     * @return $this
     */
    public function setGroupedItems($data);

    /**
     * @return \SM\MobileApi\Api\Data\Catalog\Product\BundleProduct\ProductOptionsInterface[]
     */
    public function getBundleItems();

    /**
     * @param \SM\MobileApi\Api\Data\Catalog\Product\BundleProduct\ProductOptionsInterface[] $data
     *
     * @return $this
     */
    public function setBundleItems($data);

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
     * Get css description for mobile
     *
     * @return string
     */
    public function getCssDescriptionMobi();

    /**
     * @param string $data
     *
     * @return $this
     */
    public function setCssDescriptionMobi($data);
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
     * @return \SM\MobileApi\Api\Data\Catalog\Product\DeliveryInto[]
     */
    public function getDeliveryInto();

    /**
     * @param \SM\MobileApi\Api\Data\Catalog\Product\DeliveryInto[] $delivery
     * @return $this
     */
    public function setDeliveryInto($delivery);

    /**
     * Get css description for mobile
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

    /**
     * Get css description for mobile
     *
     * @return \SM\MobileApi\Api\Data\Catalog\Product\SpecificationsInterface[]
     */
    public function getSpecifications();

    /**
     * @param \SM\MobileApi\Api\Data\Catalog\Product\SpecificationsInterface[] $data
     *
     * @return $this
     */
    public function setSpecifications($data);

    /**
     * @return \SM\MobileApi\Api\Data\Product\DeliveryReturnInterface[]
     */
    public function getDeliveryReturn();

    /**
     * @param \SM\MobileApi\Api\Data\Product\DeliveryReturnInterface[] $data
     * @return $this
     */
    public function setDeliveryReturn($data);

    /**
     * Get css description for mobile
     *
     * @return \SM\MobileApi\Api\Data\ProductLabel\ProductLabelInterface
     */
    public function getProductLabel();

    /**
     * @param \SM\MobileApi\Api\Data\ProductLabel\ProductLabelInterface $data
     *
     * @return $this
     */
    public function setProductLabel($data);

    /**
     * Get css description for mobile
     *
     * @return \SM\MobileApi\Api\Data\ProductInstallation\InstallationInterface
     */
    public function getInstallation();

    /**
     * @param \SM\MobileApi\Api\Data\ProductInstallation\InstallationInterface $data
     *
     * @return $this
     */
    public function setInstallation($data);

    /**
     * @return string
     */
    public function getCouponLabel();

    /**
     * @param string $data
     * @return $this
     */
    public function setCouponLabel($data);

    /**
     * @return string
     */
    public function getCouponTooltip();

    /**
     * @param string $data
     * @return $this
     */
    public function setCouponTooltip($data);

    /**
     * @return \SM\MobileApi\Api\Data\GTM\GTMInterface
     */
    public function getGtmData();

    /**
     * @param \SM\MobileApi\Api\Data\GTM\GTMInterface $value
     * @return mixed
     */
    public function setGtmData($value);

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
}
