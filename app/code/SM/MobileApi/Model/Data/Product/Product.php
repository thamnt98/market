<?php

namespace SM\MobileApi\Model\Data\Product;

/**
 * Class for storing category assigned products
 */
class Product extends \Magento\Framework\Model\AbstractExtensibleModel implements \SM\MobileApi\Api\Data\Product\ProductInterface
{
    public function getProduct()
    {
        return $this->getData(self::PRODUCT);
    }

    public function setProduct($data)
    {
        return $this->setData(self::PRODUCT, $data);
    }
}
