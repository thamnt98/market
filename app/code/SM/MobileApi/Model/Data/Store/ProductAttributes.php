<?php

namespace SM\MobileApi\Model\Data\Store;

use SM\MobileApi\Api\Data\Store\ProductAttributesInterface;

/**
 * Class ProductAttributes
 * @package SM\MobileApi\Model\Data\Store
 */
class ProductAttributes extends \Magento\Framework\Model\AbstractExtensibleModel implements ProductAttributesInterface
{
    public function getAttributes()
    {
        return $this->getData(self::ATTRIBUTES);
    }

    public function setAttributes($values)
    {
        return $this->setData(self::ATTRIBUTES, $values);
    }
}
