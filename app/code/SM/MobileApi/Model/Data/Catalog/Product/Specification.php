<?php

namespace SM\MobileApi\Model\Data\Catalog\Product;

use Magento\Framework\Model\AbstractExtensibleModel;
use SM\MobileApi\Api\Data\Catalog\Product\SpecificationsInterface;

/**
 * Class for storing delivery into information
 * @package SM\MobileApi\Model\Data\Catalog\Product
 */
class Specification extends AbstractExtensibleModel implements \SM\MobileApi\Api\Data\Catalog\Product\SpecificationsInterface
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

    /**
     * @inheritDoc
     */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * @inheritDoc
     */
    public function setCode($value)
    {
        return $this->setData(self::CODE, $value);
    }
}
