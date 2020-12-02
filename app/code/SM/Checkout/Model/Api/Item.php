<?php

namespace SM\Checkout\Model\Api;

use SM\Checkout\Api\Data\Checkout\QuoteItems\ItemInterface;

class Item extends \Magento\Framework\Api\AbstractExtensibleObject implements \SM\Checkout\Api\Data\Checkout\QuoteItems\ItemInterface
{
    const ITEM_ID = 'item_id';
    const SKU = 'sku';
    const NAME = 'name';
    const URL = 'url';
    const WEIGHT = 'weight';
    const WEIGHT_UNIT = 'weight_unit';
    const QTY = 'qty';
    const THUMBNAIL = 'thumbnail';
    const ROW_TOTAL = 'row_total';
    const BASE_ROW_TOTAL_BY_LOCATION = 'base_row_total_by_location';
    const CURRENCY_SYMBOL = 'currency_symbol';
    const PRODUCT_TYPE = 'product_type';
    const PRODUCT_OPTION = 'product_option';
    const SHIPPING_METHOD = 'shipping_method';
    const SHIPPING_ADDRESS_ID = 'shipping_address_id';
    const SHIPPING_METHOD_SELECTED = 'shipping_method_selected';
    const ADDITIONAL_INFO = 'additional_info';
    const DISABLE = 'disable';
    const MESSAGE = 'message';
    const FRESH_PRODUCT = "fresh_product";
    const DISABLE_STORE_PICK_UP = "disable_store_pick_up";
    const GTM_DATA = 'gtm_data';
    /**
     * {@inheritdoc}
     */
    public function setItemId($itemId)
    {
        return $this->setData(self::ITEM_ID, $itemId);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemId()
    {
        return $this->_get(self::ITEM_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getSku()
    {
        return $this->_get(self::SKU);
    }

    /**
     * {@inheritdoc}
     */
    public function setSku($sku)
    {
        return $this->setData(self::SKU, $sku);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($label)
    {
        return $this->setData(self::NAME, $label);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setUrl($url)
    {
        return $this->setData(self::URL, $url);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->_get(self::URL);
    }

    /**
     * {@inheritdoc}
     */
    public function setWeight($weight)
    {
        return $this->setData(self::WEIGHT, $weight);
    }

    /**
     * {@inheritdoc}
     */
    public function getWeight()
    {
        return $this->_get(self::WEIGHT);
    }

    /**
     * {@inheritdoc}
     */
    public function setWeightUnit($weightUnit)
    {
        return $this->setData(self::WEIGHT_UNIT, $weightUnit);
    }

    /**
     * {@inheritdoc}
     */
    public function getWeightUnit()
    {
        return $this->_get(self::WEIGHT_UNIT);
    }

    /**
     * {@inheritdoc}
     */
    public function setQty($qty)
    {
        return $this->setData(self::QTY, $qty);
    }

    /**
     * {@inheritdoc}
     */
    public function getQty()
    {
        return $this->_get(self::QTY);
    }

    /**
     * {@inheritdoc}
     */
    public function setThumbnail($thumbnail)
    {
        return $this->setData(self::THUMBNAIL, $thumbnail);
    }

    /**
     * {@inheritdoc}
     */
    public function getThumbnail()
    {
        return $this->_get(self::THUMBNAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setRowTotal($rowTotal)
    {
        return $this->setData(self::ROW_TOTAL, $rowTotal);
    }

    /**
     * {@inheritdoc}
     */
    public function getRowTotal()
    {
        return $this->_get(self::ROW_TOTAL);
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseRowTotalByLocation($baseRowTotalByLocation)
    {
        return $this->setData(self::BASE_ROW_TOTAL_BY_LOCATION, $baseRowTotalByLocation);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseRowTotalByLocation()
    {
        return $this->_get(self::BASE_ROW_TOTAL_BY_LOCATION);
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrencySymbol($symbol)
    {
        return $this->setData(self::CURRENCY_SYMBOL, $symbol);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrencySymbol()
    {
        return $this->_get(self::CURRENCY_SYMBOL);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductType($productType)
    {
        return $this->setData(self::PRODUCT_TYPE, $productType);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductType()
    {
        return $this->_get(self::PRODUCT_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductOption($productOption)
    {
        return $this->setData(self::PRODUCT_OPTION, $productOption);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductOption()
    {
        return $this->_get(self::PRODUCT_OPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingMethod($data)
    {
        return $this->setData(self::SHIPPING_METHOD, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingMethod()
    {
        return $this->_get(self::SHIPPING_METHOD);
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingAddressId($addressId)
    {
        return $this->setData(self::SHIPPING_ADDRESS_ID, $addressId);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingAddressId()
    {
        return $this->_get(self::SHIPPING_ADDRESS_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingMethodSelected($methodCode)
    {
        return $this->setData(self::SHIPPING_METHOD_SELECTED, $methodCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingMethodSelected()
    {
        return $this->_get(self::SHIPPING_METHOD_SELECTED);
    }

    /**
     * {@inheritdoc}
     */
    public function setAdditionalInfo($additionalInfo)
    {
        return $this->setData(self::ADDITIONAL_INFO, $additionalInfo);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdditionalInfo()
    {
        return $this->_get(self::ADDITIONAL_INFO);
    }

    /**
     * {@inheritdoc}
     */
    public function setDisable($disable)
    {
        return $this->setData(self::DISABLE, $disable);
    }

    /**
     * {@inheritdoc}
     */
    public function getDisable()
    {
        return $this->_get(self::DISABLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setMessage($message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->_get(self::MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function getGtmData()
    {
        return $this->_get(self::GTM_DATA);
    }

    /**
     * {@inheritdoc}
     */
    public function setGtmData($value)
    {
        return $this->setData(self::GTM_DATA, $value);
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
    public function getFreshProduct()
    {
        return $this->_get(self::FRESH_PRODUCT);
    }

    /**
     * @inheritDoc
     */
    public function setDisableStorePickUp($value)
    {
        return $this->setData(self::DISABLE_STORE_PICK_UP, $value);
    }

    /**
     * @inheritDoc
     */
    public function getDisableStorePickUp()
    {
        return $this->_get(self::DISABLE_STORE_PICK_UP);
    }
}
