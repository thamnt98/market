<?php

namespace SM\MobileApi\Model\Data\GTM;

use Magento\Framework\Model\AbstractExtensibleModel;
use SM\MobileApi\Api\Data\GTM\GTMInterface;

class GTM extends AbstractExtensibleModel implements GTMInterface
{
    public function getProductName()
    {
        return $this->getData(self::PRODUCT_NAME);
    }

    public function setProductName($value)
    {
        return $this->setData(self::PRODUCT_NAME, $value);
    }

    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    public function setProductId($value)
    {
        return $this->setData(self::PRODUCT_ID, $value);
    }

    public function getProductPrice()
    {
        return $this->getData(self::PRODUCT_PRICE);
    }

    public function setProductPrice($value)
    {
        return $this->setData(self::PRODUCT_PRICE, $value);
    }

    public function getProductCategory()
    {
        return $this->getData(self::PRODUCT_CATEGORY);
    }

    public function setProductCategory($value)
    {
        return $this->setData(self::PRODUCT_CATEGORY, $value);
    }

    public function getProductSize()
    {
        return $this->getData(self::PRODUCT_SIZE);
    }

    public function setProductSize($data)
    {
        return $this->setData(self::PRODUCT_SIZE, $data);
    }

    public function getProductVolume()
    {
        return $this->getData(self::PRODUCT_VOLUME);
    }

    public function setProductVolume($data)
    {
        return $this->setData(self::PRODUCT_VOLUME, $data);
    }

    public function getProductWeight()
    {
        return $this->getData(self::PRODUCT_WEIGHT);
    }

    public function setProductWeight($data)
    {
        return $this->setData(self::PRODUCT_WEIGHT, $data);
    }

    public function getProductBrand()
    {
        return $this->getData(self::PRODUCT_BRAND);
    }

    public function setProductBrand($data)
    {
        return $this->setData(self::PRODUCT_BRAND, $data);
    }

    public function getProductVariant()
    {
        return $this->getData(self::PRODUCT_VARIANT);
    }

    public function setProductVariant($data)
    {
        return $this->setData(self::PRODUCT_VARIANT, $data);
    }

    public function getDiscountPrice()
    {
        return $this->getData(self::DISCOUNT_PRICE);
    }

    public function setDiscountPrice($data)
    {
        return $this->setData(self::DISCOUNT_PRICE, $data);
    }

    public function getProductList()
    {
        return $this->getData(self::PRODUCT_LIST);
    }

    public function setProductList($data)
    {
        return $this->setData(self::PRODUCT_LIST, $data);
    }

    public function getInitialPrice()
    {
        return $this->getData(self::INITIAL_PRICE);
    }

    public function setInitialPrice($data)
    {
        return $this->setData(self::INITIAL_PRICE, $data);
    }

    public function getDiscountRate()
    {
        return $this->getData(self::DISCOUNT_RATE);
    }

    public function setDiscountRate($data)
    {
        return $this->setData(self::DISCOUNT_RATE, $data);
    }

    public function getProductRating(){
        return $this->getData(self::PRODUCT_RATING);
    }

    public function setProductRating($data){
        return $this->setData(self::PRODUCT_RATING,$data);
    }

    public function getProductOnSale(){
        return $this->getData(self::PRODUCT_ON_SALE);
    }

    public function setProductOnSale($data){
        return $this->setData(self::PRODUCT_ON_SALE,$data);
    }

    public function getProductType()
    {
        $type = $this->getData(self::PRODUCT_TYPE);
        if ($type == 'grouped') {
            $type = 'group';
        }
        return $type;
    }

    public function setProductType($data)
    {
        return $this->setData(self::PRODUCT_TYPE, $data);
    }
}
