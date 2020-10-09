<?php

namespace SM\MobileApi\Model\Data\Product;

/**
 * Class for storing category assigned products
 */
class Search extends \Magento\Framework\Model\AbstractExtensibleModel implements \SM\MobileApi\Api\Data\Product\SearchInterface
{
    public function getSearchType()
    {
        return $this->getData(self::SEARCH_TYPE);
    }

    public function setSearchType($type)
    {
        return $this->setData(self::SEARCH_TYPE, $type);
    }

    public function getSuggestKeyword()
    {
        return $this->getData(self::SUGGEST_KEYWORD);
    }

    public function setSuggestKeyword($keyword)
    {
        return $this->setData(self::SUGGEST_KEYWORD, $keyword);
    }

    public function getProducts()
    {
        return $this->getData(self::PRODUCTS);
    }

    public function setProducts($data)
    {
        return $this->setData(self::PRODUCTS, $data);
    }

    public function getTotal()
    {
        return $this->getData(self::TOTAL);
    }

    public function setTotal($total)
    {
        return $this->setData(self::TOTAL, $total);
    }
}
