<?php

namespace SM\InspireMe\Model\Data;

use Magento\Framework\Model\AbstractModel;

class RelatedProductResult extends AbstractModel implements \SM\InspireMe\Api\Data\RelatedProductResultInterface
{
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    public function getProducts()
    {
        return $this->getData(self::PRODUCTS);
    }

    public function setProducts($products)
    {
        return $this->setData(self::PRODUCTS, $products);
    }
}
