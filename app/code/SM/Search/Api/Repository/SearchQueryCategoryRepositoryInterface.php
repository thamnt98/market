<?php

declare(strict_types=1);

namespace SM\Search\Api\Repository;

use Magento\Framework\Api\SearchCriteriaInterface;
use SM\Search\Api\Data\Response\SearchQueryCategorySearchResultsInterface;

interface SearchQueryCategoryRepositoryInterface
{
    /**
     * @param array $entityData
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveEntity(array $entityData): void;

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \SM\Search\Api\Data\Response\SearchQueryCategorySearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchQueryCategorySearchResultsInterface;

    /**
     * @param int $categoryId
     * @return \SM\Search\Api\Entity\SearchQueryCategoryInterface[]
     */
    public function getPopular(int $categoryId): array;
}
