<?php

declare(strict_types=1);

namespace SM\MobileApi\Api;

interface SearchProductInterface
{
    /**
     * @param int $customerId
     * @param string $keyword
     * @param int $page
     * @param int $size
     * @param int $categoryId
     * @return \SM\MobileApi\Api\Data\Product\SearchInterface
     */
    public function search(int $customerId, string $keyword, int $page = 1, int $size = 12, $categoryId = null);

    /**
     * @param string $keyword
     * @param int $p
     * @param int $limit
     * @return \SM\MobileApi\Api\Data\Product\ListInterface
     */
    public function searchV2(string $keyword, int $p = 1, int $limit = 12);

    /**
     * @return \SM\MobileApi\Api\Data\Product\SearchInterface|array
     */
    public function getRecommendationProducts();

    /**
     * @param string $keyword
     * @param int    $category_id
     *
     * @return \SM\Search\Api\Catalog\SuggestionResultInterface
     */
    public function getSearchSuggestion(string $keyword, $categoryId = null);

    /**
     * @param string $barcode
     * @return \SM\MobileApi\Api\Data\Product\SearchInterface
     */
    public function searchProductByBarcode(string $barcode);
}
