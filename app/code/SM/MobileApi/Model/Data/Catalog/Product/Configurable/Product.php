<?php

namespace SM\MobileApi\Model\Data\Catalog\Product\Configurable;

use Magento\Framework\Model\AbstractExtensibleModel;
use SM\MobileApi\Api\Data\Catalog\Product\Configurable\ProductInterface;

/**
 * Class Product
 * @package SM\MobileApi\Model\Data\Catalog\Product\Configurable
 */
class Product extends AbstractExtensibleModel implements ProductInterface
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

    public function getThumbnailImage()
    {
        return $this->getData(self::THUMBNAIL_IMAGE);
    }

    public function setThumbnailImage($image)
    {
        return $this->setData(self::THUMBNAIL_IMAGE, $image);
    }

    public function getIsAvailable()
    {
        return $this->getData(self::IS_AVAILABLE);
    }

    public function setIsAvailable($data)
    {
        return $this->setData(self::IS_AVAILABLE, $data);
    }

    public function getBackorders()
    {
        return $this->getData(self::BACKORDERS);
    }

    public function setBackorders($data)
    {
        return $this->setData(self::BACKORDERS, $data);
    }

    public function getProductLabel()
    {
        return $this->getData(self::PRODUCT_LABEL);
    }

    public function setProductLabel($data)
    {
        return $this->setData(self::PRODUCT_LABEL, $data);
    }
}
