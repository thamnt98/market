<?php

declare(strict_types=1);

namespace SM\Search\Api;

use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use SM\Search\Api\Data\Response\SearchResultInterface;
use SM\Search\Api\Data\Response\SuggestionSearchResultInterface;

interface SearchInterface
{
    /**
     * @param int $customerId
     * @param \Magento\Framework\Api\Search\SearchCriteriaInterface $searchCriteria
     * @return \SM\Search\Api\Data\Response\SearchResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function search(int $customerId, SearchCriteriaInterface $searchCriteria): SearchResultInterface;

    /**
     * @param \Magento\Framework\Api\Search\SearchCriteriaInterface $searchCriteria
     * @return \SM\Search\Api\Data\Response\SuggestionSearchResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function suggest(SearchCriteriaInterface $searchCriteria): SuggestionSearchResultInterface;

    /**
     * @param string $suggestKeyword
     * @param int $catId
     * @return SuggestionSearchResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function suggestByKeyword(string $suggestKeyword, int $catId, $pageSize = null, $currentPage = null): SuggestionSearchResultInterface;

    /**
     * @param int $p
     * @param int $limit
     * @return \SM\MobileApi\Api\Data\Product\ListInterface
     * @throws LocalizedException
     */
    public function getSuggestProductOnNoResult($p = 1, $limit = 12);
}
