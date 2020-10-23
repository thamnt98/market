<?php

namespace SM\MobileApi\Model\Data\Catalog\Product\Grouped;

use Magento\Framework\Model\AbstractExtensibleModel;
use SM\MobileApi\Api\Data\Catalog\Product\GroupedProduct\ProductItemsInterface;

/**
 * Class Product
 * @package SM\MobileApi\Model\Data\Catalog\Product\Grouped
 */
class ProductItems extends AbstractExtensibleModel implements ProductItemsInterface
{
    public function getId()
    {
        return $this->getData(self::ID);
    }

    public function setId($data)
    {
        return $this->setData(self::ID, $data);
    }

    public function getSku()
    {
        return $this->getData(self::SKU);
    }

    public function setSku($data)
    {
        return $this->setData(self::SKU, $data);
    }

    public function getImage()
    {
        return$this->getData(self::IMAGE);
    }

    public function setImage($image)
    {
        return $this->setData(self::IMAGE, $image);
    }

    public function getIsSaleable()
    {
        return $this->getData(self::IS_SALEABLE);
    }

    public function setIsSaleable($data)
    {
        return $this->setData(self::IS_SALEABLE, $data);
    }

    public function getFinalPrice()
    {
        return $this->getData(self::FINAL_PRICE);
    }

    public function setFinalPrice($data)
    {
        return $this->setData(self::FINAL_PRICE, $data);
    }

    public function getPrice()
    {
        return $this->getData(self::PRICE);
    }

    public function setPrice($data)
    {
        return $this->setData(self::PRICE, $data);
    }

    public function getStock()
    {
        return $this->getData(self::STOCK);
    }

    public function setStock($data)
    {
        return $this->setData(self::STOCK, $data);
    }

    public function getIsAvailable()
    {
        return $this->getData(self::IS_AVAILABLE);
    }

    public function setIsAvailable($data)
    {
        return $this->setData(self::IS_AVAILABLE, $data);
    }

    public function getProductLabel()
    {
        return $this->getData(self::PRODUCT_LABEL);
    }

    public function setProductLabel($data)
    {
        return $this->setData(self::PRODUCT_LABEL, $data);
    }

    public function getDeliveryInto()
    {
        return $this->getData(self::DELIVERY_METHODS);
    }

    public function setDeliveryInto($data)
    {
        return $this->setData(self::DELIVERY_METHODS, $data);
    }

    public function getStoresInfo()
    {
        return $this->getData(self::STORE_INFO);
    }

    public function setStoresInfo($data)
    {
        return $this->setData(self::STORE_INFO, $data);
    }

    public function getQtyDefault()
    {
        return $this->getData(self::QTY_DEFAULT);
    }

    public function setQtyDefault($data)
    {
        return $this->setData(self::QTY_DEFAULT, $data);
    }

    public function getPosition()
    {
        return $this->getData(self::POSITION);
    }

    public function setPosition($data)
    {
        return $this->setData(self::POSITION, $data);
    }

    public function getName()
    {
        return $this->getData(self::NAME);
    }

    public function setName($data)
    {
        return $this->setData(self::NAME, $data);
    }

    public function setConfigurableAttributes($data)
    {
        return $this->setData(self::CONFIGURABLE_ATTRIBUTES, $data);
    }
    public function getConfigurableAttributes()
    {
        return $this->getData(self::CONFIGURABLE_ATTRIBUTES);
    }

    public function setType($data)
    {
        return $this->setData(self::TYPE, $data);
    }
    public function getType()
    {
        return $this->getData(self::TYPE);
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
}
