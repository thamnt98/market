<?php

namespace SM\MobileApi\Model\Data\Product;

use SM\MobileApi\Api\Data\Product\ListItemInterface;

/**
 * Class for storing Product information
 */
class ListItem extends \Magento\Framework\Model\AbstractExtensibleModel implements \SM\MobileApi\Api\Data\Product\ListItemInterface
{
    public function getId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    public function setId($id)
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

    public function getName()
    {
        return $this->getData(self::NAME);
    }

    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
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

    public function getPrice()
    {
        return $this->getData(self::PRICE);
    }

    public function setPrice($price)
    {
        return $this->setData(self::PRICE, $price);
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

    public function getImage()
    {
        return $this->getData(self::IMAGE);
    }

    public function setImage($data)
    {
        return $this->setData(self::IMAGE, $data);
    }

    public function getProductUrl()
    {
        return $this->getData(self::PRODUCT_URL);
    }

    public function setProductUrl($data)
    {
        return $this->setData(self::PRODUCT_URL, $data);
    }

    public function getCategoryNames()
    {
        return $this->getData(self::CATEGORY_NAMES);
    }

    public function setCategoryNames($categoryNames)
    {
        return $this->setData(self::CATEGORY_NAMES, $categoryNames);
    }

    public function getRequiredPriceCalculation()
    {
        return $this->getData(self::REQUIRED_PRICE_CALCULATION);
    }

    public function setRequiredPriceCalculation($data)
    {
        return $this->setData(self::REQUIRED_PRICE_CALCULATION, $data);
    }

    public function getReviewEnable()
    {
        return $this->getData(self::REVIEW_ENABLE);
    }

    public function setReviewEnable($data)
    {
        return $this->setData(self::REVIEW_ENABLE, $data);
    }

    public function getReview()
    {
        return $this->getData(self::REVIEW);
    }

    public function setReview($data)
    {
        return $this->setData(self::REVIEW, $data);
    }

    /**
     * @inheritDoc
     */
    public function getConfigChildCount()
    {
        return $this->getData(self::CONFIG_CHILD_COUNT);
    }

    /**
     * @inheritDoc
     */
    public function setConfigChildCount($data)
    {
        return $this->setData(self::CONFIG_CHILD_COUNT, $data);
    }

    /**
     * @inheritDoc
     */
    public function getProductLabel()
    {
        return $this->getData(self::PRODUCT_LABEL);
    }

    /**
     * @inheritDoc
     */
    public function setProductLabel($data)
    {
        return $this->setData(self::PRODUCT_LABEL, $data);
    }

    public function getIsFlashSale()
    {
        return $this->getData(self::IS_FLASH_SALE);
    }

    public function setIsFlashSale($data)
    {
        return $this->setData(self::IS_FLASH_SALE, $data);
    }

    public function getFlashSaleQty()
    {
        return $this->getData(self::FLASH_SALE_QTY);
    }

    public function setFlashSaleQty($data)
    {
        return $this->setData(self::FLASH_SALE_QTY, $data);
    }

    public function getFlashSaleQtyPerCustomer()
    {
        return $this->getData(self::FLASH_SALE_QTY_PER_CUSTOMER);
    }

    public function setFlashSaleQtyPerCustomer($data)
    {
        return $this->setData(self::FLASH_SALE_QTY_PER_CUSTOMER, $data);
    }

    public function getFlashSaleQtyAvailable()
    {
        return $this->getData(self::FLASH_SALE_QTY_AVAILABLE);
    }

    public function setFlashSaleQtyAvailable($data)
    {
        return $this->setData(self::FLASH_SALE_QTY_AVAILABLE, $data);
    }

    public function getGtmData()
    {
        return $this->getData(self::GTM_DATA);
    }

    public function setGtmData($data)
    {
        return $this->setData(self::GTM_DATA, $data);
    }

    /**
     * @return int
     */
    public function getItemId()
    {
        return $this->getData(self::ITEM_ID);
    }

    /**
     * @param int $data
     * @return $this
     */
    public function setItemId($data)
    {
        return $this->setData(self::ITEM_ID, $data);
    }

    /**
     * @return int
     */
    public function getItemQty()
    {
        return $this->getData(self::ITEM_QTY);
    }

    /**
     * @param int $data
     * @return $this
     */
    public function setItemQty($data)
    {
        return $this->setData(self::ITEM_QTY, $data);
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

    /**
     * @inheritDoc
     */
    public function getDiscountPercent()
    {
        return $this->getData(self::DISCOUNT_PERCENT);
    }

    /**
     * @inheritDoc
     */
    public function setDiscountPercent($value)
    {
        return $this->setData(self::DISCOUNT_PERCENT, $value);
    }
}
