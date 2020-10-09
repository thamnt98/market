<?php

namespace SM\MobileApi\Model\Data\Product;

use SM\MobileApi\Api\Data\Product\ProductDetailsInterface;

/**
 * Class for storing Product information
 */
class ProductDetails extends \Magento\Framework\Model\AbstractExtensibleModel implements \SM\MobileApi\Api\Data\Product\ProductDetailsInterface
{
    public function getId()
    {
        return $this->getData(self::ID);
    }

    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    public function getSku()
    {
        return $this->getData(self::SKU);
    }

    public function setSku($sku)
    {
        return $this->setData(self::SKU, $sku);
    }

    public function getName()
    {
        return $this->getData(self::NAME);
    }

    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    public function getTypeId()
    {
        return $this->getData(self::TYPE_ID);
    }

    public function setTypeId($type)
    {
        return $this->setData(self::TYPE_ID, $type);
    }

    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    public function getStock()
    {
        return $this->getData(self::STOCK);
    }

    public function setStock($data)
    {
        return $this->setData(self::STOCK, $data);
    }

    public function getIsInStock()
    {
        return $this->getData(self::IS_IN_STOCK);
    }

    public function setIsInStock($isStock)
    {
        return $this->setData(self::IS_IN_STOCK, $isStock);
    }

    public function getIsSaleable()
    {
        return $this->getData(self::IS_SALEABLE);
    }

    public function setIsSaleable($isSaleable)
    {
        return $this->setData(self::IS_SALEABLE, $isSaleable);
    }

    public function getIsAvailable()
    {
        return $this->getData(self::IS_AVAILABLE);
    }

    public function setIsAvailable($data)
    {
        return $this->setData(self::IS_AVAILABLE, $data);
    }

    public function getFinalPrice()
    {
        return $this->getData(self::FINAL_PRICE);
    }

    public function setFinalPrice($price)
    {
        return $this->setData(self::FINAL_PRICE, $price);
    }

    public function getMinPrice()
    {
        return $this->getData(self::MIN_PRICE);
    }

    public function setMinPrice($price)
    {
        return $this->setData(self::MIN_PRICE, $price);
    }

    public function getMaxPrice()
    {
        return $this->getData(self::MAX_PRICE);
    }

    public function setMaxPrice($price)
    {
        return $this->setData(self::MAX_PRICE, $price);
    }

    public function getTierPrice()
    {
        return $this->getData(self::TIER_PRICE);
    }

    public function setTierPrice($price)
    {
        return $this->setData(self::TIER_PRICE, $price);
    }

    public function getPrice()
    {
        return $this->getData(self::PRICE);
    }

    public function setPrice($price)
    {
        return $this->setData(self::PRICE, $price);
    }

    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    public function getShortDescription()
    {
        return $this->getData(self::SHORT_DESCRIPTION);
    }

    public function setShortDescription($description)
    {
        return $this->setData(self::SHORT_DESCRIPTION, $description);
    }

    public function getImage()
    {
        return $this->getData(self::IMAGE);
    }

    public function setImage($data)
    {
        return $this->setData(self::IMAGE, $data);
    }

    public function getGallery()
    {
        return $this->getData(self::GALLERY);
    }

    public function setGallery($data)
    {
        return $this->setData(self::GALLERY, $data);
    }

    public function getConfigurableAttributes()
    {
        return $this->getData(self::CONFIGURABLE_ATTRIBUTES);
    }

    public function setConfigurableAttributes($data)
    {
        return $this->setData(self::CONFIGURABLE_ATTRIBUTES, $data);
    }

    public function getBundleItems()
    {
        return $this->getData(self::BUNDLE_ITEMS);
    }

    public function setBundleItems($data)
    {
        return $this->setData(self::BUNDLE_ITEMS, $data);
    }

    public function getOptions()
    {
        return $this->getData(self::OPTIONS);
    }

    public function setOptions($data)
    {
        return $this->setData(self::OPTIONS, $data);
    }

    public function getGroupedItems()
    {
        return $this->getData(self::GROUPED_ITEMS);
    }

    public function setGroupedItems($data)
    {
        return $this->setData(self::GROUPED_ITEMS, $data);
    }

