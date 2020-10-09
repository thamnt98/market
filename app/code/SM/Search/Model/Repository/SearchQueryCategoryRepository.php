<?php

declare(strict_types=1);

namespace SM\Search\Model\Repository;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use SM\Search\Api\Data\Response\SearchQueryCategorySearchResultsInterface;
use SM\Search\Api\Data\Response\SearchQueryCategorySearchResultsInterfaceFactory;
use SM\Search\Api\Entity\SearchQueryCategoryInterface;
use SM\Search\Api\Repository\SearchQueryCategoryRepositoryInterface;
use SM\Search\Helper\Config;
use SM\Search\Model\ResourceModel\SearchQueryCategory as ResourceModel;
use SM\Search\Model\ResourceModel\SearchQueryCategory\Collection;
use SM\Search\Model\ResourceModel\SearchQueryCategory\CollectionFactory;

class SearchQueryCategoryRepository implements SearchQueryCategoryRepositoryInterface
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
     * @var SearchQueryCategorySearchResultsInterfaceFactory
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
     * SearchQueryCategoryRepository constructor.
     * @param ResourceModel $resourceModel
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param CollectionFactory $collectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SearchQueryCategorySearchResultsInterfaceFactory $searchResultsFactory
     * @param StoreManagerInterface $storeManager
     * @param Config $config
     */
    public function __construct(
        ResourceModel $resourceModel,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        SearchQueryCategorySearchResultsInterfaceFactory $searchResultsFactory,
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
    public function getList(SearchCriteriaInterface $searchCriteria): SearchQueryCategorySearchResultsInterface
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var SearchQueryCategorySearchResultsInterface $searchResults */
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
    public function getPopular(int $categoryId): array
    {
        $sortByCount = $this->sortOrderBuilder
            ->setField(Config::SEARCH_COUNT_FIELD_NAME)
            ->setDescendingDirection()
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(SearchQueryCategoryInterface::CATEGORY_ID, $categoryId)
            ->addFilter(Config::STORE_ID_ATTRIBUTE_CODE, $this->storeManager->getStore()->getId())
            ->addFilter(Config::SEARCH_NUM_RESULTS_FIELD_NAME, 0, 'gt')
            ->setSortOrders([$sortByCount])
            ->setPageSize($this->config->getPopularSearchSize())
            ->create();

        $searchResult = $this->getList($searchCriteria);

        return $searchResult->getItems();
    }
}
