<?php

declare(strict_types=1);

namespace SM\Search\Api\Repository;

use Magento\Framework\Api\SearchCriteriaInterface;
use SM\Search\Api\Data\Response\SearchQueryPersonalSearchResultsInterface;

interface SearchQueryPersonalRepositoryInterface
{
    /**
     * @param array $entityData
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveEntity(array $entityData): void;

    /**
     * @param int $customerId
     * @param string $queryText
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteOne(int $customerId, string $queryText): void;

    /**
     * @param int $customerId
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteAll(int $customerId): void;

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \SM\Search\Api\Data\Response\SearchQueryPersonalSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchQueryPersonalSearchResultsInterface;

    /**
     * @return \SM\Search\Api\Entity\SearchQueryPersonalInterface[]
     */
    public function getLatest(): array;
}
