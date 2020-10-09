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
     * @param int $customerId
     * @param string $keyword
     * @param int $p
     * @param int $limit
     * @param int $cat
     * @return \SM\MobileApi\Api\Data\Product\ListInterface
     */
    public function searchV2(int $customerId, string $keyword, int $p = 1, int $limit = 12, $cat = null);

    /**
     * @param int $customerId
     * @return \SM\MobileApi\Api\Data\Product\SearchInterface
     */
    public function getRecommendationProducts(int $customerId);

    /**
     * @param string $keyword
     * @param int $categoryId
     * @return \SM\MobileApi\Api\Data\Product\SearchInterface
     */
    public function getSearchSuggestion(string $keyword, $categoryId = null);

    /**
     * @param int $customerId
     * @param string $barcode
     * @return \SM\MobileApi\Api\Data\Product\SearchInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function searchProductByBarcode(int $customerId, string $barcode);
}
