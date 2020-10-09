<?php

namespace SM\MobileApi\Model\Data\Product;

use SM\MobileApi\Api\Data\Product\ListInterface;

/**
 * Class for storing category assigned products
 */
class Liist extends \Magento\Framework\Model\AbstractExtensibleModel implements \SM\MobileApi\Api\Data\Product\ListInterface
{
    public function getFilters()
    {
        return $this->getData(self::FILTERS);
    }

    public function setFilters($data)
    {
        return $this->setData(self::FILTERS, $data);
    }

    public function getToolbarInfo()
    {
        return $this->getData(self::TOOLBAR_INFO);
    }

    public function setToolbarInfo($data)
    {
        return $this->setData(self::TOOLBAR_INFO, $data);
    }

    public function getProducts()
    {
        return $this->getData(self::PRODUCTS);
    }

    public function setProducts($data)
    {
        return $this->setData(self::PRODUCTS, $data);
    }

    public function getCategoryId()
    {
        return $this->getData(self::CATEGORY_ID);
    }

    public function setCategoryId($id)
    {
        return $this->setData(self::CATEGORY_ID, $id);
    }

    public function setEndTime($datetime)
    {
        return $this->setData(self::EVENT_END_TIME, $datetime);
    }

    public function getEndTime()
    {
        return $this->getData(self::EVENT_END_TIME);
    }

    public function setEndTimeConverted($datetime)
    {
        return $this->setData(self::EVENT_END_TIME_CONVERTED, $datetime);
    }

    public function getEndTimeConverted()
    {
        return $this->getData(self::EVENT_END_TIME_CONVERTED);
    }

    public function setFlashImage($flashImage)
    {
        return $this->setData(self::FLASH_IMAGE, $flashImage);
    }

    public function getFlashImage()
    {
        return $this->getData(self::FLASH_IMAGE);
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
    public function getIsFresh()
    {
        return $this->getData(self::IS_FRESH);
    }

    /**
     * @inheritDoc
     */
    public function setIsFresh($value)
    {
        return $this->setData(self::IS_FRESH, $value);
    }
}
