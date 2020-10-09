<?php

namespace SM\MobileApi\Model\Data\Catalog\Product\Option;

class Value extends \Magento\Framework\Model\AbstractExtensibleModel implements \SM\MobileApi\Api\Data\Catalog\Product\Option\ValueInterface
{
    public function getValueId()
    {
        return $this->getData(self::VALUE_ID);
    }

    public function setValueId($data)
    {
        return $this->setData(self::VALUE_ID, $data);
    }

    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    public function setTitle($data)
    {
        return $this->setData(self::TITLE, $data);
    }

    public function getPrice()
    {
        return $this->getData(self::PRICE);
    }

    public function setPrice($data)
    {
        return $this->setData(self::PRICE, $data);
    }

    public function getPriceType()
    {
        return $this->getData(self::PRICE_TYPE);
    }

    public function setPriceType($data)
    {
        return $this->setData(self::PRICE_TYPE, $data);
    }

    public function getSku()
    {
        return $this->getData(self::SKU);
    }

    public function setSku($data)
    {
        return $this->setData(self::SKU, $data);
    }

    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    public function setSortOrder($data)
    {
        return $this->setData(self::SORT_ORDER, $data);
    }
}
