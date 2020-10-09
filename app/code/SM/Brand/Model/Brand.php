<?php

namespace SM\Brand\Model;

use Magento\Framework\Api\AbstractExtensibleObject;
use SM\Brand\Api\Data\BrandInterface;

class Brand extends AbstractExtensibleObject implements BrandInterface
{

    public function getCategories()
    {
        return $this->_get(self::CATEGORIES);
    }

    public function getMostPopular()
    {
        return $this->_get(self::MOST_POPULAR);
    }

    public function setCategories($data)
    {
        return $this->setData(self::CATEGORIES, $data);
    }

    public function setMostPopular($data)
    {
        return $this->setData(self::MOST_POPULAR, $data);
    }

    public function getBanner()
    {
        return $this->_get(self::BANNER);
    }

    public function setBanner($data)
    {
        return $this->setData(self::BANNER, $data);
    }
}
