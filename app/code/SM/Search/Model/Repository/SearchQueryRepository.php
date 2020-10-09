<?php

declare(strict_types=1);

namespace SM\Search\Model\Repository;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Search\Model\ResourceModel\Query\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use SM\Search\Api\Data\Response\SearchQuerySearchResultsInterface;
use SM\Search\Api\Data\Response\SearchQuerySearchResultsInterfaceFactory;
use SM\Search\Api\Repository\SearchQueryRepositoryInterface;
use SM\Search\Helper\Config;
use SM\Search\Model\Data\Response\QueryTextFactory;
use SM\Search\Model\Entity\SearchQueryCategoryFactory;
use SM\Search\Model\ResourceModel\SearchQueryPersonal\Collection;

class SearchQueryRepository implements SearchQueryRepositoryInterface
{
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
     * @var SearchQuerySearchResultsInterfaceFactory
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
     * @var QueryTextFactory
     */
    protected $queryTextFactory;

    /**
     * @var SearchQueryCategoryFactory
     */
    protected $queryCategoryFactory;

    /**
     * SearchQueryRepository constructor.
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param CollectionFactory $collectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SearchQuerySearchResultsInterfaceFactory $searchResultsFactory
     * @param StoreManagerInterface $storeManager
     * @param Config $config
     * @param QueryTextFactory $queryTextFactory
     * @param SearchQueryCategoryFactory $queryCategoryFactory
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        SearchQuerySearchResultsInterfaceFactory $searchResultsFactory,
        StoreManagerInterface $storeManager,
        Config $config,
        QueryTextFactory $queryTextFactory,
        SearchQueryCategoryFactory $queryCategoryFactory
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->storeManager = $storeManager;
        $this->config = $config;
        $this->queryTextFactory = $queryTextFactory;
        $this->queryCategoryFactory = $queryCategoryFactory;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchQuerySearchResultsInterface
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var SearchQuerySearchResultsInterface $searchResults */
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
    public function getPopular(): array
    {
        $data = [];
        $sortByCount = $this->sortOrderBuilder
            ->setField(Config::SEARCH_COUNT_FIELD_NAME)
            ->setDescendingDirection()
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(Config::STORE_ID_ATTRIBUTE_CODE, $this->storeManager->getStore()->getId())
            ->addFilter(Config::SEARCH_NUM_RESULTS_FIELD_NAME, 0, 'gt')
            ->setSortOrders([$sortByCount])
            ->setPageSize($this->config->getPopularSearchSize())
            ->create();

        $searchResult = $this->getList($searchCriteria);

        foreach ($searchResult->getItems() as $item) {
            //Change response to SearchQueryCategoryInterface[] for same response with api V1/search/popular-search/category/:categoryId
            //For more information:APO-2857
            $queryCategory = $this->queryCategoryFactory->create();
            $queryCategory->setQueryText($item->getQueryText());
            $queryCategory->setNumResults((int)$item->getNumResults());
            $queryCategory->setPopularity((int)$item->getPopularity());
            $queryCategory->setStoreId((int)$item->getStoreId());
            $queryCategory->setQueryId((int)$item->getQueryId());
            $data[] = $queryCategory;
        }

        return $data;
    }
}
