<?php

declare(strict_types=1);

namespace SM\Reports\Model\Repository;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use SM\Product\Api\Repository\ProductRepositoryInterface;
use SM\Reports\Api\Data\Response\ReportViewedProductSummarySearchResultsInterface;
use SM\Reports\Api\Data\Response\ReportViewedProductSummarySearchResultsInterfaceFactory;
use SM\Reports\Api\Entity\ReportViewedProductSummaryInterface;
use SM\Reports\Api\Repository\ReportViewedProductSummaryRepositoryInterface;
use SM\Reports\Helper\Config;
use SM\Reports\Model\ReportViewedProductSummary\Extractor;
use SM\Reports\Model\ResourceModel\ReportViewedProductSummary as ResourceModel;
use SM\Reports\Model\ResourceModel\ReportViewedProductSummary\Collection;
use SM\Reports\Model\ResourceModel\ReportViewedProductSummary\CollectionFactory;

class ReportViewedProductSummaryRepository implements ReportViewedProductSummaryRepositoryInterface
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
     * @var ReportViewedProductSummarySearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var Extractor
     */
    protected $extractor;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var Config
     */
    protected $config;

    /**
     * ReportViewedProductSummaryRepository constructor.
     * @param ResourceModel $resourceModel
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param CollectionFactory $collectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param ReportViewedProductSummarySearchResultsInterfaceFactory $searchResultsFactory
     * @param StoreManagerInterface $storeManager
     * @param RequestInterface $request
     * @param Extractor $extractor
     * @param ProductRepositoryInterface $productRepository
     * @param Config $config
     */
    public function __construct(
        ResourceModel $resourceModel,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        ReportViewedProductSummarySearchResultsInterfaceFactory $searchResultsFactory,
        StoreManagerInterface $storeManager,
        RequestInterface $request,
        Extractor $extractor,
        ProductRepositoryInterface $productRepository,
        Config $config
    ) {
        $this->resourceModel = $resourceModel;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->storeManager = $storeManager;
        $this->request = $request;
        $this->extractor = $extractor;
        $this->productRepository = $productRepository;
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
    public function getList(SearchCriteriaInterface $searchCriteria): ReportViewedProductSummarySearchResultsInterface
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var ReportViewedProductSummarySearchResultsInterface $searchResults */
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
    public function getRecommendationProducts(int $customerId): ReportViewedProductSummarySearchResultsInterface
    {
        $sortByPopularity = $this->sortOrderBuilder
        ->setField(ReportViewedProductSummaryInterface::POPULARITY)
        ->setDescendingDirection()
        ->create();

        $sortByLatestView = $this->sortOrderBuilder
        ->setField(ReportViewedProductSummaryInterface::UPDATED_AT)
        ->setDescendingDirection()
        ->create();

        $limit = $this->request->getParam(Config::SEARCH_PARAM_RECOMMENDATION_LIMIT_FIELD_NAME)
            ?? $this->config->getRecommendationProductsSize();

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(ReportViewedProductSummaryInterface::CUSTOMER_ID, $customerId)
            ->addFilter(ReportViewedProductSummaryInterface::STORE_ID, $this->storeManager->getStore()->getId())
            ->setSortOrders([$sortByPopularity, $sortByLatestView])
            ->setPageSize($limit * 5) //Increase the product number limit to exclude out of stock or deleted product
            ->create();

        $searchResult = $this->getList($searchCriteria);

        $productIds = $this->extractor->extractProductIds($searchResult->getItems());
        if (!empty($productIds)) {
            $searchResult->setProducts(
                $this->productRepository->getStoreFrontProductByIds($productIds, $limit)
            );
        }

        return $searchResult;
    }
}
