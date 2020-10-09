<?php

namespace SM\Category\Api\Data\Catalog;

/**
 * Interface CategoryColorInterface
 * @package SM\Category\Api\Data\Catalog
 */
interface CategoryColorInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const CATEGORY_COLOR = 'category_color';
    const MOST_POPULAR_COLOR = 'most_popular_color';
    const FAVORITE_BRAND_COLOR = 'favorite_brand_color';
    const PRODUCT_CATEGORY_COLOR = 'product_color';

    /**
     * @return string
     */
    public function getCategoryColor();

    /**
     * @param string $color
     * @return $this
     */
    public function setCategoryColor($color);

    /**
     * @return string
     */
    public function getMostPopularColor();

    /**
     * @param string $color
     * @return $this
     */
    public function setMostPopularColor($color);

    /**
     * @return string
     */
    public function getFavoriteBrandColor();

    /**
     * @param string $color
     * @return $this
     */
    public function setFavoriteBrandColor($color);

    /**
     * @return string
     */
    public function getProductColor();

    /**
     * @param string $color
     * @return $this
     */
    public function setProductColor($color);
}
