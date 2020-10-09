<?php

namespace SM\MobileApi\Model\Data\Catalog\Product;

/**
 * Class for storing product's Tier-price information
 */
class TierPrice extends \Magento\Framework\Model\AbstractExtensibleModel implements \SM\MobileApi\Api\Data\Catalog\Product\TierPriceInterface
{
    public function getPrice()
    {
        return $this->getData(self::PRICE);
    }

    public function setPrice($price)
    {
        return $this->setData(self::PRICE, $price);
    }

    public function getWebsite()
    {
        return $this->getData(self::WEBSITE);
    }

    public function setWebsite($id)
    {
        return $this->setData(self::WEBSITE, $id);
    }

    public function getQty()
    {
        return $this->getData(self::QTY);
    }

    public function setQty($qty)
    {
        return $this->setData(self::QTY, $qty);
    }

    public function getCustomerGroupId()
    {
        return $this->getData(self::CUSTOMER_GROUP_ID);
    }

    public function setCustomerGroupId($id)
    {
        return $this->setData(self::CUSTOMER_GROUP_ID, $id);
    }

    public function getSavePercent()
    {
        return $this->getData(self::SAVE_PERCENT);
    }

    public function setSavePercent($percent)
    {
        return $this->setData(self::SAVE_PERCENT, $percent);
    }
}
