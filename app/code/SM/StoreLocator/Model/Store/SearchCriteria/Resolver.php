<?php

//declare(strict_types=1);

namespace SM\StoreLocator\Model\Store\SearchCriteria;

use SM\StoreLocator\Api\Data\Request\StoreSearchCriteriaInterface;
use SM\StoreLocator\Api\Data\StoreInterface;
use SM\StoreLocator\Model\Data\Request\StoreSearchCriteria\SortOrder;
use SM\StoreLocator\Model\Store\Sorter;
use SM\StoreLocator\Model\Store\ResourceModel\Location as ResourceLocation;
use Magento\Framework\Exception\LocalizedException;

class Resolver
{
    const KEYWORD = 'keyword';

    /**
     * @var ResourceLocation
     */
    protected $resourceLocation;

    /**
     * StoreRepository constructor.
     * @param ResourceLocation $resourceLocation
     * @codeCoverageIgnore
     */
    public function __construct(
        ResourceLocation $resourceLocation
    ) {
        $this->resourceLocation = $resourceLocation;
    }

    /**
     * @param StoreSearchCriteriaInterface $searchCriteria
     * @return array
     */
    public function extractSortDistance(StoreSearchCriteriaInterface $searchCriteria): array
    {
        $sortOrders = $searchCriteria->getSortOrders();
        $sortDistance = [];

        foreach ($sortOrders as $key => $sortOrder) {
            if ($sortOrder->getField() == Sorter::DISTANCE) {
                $sortDistance[SortOrder::LAT] = $sortOrder->getLat();
                $sortDistance[SortOrder::LONG] = $sortOrder->getLong();
                $sortDistance[SortOrder::DIRECTION] = $sortOrder->getDirection() ?? SortOrder::SORT_ASC;

                unset($sortOrders[$key]);
            }
        }

        $searchCriteria->setSortOrders($sortOrders);

        return $sortDistance;
    }
    /**
     * @param \SM\StoreLocator\Api\Data\Request\StoreSearchCriteriaInterface $searchCriteria
     * @throws LocalizedException
     */
    public function resolveSearchKeyword(StoreSearchCriteriaInterface $searchCriteria): void
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() == self::KEYWORD && !empty($filter->getValue())) {
                    //Get all store_code match with full text search
                    $storeCodes = $this->resourceLocation->searchStoresByKeyWord($filter->getValue());

                    // renew filter
                    $filter->setField(StoreInterface::ID);
                    $filter->setConditionType('in');
                    $filter->setValue(implode(",", array_unique($storeCodes)));
                }
            }
        }
    }

}
