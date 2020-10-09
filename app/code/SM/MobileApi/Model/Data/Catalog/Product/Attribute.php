<?php

namespace SM\MobileApi\Model\Data\Catalog\Product;

/**
 * Class for storing attribute information
 */
class Attribute extends \Magento\Framework\Model\AbstractExtensibleModel implements \SM\MobileApi\Api\Data\Catalog\Product\AttributeInterface
{
    public function getLabel()
    {
        return $this->getData(self::LABEL);
    }

    public function setLabel($label)
    {
        return $this->setData(self::LABEL, $label);
    }

    public function getValue()
    {
        return $this->getData(self::VALUE);
    }

    public function setValue($value)
    {
        return $this->setData(self::VALUE, $value);
    }

    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    public function setCode($code)
    {
        return $this->setData(self::CODE, $code);
    }
}
