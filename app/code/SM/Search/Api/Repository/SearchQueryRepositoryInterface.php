<?php

declare(strict_types=1);

namespace SM\Search\Api\Repository;

use Magento\Framework\Api\SearchCriteriaInterface;
use SM\Search\Api\Data\Response\SearchQuerySearchResultsInterface;

interface SearchQueryRepositoryInterface
{
    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \SM\Search\Api\Data\Response\SearchQuerySearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchQuerySearchResultsInterface;

    /**
     * @return \SM\Search\Api\Entity\SearchQueryCategoryInterface[]
     */
    public function getPopular(): array;
}
