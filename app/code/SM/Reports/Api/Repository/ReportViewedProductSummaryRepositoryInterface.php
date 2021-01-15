<?php

declare(strict_types=1);

namespace SM\Reports\Api\Repository;

use Magento\Framework\Api\SearchCriteriaInterface;
use SM\Reports\Api\Data\Response\ReportViewedProductSummarySearchResultsInterface;

interface ReportViewedProductSummaryRepositoryInterface
{
    /**
     * @param array $entityData
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveEntity(array $entityData): void;

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \SM\Reports\Api\Data\Response\ReportViewedProductSummarySearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): ReportViewedProductSummarySearchResultsInterface;

    /**
     * @param int $customerId
     * @return \SM\Reports\Api\Data\Response\ReportViewedProductSummarySearchResultsInterface
     */
    public function getRecommendationProducts(int $customerId): ReportViewedProductSummarySearchResultsInterface;

    /**
     * @param int $customerId
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getRecommendationCollection(int $customerId);
}
