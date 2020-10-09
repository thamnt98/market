<?php

namespace SM\Category\Model;

use SM\Category\Api\CategoryInterface;
use SM\Category\Model\Catalog\Tree;

/**
 * Class Catalog
 * @package SM\Category\Model
 */
class Catalog implements CategoryInterface
{
    protected $tree;

    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    public function getCategoryTree()
    {
        return $this->tree->getParentCategories();
    }

    public function getSubCategory($category_id)
    {
        return $this->tree->getSubCategoryById($category_id);
    }

    public function getMostPopularProduct($categoryId)
    {
        return $this->tree->getMostPopular($categoryId);
    }

    public function getFavoriteBrands()
    {
        return $this->tree->getFavoriteBrand();
    }

    public function getCategoryMetaData($category_id){
        return $this->tree->getCategoryMetaData($category_id);
    }
}
