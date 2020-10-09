<?php

namespace SM\MobileApi\Model\Data\Catalog;

class ProductFilterItem extends \Magento\Framework\Model\AbstractExtensibleModel implements \SM\MobileApi\Api\Data\Catalog\ProductFilterItemInterface
{
    public function getCount()
    {
        return $this->getData(self::COUNT);
    }

    public function setCount($data)
    {
        return $this->setData(self::COUNT, $data);
    }

    public function getLabel()
    {
        return $this->getData(self::LABEL);
    }

    public function setLabel($data)
    {
        return $this->setData(self::LABEL, $data);
    }

    public function getValue()
    {
        return $this->getData(self::VALUE);
    }

    public function setValue($data)
    {
        return $this->setData(self::VALUE, $data);
    }
}
