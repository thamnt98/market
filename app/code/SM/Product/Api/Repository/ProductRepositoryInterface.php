<?php

declare(strict_types=1);

namespace SM\Product\Api\Repository;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;

interface ProductRepositoryInterface
{
    const ENTITY_ID = 'entity_id';

    /**
     * @param int $customerId
     * @param string $sku
     * @param bool $editMode
     * @param int|null $storeId
     * @param bool $forceReload
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(int $customerId, string $sku, bool $editMode = false, ?int $storeId = null, bool $forceReload = false): ProductInterface;

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Catalog\Api\Data\ProductSearchResultsInterface
     */
    public function getStoreFrontList(SearchCriteriaInterface $searchCriteria): ProductSearchResultsInterface;

    /**
     * @param int[] $productIds
     * @param string $sortBy
     * @param string $direction
     * @param int $limit
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     */
    public function getStoreFrontProductByIds(array $productIds, $limit = null, $sortBy = self::ENTITY_ID, $direction = SortOrder::SORT_ASC): array;

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function generateCollection();
    
    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function generateMobileCollection();
}
