<?php

declare(strict_types=1);

namespace SM\Search\Model\Repository;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use SM\Search\Api\Data\Response\SearchQueryPersonalSearchResultsInterface;
use SM\Search\Api\Data\Response\SearchQueryPersonalSearchResultsInterfaceFactory;
use SM\Search\Api\Entity\SearchQueryPersonalInterface;
use SM\Search\Api\Repository\SearchQueryPersonalRepositoryInterface;
use SM\Search\Helper\Config;
use SM\Search\Model\ResourceModel\SearchQueryPersonal as ResourceModel;
use SM\Search\Model\ResourceModel\SearchQueryPersonal\Collection;
use SM\Search\Model\ResourceModel\SearchQueryPersonal\CollectionFactory;

class SearchQueryPersonalRepository implements SearchQueryPersonalRepositoryInterface
{
    /**
     * @var ResourceModel
     */
    protected $resourceModel;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    protected $sortOrderBuilder;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var SearchQueryPersonalSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Config
     */
    protected $config;

    /**
     * SearchQueryPersonalRepository constructor.
     * @param ResourceModel $resourceModel
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param CollectionFactory $collectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SearchQueryPersonalSearchResultsInterfaceFactory $searchResultsFactory
     * @param StoreManagerInterface $storeManager
     * @param Config $config
     */
    public function __construct(
        ResourceModel $resourceModel,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        SearchQueryPersonalSearchResultsInterfaceFactory $searchResultsFactory,
        StoreManagerInterface $storeManager,
        Config $config
    ) {
        $this->resourceModel = $resourceModel;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->storeManager = $storeManager;
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function saveEntity(array $entityData): void
    {
        $this->resourceModel->saveEntityData($entityData);
    }

    /**
     * @inheritDoc
     */
    public function deleteOne(int $customerId, string $queryText): void
    {
        $this->resourceModel->deleteOne($customerId, $queryText);
    }

    /**
     * @inheritDoc
     */
    public function deleteAll(int $customerId): void
    {
        $this->resourceModel->deleteAll($customerId);
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchQueryPersonalSearchResultsInterface
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var SearchQueryPersonalSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());

        return $searchResults;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function getLatest(int $customerId): array
    {
        $sortByPosition = $this->sortOrderBuilder
        ->setField(SearchQueryPersonalInterface::UPDATED_AT)
        ->setDescendingDirection()
        ->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(Config::STORE_ID_ATTRIBUTE_CODE, $this->storeManager->getStore()->getId())
            ->addFilter(SearchQueryPersonalInterface::CUSTOMER_ID, $customerId)
            ->setSortOrders([$sortByPosition])
            ->setPageSize($this->config->getLatestSearchSize())
            ->create();

        $searchResult = $this->getList($searchCriteria);

        return $searchResult->getItems();
    }
}
