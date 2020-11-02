<?php

namespace SM\Category\Api;

/**
 * Interface for managing category information
 */
interface CategoryInterface
{
    /**
     * Get all C0 Category without product
     * @return \SM\Category\Api\Data\Catalog\CategoryTreeInterface
     */
    public function getCategoryTree();

    /**
     * Get children category
     *
     * @param int $category_id
     * @return \SM\Category\Api\Data\Catalog\CategoryInterface[]
     */
    public function getSubCategory($category_id);

    /**
     * @param int $category_id
     * @return \SM\MobileApi\Api\Data\Product\ListInterface|array
     */
    public function getMostPopularProduct($category_id);

    /**
     * Get category gallery and color
     * @param int $category_id
     * @return \SM\Category\Api\Data\Catalog\CategoryMetaDataInterface
     */
    public function getCategoryMetaData($category_id);
}
