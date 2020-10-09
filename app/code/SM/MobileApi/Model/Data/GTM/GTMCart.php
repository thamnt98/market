<?php

namespace SM\MobileApi\Model\Data\GTM;

use Magento\Framework\Model\AbstractExtensibleModel;
use SM\MobileApi\Api\Data\GTM\GTMCartInterface;

class GTMCart extends AbstractExtensibleModel implements GTMCartInterface
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
    /**
     * @return string
     */
    public function getProductRating(){
        return $this->getData(self::PRODUCT_RATING);
    }

    /**
     * @param string $data
     * @return $this
     */
    public function setProductRating($data){
        return $this->setData(self::PRODUCT_RATING,$data);
    }

    /**
     * @return string
     */
    public function getProductOnSale(){
        return $this->getData(self::PRODUCT_ON_SALE);
    }

    /**
     * @param string $data
     * @return $this
     */
    public function setProductOnSale($data){
        return $this->setData(self::PRODUCT_ON_SALE, $data);
    }

    /**
     * @return string
     */
    public function getProductBundle(){
        return $this->getData(self::PRODUCT_BUNDLE);
    }

    /**
     * @param string $data
     * @return $this
     */
    public function setProductBundle($data){
        return $this->setData(self::PRODUCT_BUNDLE, $data);
    }

    /**
     * @return int
     */
    public function getProductQty(){
        return $this->getData(self::PRODUCT_QTY);
    }

    /**
     * @param int $data
     * @return $this
     */
    public function setProductQty($data){
        return $this->setData(self::PRODUCT_QTY, $data);
    }

    /**
     * @return string
     */
    public function getProductType(){
        $type = $this->getData(self::PRODUCT_TYPE);
        if ($type == 'grouped') {
            $type = 'group';
        }
        return $type;
    }

    /**
     * @param string $data
     * @return $this
     */
    public function setProductType($data){
        return $this->setData(self::PRODUCT_TYPE, $data);
    }

    /**
     * @return string
     */
    public function getApplyVoucher(){
        return $this->getData(self::APPLY_VOUCHER);
    }

    /**
     * @param string $data
     * @return $this
     */
    public function setApplyVoucher($data){
        return $this->setData(self::APPLY_VOUCHER, $data);
    }

    /**
     * @return string
     */
    public function getVoucherId(){
        return $this->getData(self::VOUCHER_ID);
    }

    /**
     * @param string $data
     * @return $this
     */
    public function setVoucherId($data){
        return $this->setData(self::VOUCHER_ID, $data);
    }

    /**
     * @return float
     */
    public function getSalePrice(){
        return $this->getData(self::SALE_PRICE);
    }

    /**
     * @param float $data
     * @return $this
     */
    public function setSalePrice($data){
        return $this->setData(self::SALE_PRICE, $data);
    }
}
