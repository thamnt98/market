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
    /**
     * @var Tree
     */
    protected $tree;

    /**
     * Catalog constructor.
     * @param Tree $tree
     */
    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
    }

    /**
     * @inheritDoc
     */
    public function getCategoryTree()
    {
        return $this->tree->getTree();
    }

    /**
     * @inheritDoc
     */
    public function getSubCategory($category_id)
    {
        return $this->tree->getSubCategoryById($category_id);
    }

    /**
     * @inheritDoc
     */
    public function getMostPopularProduct($categoryId)
    {
        return $this->tree->getMostPopular($categoryId);
    }

    /**
     * @inheritDoc
     */
    public function getCategoryMetaData($category_id)
    {
        return $this->tree->getCategoryMetaData($category_id);
    }
}
