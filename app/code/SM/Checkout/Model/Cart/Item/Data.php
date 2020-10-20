<?php
/**
 * Class Data
 * @package SM\Checkout\Model\Cart\Item
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Checkout\Model\Cart\Item;

use Magento\Framework\Api\AbstractSimpleObject;
use SM\Checkout\Api\Data\CartItemDataInterface;

class Data extends AbstractSimpleObject implements CartItemDataInterface
{
    /**
     * @inheritDoc
     */
    public function getOptionList()
    {
        return $this->_get(self::OPTION_LIST);
    }

    /**
     * @param $data
     */
    public function setOptionList($data)
    {
        $this->setData(self::OPTION_LIST, $data);
    }

    /**
     * @inheritDoc
     */
    public function getConfigOptionList()
    {
        return $this->_get(self::CONFIG_OPTION_LIST);
    }

    /**
     * @inheritDoc
     */
    public function setConfigOptionList($configOptionList)
    {
        $this->setData(self::CONFIG_OPTION_LIST, $configOptionList);
    }

    /**
     * @inheritDoc
     */
    public function getInstallationData()
    {
        return $this->_get(self::INSTALLATION_DATA);
    }

    /**
     * @param \SM\Checkout\Api\Data\CartItem\InstallationInterface $data
     * @return $this
     */
    public function setInstallationData($data)
    {
        $this->setData(self::INSTALLATION_DATA, $data);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getProductLabel()
    {
        return $this->_get(self::PRODUCT_LABEL);
    }

    /**
     * @param $data
     */
    public function setProductLabel($data)
    {
        $this->setData(self::PRODUCT_LABEL, $data);
    }

    /**
     * @inheritDoc
     */
    public function getImageUrl()
    {
        return $this->_get(self::PRODUCT_IMAGE);
    }

    /**
     * @param $url
     */
    public function setImageUrl($url)
    {
        $this->setData(self::PRODUCT_IMAGE, $url);
    }

    /**
     * @inheritDoc
     */
    public function getProductSku()
    {
        return $this->_get(self::PRODUCT_SKU);
    }

    /**
     * @inheritDoc
     */
    public function setProductSku($sku)
    {
        $this->setData(self::PRODUCT_SKU, $sku);
    }

    /**
     * @inheritDoc
     */
    public function getIsChecked()
    {
        return $this->_get(self::IS_CHECKED);
    }

    /**
     * @inheritDoc
     */
    public function setIsChecked($data)
    {
        return $this->setData(self::IS_CHECKED, $data);
    }

    /**
     * @inheritDoc
     */
    public function getProductId()
    {
        return $this->_get(self::PRODUCT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setProductId($data)
    {
        return $this->setData(self::PRODUCT_ID, $data);
    }

    public function getSalableQuantity()
    {
        return $this->_get(self::SALABLE_QUANTITY);
    }

    public function setSalableQuantity($qty)
    {
        return $this->setData(self::SALABLE_QUANTITY, $qty);
    }

    /**
     * @inheritDoc
     */
    public function getDiscountPercent(){
        return $this->_get(self::DISCOUNT_PERCENT);
    }

    /**
     * @inheritDoc
     */
    public function setDiscountPercent($data){
        return $this->setData(self::DISCOUNT_PERCENT,$data);
    }
    /**
     * @inheritDoc
     */
    public function getOriginalPrice(){
        return $this->_get(self::ORIGINAL_PRICE);
    }

    /**
     * @inheritDoc
     */
    public function setOriginalPrice($data){
        return $this->setData(self::ORIGINAL_PRICE,$data);
    }

    /**
     * @inheritDoc
     */
    public function getGtmData(){
        return $this->_get(self::GTM_DATA);
    }

    /**
     * @inheritDoc
     */
    public function setGtmData($data){
        return $this->setData(self::GTM_DATA,$data);
    }
}
