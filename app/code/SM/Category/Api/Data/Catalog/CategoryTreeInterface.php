<?php

namespace SM\Category\Api\Data\Catalog;

/**
 * Interface for storing category tree
 */
interface CategoryTreeInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const CATEGORIES = 'categories';

    /**
     * Get Category Tree
     *
     * @return \SM\Category\Api\Data\Catalog\CategoryInterface[]
     */
    public function getCategories();

    /**
     * Set Category Tree
     *
     * @param \SM\Category\Api\Data\Catalog\CategoryInterfaceCategoryInterface[]
     *
     * @return $this
     */
    public function setCategories($data);
}
