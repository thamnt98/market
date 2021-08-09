<?php

namespace SM\RecommendSearchCatalogGraphQl\Api;

/**
 * Interface RecommendProductInterface
 * @package SM\RecommendSearchCatalogGraphQl\Api
 */
interface RecommendProductInterface
{
    public function getCategoryNameByCategoryId(int $categoryId);
}
