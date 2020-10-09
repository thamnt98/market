<?php

namespace SM\MobileApi\Model\Data\Catalog;

class ProductToolbarOrder extends \Magento\Framework\Model\AbstractExtensibleModel implements \SM\MobileApi\Api\Data\Catalog\ProductToolbarOrderInterface
{
    public function getField()
    {
        return $this->getData(self::FIELD);
    }

    public function setField($data)
    {
        return $this->setData(self::FIELD, $data);
    }

    public function getLabel()
    {
        return $this->getData(self::LABEL);
    }

    public function setLabel($data)
    {
        return $this->setData(self::LABEL, $data);
    }
}
