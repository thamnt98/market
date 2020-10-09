<?php

namespace SM\MobileApi\Model\Data\Catalog\Product;

use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Class for storing delivery into information
 * @package SM\MobileApi\Model\Data\Catalog\Product
 */
class DeliveryInto extends AbstractExtensibleModel implements \SM\MobileApi\Api\Data\Catalog\Product\DeliveryInto
{
    public function getValue()
    {
        return $this->getData(self::VALUE);
    }

    public function setValue($value)
    {
        return $this->setData(self::VALUE, $value);
    }

    public function getLabel()
    {
        return $this->getData(self::LABEL);
    }

    public function setLabel($label)
    {
        return $this->setData(self::LABEL, $label);
    }
}
