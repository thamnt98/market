<?php

namespace SM\MobileApi\Model\Data\Store\Setting;

/**
 * Class ProductAttribute
 * @package SM\MobileApi\Model\Data\Store
 */
class ProductAttribute extends \Magento\Framework\Model\AbstractExtensibleModel implements \SM\MobileApi\Api\Data\Store\Setting\ProductAttributeInterface
{
    public function getLabel()
    {
        return $this->getData(self::LABEL);
    }

    public function setLabel($value)
    {
        return $this->setData(self::LABEL, $value);
    }

    public function getValue()
    {
        return $this->getData(self::VALUE);
    }

    public function setValue($value)
    {
        return $this->setData(self::VALUE, $value);
    }
}
