<?php

namespace SM\StoreLocator\Model\Repository;

use Exception;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use SM\StoreLocator\Api\Data\Request\StoreSearchCriteriaInterface;
use SM\StoreLocator\Api\Data\Response\StoreSearchResultsInterface;
use SM\StoreLocator\Api\Data\Response\StoreSearchResultsInterfaceFactory;
use SM\StoreLocator\Api\StoreLocationRepositoryInterface;
use SM\StoreLocator\Model\Data\Request\StoreSearchCriteria\SortOrder;
use SM\StoreLocator\Model\Store\Converter;
use SM\StoreLocator\Model\Store\Location;
use SM\StoreLocator\Model\Store\LocationFactory;
use SM\StoreLocator\Model\Store\ResourceModel\Location as ResourceLocation;
use SM\StoreLocator\Model\Store\ResourceModel\Location\Collection as LocationCollection;
use SM\StoreLocator\Model\Store\ResourceModel\Location\CollectionFactory  as LocationCollectionFactory;
use SM\StoreLocator\Model\Store\SearchCriteria\Resolver as SearchCriteriaResolver;
use SM\StoreLocator\Model\Store\Sorter;

class StoreLocationRepository implements StoreLocationRepositoryInterface
{
    const STORE_ID = 'place_id';
    const STORE_CODE = 'store_code';
    const STORE_STATUS = 'status';

    /**
     * @var LocationCollectionFactory
     */
    private $locationCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessorInterface;

    /**
     * @var Converter
     */
    private $converter;

    /**
     * @var StoreSearchResultsInterfaceFactory
     */
    private $storeSearchResultsFactory;

    /**
     * @var LocationFactory
     */
    private $locationFactory;

    /**
     * @var ResourceLocation
     */
    private $locationResource;

    private $cachedPosById;

    /**
     * @var SearchCriteriaResolver
     */
    private $searchCriteriaResolver;

    /**
     * @var Sorter
     */
    private $sorter;

    public function __construct(
        LocationCollectionFactory $locationCollectionFactory,
        CollectionProcessorInterface $collectionProcessorInterface,
        StoreSearchResultsInterfaceFactory $storeSearchResultsFactory,
        Converter $converter,
        LocationFactory $locationFactory,
        ResourceLocation $locationResource,
        SearchCriteriaResolver $searchCriteriaResolver,
        Sorter $sorter
    ) {
        $this->locationCollectionFactory = $locationCollectionFactory;
        $this->collectionProcessorInterface = $collectionProcessorInterface;
        $this->storeSearchResultsFactory = $storeSearchResultsFactory;
        $this->converter = $converter;
        $this->locationFactory = $locationFactory;
        $this->locationResource = $locationResource;
        $this->searchCriteriaResolver = $searchCriteriaResolver;
        $this->sorter = $sorter;
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
    }

    /**
     * {@inheritdoc}
     * @throws LocalizedException
     */
    public function getList(StoreSearchCriteriaInterface $searchCriteria): StoreSearchResultsInterface
    {
        /** @var LocationCollection $collection */
        $collection = $this->locationCollectionFactory->create();

        $collection->addFieldToFilter(self::STORE_STATUS, '1');

        $this->searchCriteriaResolver->resolveSearchKeyword($searchCriteria);
        $sortDistance = $this->searchCriteriaResolver->extractSortDistance($searchCriteria);

        $this->collectionProcessorInterface->process($searchCriteria, $collection);

        /** @var StoreSearchResultsInterface $searchResults */
        $searchResults = $this->storeSearchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);


        $items = $this->converter->convertItems($collection->getItems());

        if (isset($sortDistance[SortOrder::LAT]) && isset($sortDistance[SortOrder::LONG])) {
            $items = $this->sorter->sortByDistance(
                $sortDistance[SortOrder::LAT],
                $sortDistance[SortOrder::LONG],
                $items,
                $sortDistance[SortOrder::DIRECTION]
            );
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount(count($items));
        return $searchResults;
    }

    /**
     * @param StoreSearchCriteriaInterface $searchCriteria
     * @return \SM\StoreLocator\Api\Data\Response\StoreSearchLittleInfoResultsInterface|StoreSearchResultsInterface
     * @throws LocalizedException
     */
    public function getListStores(StoreSearchCriteriaInterface $searchCriteria)
    {
        return $this->getList($searchCriteria);
    }

    /**
     * @param $id
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getStoreById($id)
    {
        if (!isset($this->cachedPosById[$id])) {
            /** @var Location $pointOfSale */
            $pointOfSale = $this->locationFactory->create();
            $this->locationResource->load($pointOfSale, $id);

            if (!$pointOfSale->getId()) {
                throw new NoSuchEntityException(__('Store with id "%1" does not exist.', $id));
            }

            $this->cachedPosById[$id] = $pointOfSale;
        }

        return $this->cachedPosById[$id];
    }

    /**
     * @param $id
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteStoreById($id)
    {
        $location = $this->locationFactory->create();
        $this->locationResource->load($location, $id);
        if (!$location->getId()) {
            throw new NoSuchEntityException(__('Store with id "%1" does not exist.', $id));
        }
        try {
            $this->locationResource->delete($location);
        } catch (Exception $e) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        }
    }
}
