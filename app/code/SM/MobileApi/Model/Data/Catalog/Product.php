<?php

namespace SM\MobileApi\Model\Data\Catalog;

/**
 * Class for storing Product information
 */
class Product extends \Magento\Framework\Model\AbstractExtensibleModel implements \SM\MobileApi\Api\Data\Catalog\ProductInterface
{
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    public function setProductId($id)
    {
        return $this->setData(self::PRODUCT_ID, $id);
    }

    public function getSku()
    {
        return $this->getData(self::SKU);
    }

    public function setSku($sku)
    {
        return $this->setData(self::SKU, $sku);
    }

    public function getMinimalPrice()
    {
        return $this->getData(self::MINIMAL_PRICE);
    }

    public function setMinimalPrice($price)
    {
        return $this->setData(self::MINIMAL_PRICE, $price);
    }

    public function getName()
    {
        return $this->getData(self::NAME);
    }

    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    public function getPosition()
    {
        return $this->getData(self::POSITION);
    }

    public function setPosition($pos)
    {
        return $this->setData(self::POSITION, $pos);
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

    public function getMediaUrls()
    {
        return $this->getData(self::MEDIA_URLS);
    }

    public function setMediaUrls($data)
    {
        return $this->setData(self::MEDIA_URLS, $data);
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

    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
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

    public function getAdditionalInformation()
    {
        return $this->getData(self::ADDITIONAL_INFORMATION);
    }

    public function setAdditionalInformation($data)
    {
        return $this->setData(self::ADDITIONAL_INFORMATION, $data);
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
}
