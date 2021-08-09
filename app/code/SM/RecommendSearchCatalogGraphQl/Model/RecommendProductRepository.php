<?php

namespace SM\RecommendSearchCatalogGraphQl\Model;

use Magento\Framework\App\ObjectManager;
use SM\RecommendSearchCatalogGraphQl\Api\RecommendProductInterface;

/**
 * Class RecommendProductRepository
 * @package SM\RecommendSearchCatalogGraphQl\Model
 */
class RecommendProductRepository implements RecommendProductInterface
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * RecommendProductRepository constructor.
     */
    public function __construct()
    {
        $this->objectManager = ObjectManager::getInstance();
    }

    /**
     * @param int $categoryId
     * @return mixed
     */
    public function getCategoryNameByCategoryId(int $categoryId)
    {
        $category = $this->objectManager->create('Magento\Catalog\Model\Category')
            ->load($categoryId);
        return $category->getName();
    }
}