    public function getRequiredPriceCalculation()
    {
        return $this->getData(self::REQUIRED_PRICE_CALCULATION);
    }

    public function setRequiredPriceCalculation($data)
    {
        return $this->setData(self::REQUIRED_PRICE_CALCULATION, $data);
    }

    public function getProductUrl()
    {
        return $this->getData(self::PRODUCT_URL);
    }

    public function setProductUrl($data)
    {
        return $this->setData(self::PRODUCT_URL, $data);
    }

    public function getDeliveryInto()
    {
        return $this->getData(self::DELIVERY_METHODS);
    }
    public function setDeliveryInto($delivery)
    {
        return $this->setData(self::DELIVERY_METHODS, $delivery);
    }

    public function getReviewEnable()
    {
        return $this->getData(self::REVIEW_ENABLE);
    }

    public function setReviewEnable($data)
    {
        return $this->setData(self::REVIEW_ENABLE, $data);
    }

    public function getCssDescriptionMobi()
    {
        return $this->getData(self::CSS_DESCRIPTION_MOBI);
    }

    public function setCssDescriptionMobi($data)
    {
        return $this->setData(self::CSS_DESCRIPTION_MOBI, $data);
    }

    public function getReview()
    {
        return $this->getData(self::REVIEW);
    }

    public function setReview($data)
    {
        return $this->setData(self::REVIEW, $data);
    }

    public function getMediaUrls()
    {
        return $this->getData(self::MEDIA_URLS);
    }

    public function setMediaUrls($data)
    {
        return $this->setData(self::MEDIA_URLS, $data);
    }

    public function getStoresInfo()
    {
        return $this->getData(self::STORE_INFO);
    }

    public function setStoresInfo($data)
    {
        return $this->setData(self::STORE_INFO, $data);
    }

    public function getSpecifications()
    {
        return $this->getData(self::SPECIFICATIONS);
    }

    public function setSpecifications($data)
    {
        return $this->setData(self::SPECIFICATIONS, $data);
    }

    public function getDeliveryReturn()
    {
        return $this->getData(self::DELIVERY_RETURN);
    }

    public function setDeliveryReturn($data)
    {
        return $this->setData(self::DELIVERY_RETURN, $data);
    }

    public function getProductLabel()
    {
        return $this->getData(self::PRODUCT_LABEL);
    }

    public function setProductLabel($data)
    {
        return $this->setData(self::PRODUCT_LABEL, $data);
    }

    public function getInstallation()
    {
        return $this->getData(self::INSTALLATION);
    }

    public function setInstallation($data)
    {
        return $this->setData(self::INSTALLATION, $data);
    }

    /**
     * @inheritdoc
     */
    public function getCouponLabel()
    {
        return $this->getData(self::COUPON_LABEL);
    }

    /**
     * @inheritdoc
     */
    public function setCouponLabel($data)
    {
        return $this->setData(self::COUPON_LABEL, $data);
    }

    /**
     * @inheritdoc
     */
    public function getCouponTooltip()
    {
        return $this->getData(self::COUPON_TOOLTIP);
    }

    /**
     * @inheritdoc
     */
    public function setCouponTooltip($data)
    {
        return $this->setData(self::COUPON_TOOLTIP, $data);
    }

    /**
     * @inheritDoc
     */
    public function getGtmData()
    {
        return $this->getData(self::GTM_DATA);
    }

    /**
     * @inheritDoc
     */
    public function setGtmData($value)
    {
        return $this->setData(self::GTM_DATA, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsAlcohol()
    {
        return $this->getData(self::IS_ALCOHOL) ?? false;
    }

    /**
     * @inheritDoc
     */
    public function setIsAlcohol($value)
    {
        return $this->setData(self::IS_ALCOHOL, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsTobacco()
    {
        return $this->getData(self::IS_TOBACCO) ?? false;
    }

    /**
     * @inheritDoc
     */
    public function setIsTobacco($value)
    {
        return $this->setData(self::IS_TOBACCO, $value);
    }

    /**
     * @inheritDoc
     */
    public function getFreshProduct()
    {
        return $this->getData(self::FRESH_PRODUCT);
    }

    /**
     * @inheritDoc
     */
    public function setFreshProduct($value)
    {
        return $this->setData(self::FRESH_PRODUCT, $value);
    }
}
