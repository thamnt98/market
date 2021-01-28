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
     * @var \Magento\Review\Model\ResourceModel\Review\Summary
     */
    protected $reviewSummary;

    /**
     * ReportViewedProductSummaryRepository constructor.
     *
     * @param \Magento\Review\Model\ResourceModel\Review\Summary      $reviewSummary
     * @param ResourceModel                                           $resourceModel
     * @param SearchCriteriaBuilder                                   $searchCriteriaBuilder
     * @param SortOrderBuilder                                        $sortOrderBuilder
     * @param CollectionFactory                                       $collectionFactory
     * @param CollectionProcessorInterface                            $collectionProcessor
     * @param ReportViewedProductSummarySearchResultsInterfaceFactory $searchResultsFactory
     * @param StoreManagerInterface                                   $storeManager
     * @param RequestInterface                                        $request
     * @param Extractor                                               $extractor
     * @param ProductRepositoryInterface                              $productRepository
     * @param Config                                                  $config
     */
    public function __construct(
        \Magento\Review\Model\ResourceModel\Review\Summary $reviewSummary,
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
        $this->reviewSummary = $reviewSummary;
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
        /** @var ReportViewedProductSummarySearchResultsInterface $result */
        $result = $this->searchResultsFactory->create();
        $coll = $this->getRecommendationCollection($customerId);

        $result->setProducts($coll->getItems());
        $result->setTotalCount($coll->getSize());

        return $result;
    }

    /**
     * @param $customerId
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException|LocalizedException
     */
    public function getRecommendationCollection($customerId)
    {
        $limit = $this->request->getParam(
            \SM\Reports\Helper\Config::SEARCH_PARAM_RECOMMENDATION_LIMIT_FIELD_NAME,
            $this->config->getRecommendationProductsSize()
        );

        $coll = $this->productRepository->generateMobileCollection();
        $this->reviewSummary->appendSummaryFieldsToCollection(
            $coll,
            $this->storeManager->getStore()->getId() . '',
            \Magento\Review\Model\Review::ENTITY_PRODUCT_CODE
        );

        $inCartSelect = $coll->getConnection()->select();
        $inCartSelect
            ->from(['qi' => 'quote_item'], 'item_id')
            ->joinInner(['q' => 'quote'], 'q.entity_id = qi.quote_id', [])
            ->where('qi.product_id = e.entity_id')
            ->where('qi.parent_item_id IS NULL')
            ->where('q.is_active = ?', 1)
            ->where('q.customer_id = ?', $customerId)
            ->limit(1);

        $coll->getSelect()
            ->joinInner(
                ['view' => 'report_viewed_product_summary'],
                'e.entity_id = view.product_id',
                []
            )->where(
                'view.customer_id = ?',
                $customerId
            )->where(
                'view.store_id = ?',
                $this->storeManager->getStore()->getId()
            )->where(
                "({$inCartSelect->__toString()}) IS NULL"
            )->order(
                'view.popularity DESC'
            )->order(
                'view.updated_at DESC'
            )->limit($limit);

        return $coll;
    }
}
