<?php


namespace SM\Category\Model\Data\Catalog;

use SM\Category\Api\Data\Catalog\CategoryColorInterface;

/**
 * Class for storing category color
 * @package SM\Category\Model\Data\Catalog
 */
class CategoryColor extends \Magento\Framework\Model\AbstractExtensibleModel implements CategoryColorInterface
{

    public function getCategoryColor()
    {
        return $this->getData(self::CATEGORY_COLOR);
    }

    public function setCategoryColor($color)
    {
        return $this->setData(self::CATEGORY_COLOR, $color);
    }

    public function getMostPopularColor()
    {
        return $this->getData(self::MOST_POPULAR_COLOR);
    }

    public function setMostPopularColor($color)
    {
        return $this->setData(self::MOST_POPULAR_COLOR, $color);
    }

    public function getFavoriteBrandColor()
    {
        return $this->getData(self::FAVORITE_BRAND_COLOR);
    }

    public function setFavoriteBrandColor($color)
    {
        return $this->setData(self::FAVORITE_BRAND_COLOR, $color);
    }

    public function getProductColor()
    {
        return $this->getData(self::PRODUCT_CATEGORY_COLOR);
    }

    public function setProductColor($color)
    {
        return $this->setData(self::PRODUCT_CATEGORY_COLOR, $color);
    }
}
