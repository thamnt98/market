<?php


namespace SM\Sales\Model\Data;


use Magento\Framework\DataObject;
use SM\Sales\Api\Data\DetailItemDataInterface;
use SM\Sales\Api\Data\DetailItemInterface;

class DetailItemData extends DataObject implements \SM\Sales\Api\Data\DetailItemDataInterface
{

    /**
     * @inheritDoc
     */
    public function getProductName()
    {
        return $this->getData(self::PRODUCT_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setProductName($value)
    {
        return $this->setData(self::PRODUCT_NAME, $value);
    }

    /**
     * @inheritDoc
     */
    public function getQuantity()
    {
        return $this->getData(self::QUANTITY);
    }

    /**
     * @inheritDoc
     */
    public function setQuantity($value)
    {
        return $this->setData(self::QUANTITY, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPrice()
    {
        return $this->getData(self::PRICE);
    }

    /**
     * @inheritDoc
     */
    public function setPrice($value)
    {
        return $this->setData(self::PRICE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getImageUrl()
    {
        return $this->getData(self::IMAGE_URL);
    }

    /**
     * @inheritDoc
     */
    public function setImageUrl($value)
    {
        return $this->setData(self::IMAGE_URL, $value);
    }

    /**
     * @inheritDoc
     */
    public function getUrl()
    {
        return $this->getData(self::URL);
    }

    /**
     * @inheritDoc
     */
    public function setUrl($value)
    {
        return $this->setData(self::URL, $value);
    }

    /**
     * @inheritDoc
     */
    public function getSku()
    {
        return $this->getData(self::SKU);
    }

    /**
     * @inheritDoc
     */
    public function setSku($value)
    {
        return $this->setData(self::SKU, $value);
    }

    /**
     * @inheritDoc
     */
    public function setTotal($value)
    {
        return $this->setData(self::TOTAL, $value);
    }

    /**
     * @inheritDoc
     */
    public function getTotal()
    {
        return $this->getData(self::TOTAL);
    }

    /**
     * @inheritDoc
     */
    public function setItemId($value)
    {
        return $this->setData(self::ITEM_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getItemId()
    {
        return $this->getData(self::ITEM_ID);
    }

    /**
     * @inheritDoc
     */
    public function getOptions()
    {
        return $this->getData(self::OPTIONS);
    }

    /**
     * @inheritDoc
     */
    public function setOptions($value)
    {
        return $this->setData(self::OPTIONS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getHasOptions()
    {
        return $this->getData(self::HAS_OPTIONS);
    }

    /**
     * @inheritDoc
     */
    public function setHasOptions($value)
    {
        return $this->setData(self::HAS_OPTIONS, $value);
    }

    /**
     * @inheritDoc
     */
    public function setInstallationService($data)
    {
        return $this->setData(\SM\Installation\Helper\Data::QUOTE_OPTION_KEY, $data);
    }

    /**
     * @inheritDoc
     */
    public function getInstallationService()
    {
        return $this->getData(\SM\Installation\Helper\Data::QUOTE_OPTION_KEY);
    }

    /**
     * @inheritDoc
     */
    public function setBuyRequest($value)
    {
        return $this->setData(self::BUY_REQUEST, $value);
    }

    /**
     * @inheritDoc
     */
    public function getBuyRequest()
    {
        return $this->getData(self::BUY_REQUEST);
    }

    /**
     * @inheritDoc
     */
    public function getProductOption()
    {
        return $this->getData(self::PRODUCT_OPTION);
    }

    /**
     * @inheritDoc
     */
    public function setProductOption($value)
    {
        return $this->setData(self::PRODUCT_OPTION, $value);
    }

    /**
     * @inheritDoc
     */
    public function getProductType()
    {
        return $this->getData(self::PRODUCT_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setProductType($value)
    {
        return $this->setData(self::PRODUCT_TYPE, $value);
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
        return $this->getData(self::FRESH_PRODUCT);
    }


    /**
     * @inheritDoc
     */
    public function setIsAvailable($value)
    {
        return $this->setData(self::IS_AVAILABLE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsAvailable()
    {
        return $this->getData(self::IS_AVAILABLE);
    }
}
