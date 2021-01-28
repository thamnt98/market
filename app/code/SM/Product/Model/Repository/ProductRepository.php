<?php

declare(strict_types=1);

namespace SM\Product\Model\Repository;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductSearchResultsInterface;
use Magento\Catalog\Api\Data\ProductSearchResultsInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface as BaseProductRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Layer\Category\CollectionFilter;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use SM\Product\Api\Repository\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    /**
     * @var BaseProductRepositoryInterface
     */
    protected $productRepository;

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
     * @var JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var CollectionFilter
     */
    protected $collectionFilter;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ProductSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * ReportEventPersonalRepository constructor.
     * @param BaseProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param CollectionFactory $collectionFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param CollectionFilter $collectionFilter
     * @param CategoryFactory $categoryFactory
     * @param StoreManagerInterface $storeManager
     * @param ProductSearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        BaseProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        CollectionFactory $collectionFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        CollectionFilter $collectionFilter,
        CategoryFactory $categoryFactory,
        StoreManagerInterface $storeManager,
        ProductSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->collectionFactory = $collectionFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->collectionFilter = $collectionFilter;
        $this->categoryFactory = $categoryFactory;
        $this->storeManager = $storeManager;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * @inheritDoc
     */
    public function get(
        int $customerId,
        string $sku,
        bool $editMode = false,
        ?int $storeId = null,
        bool $forceReload = false
    ): ProductInterface {
        return $this->productRepository->get($sku, $editMode, $storeId, $forceReload);
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function getStoreFrontList(SearchCriteriaInterface $searchCriteria): ProductSearchResultsInterface
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $this->extensionAttributesJoinProcessor->process($collection);
        $this->collectionFilter->filter($collection, $this->getRootCategory());
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var ProductSearchResultsInterface $searchResult */
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setTotalCount($collection->getSize());
        $searchResult->setItems($collection->getItems());

        return $searchResult;
    }

    /**
     * @return Category
     * @throws LocalizedException
     */
    protected function getRootCategory(): Category
    {
        $category = $this->categoryFactory->create();
        /** @var Store $store */
        $store = $this->storeManager->getStore();
        $category->setId($store->getRootCategoryId());
        return $category;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function getStoreFrontProductByIds(
        array $productIds,
        $limit = null,
        $sortBy = self::ENTITY_ID,
        $direction = SortOrder::SORT_ASC
    ): array {
        $searchCriteriaBuilder = $this->searchCriteriaBuilder
            ->addFilter(ProductRepositoryInterface::ENTITY_ID, $productIds, 'in');

        if ($sortBy != self::ENTITY_ID) {
            $sortOrder = $this->sortOrderBuilder
                ->setField($sortBy)
                ->setDirection($direction)
                ->create();
            $searchCriteriaBuilder->setSortOrders([$sortOrder]);
        }
        $searchCriteria = $searchCriteriaBuilder->create();

        $searchResult = $this->getStoreFrontList($searchCriteria);
        $products = $searchResult->getItems();

        if ($sortBy != self::ENTITY_ID) {
            return $products;
        }

        usort($products, function ($a, $b) use ($productIds) {
            /** @var ProductInterface $a */
            $pos_a = array_search($a->getId(), $productIds);
            /** @var ProductInterface $b */
            $pos_b = array_search($b->getId(), $productIds);
            return $pos_a - $pos_b;
        });

        if (isset($limit)) {
            $products = array_slice($products, 0, $limit);
        }

        return $products;
    }

    /**
     * @return Collection
     */
    public function generateCollection()
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $coll */
        $coll = $this->collectionFactory->create();
        try {
            $coll->addFieldToSelect('*');
            $this->extensionAttributesJoinProcessor->process($coll);
            $this->collectionFilter->filter($coll, $this->getRootCategory());
        } catch (\Exception $e) {
        }

        return $coll;
    }

    /**
     * @return Collection
     */
    public function generateMobileCollection()
    {
        return $this
            ->generateCollection()
            ->addAttributeToFilter(// Mobile not show tobacco product
                [
                    ["attribute" => "is_tobacco", "null" => true],
                    ["attribute" => "is_tobacco", "eq" => 0],
                ]
            );
    }
}
