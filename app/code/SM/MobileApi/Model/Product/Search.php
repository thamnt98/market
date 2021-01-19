<?php

declare(strict_types=1);

namespace SM\MobileApi\Model\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Search\Model\QueryFactory;
use SM\MobileApi\Api\Data\Product\SearchInterface;
use SM\MobileApi\Api\SearchProductInterface;
use SM\Reports\Api\Repository\ReportViewedProductSummaryRepositoryInterface;
use SM\Search\Api\Data\Response\SuggestionSearchResultInterface;
use SM\Search\Helper\Config;
use SM\Search\Helper\Config as SearchHelperConfig;
use SM\Search\Model\Search as Searchs;

class Search implements SearchProductInterface
{
    const SHOW_CATEGORY_NAMES  = 'show_category_names';

    /**
     * @var Searchs
     */
    protected $search;

    /**
     * @var \Magento\Framework\Api\Search\SearchCriteriaInterface
     */
    protected $searchCriterial;
    /**
     * @var \Magento\Framework\Api\Search\FilterGroup
     */
    protected $filterGroup;

    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \SM\MobileApi\Helper\Product
     */
    protected $productHelper;

    /**
     * @var SearchInterface
     */
    protected $responseInterface;

    /**
     * @var \Magento\Framework\Api\FilterFactory
     */
    protected $filterFactory;

    /**
     * @var \Magento\Framework\Api\SortOrder
     */
    protected $sortOrder;

    /**
     * @var ReportViewedProductSummaryRepositoryInterface
     */
    protected $reportViewedProductSummaryRepository;

    /**
     * @var QueryFactory
     */
    protected $queryFactory;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable
     */
    protected $configurableProductModel;

    /**
     * @var \Magento\Framework\Api\SortOrderBuilder
     */
    protected $sortOrderBuilder;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \SM\Category\Model\Catalog\Search
     */
    protected $mCatalogSearch;

    /**
     * @var \SM\MobileApi\Model\Data\Product\LiistFactory
     */
    protected $productListFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \SM\MobileApi\Model\Data\Product\ListItemFactory
     */
    protected $productDataFactory;

    /**
     * @var \SM\Catalog\Helper\Data
     */
    protected $helper;

    /**
     * @var \SM\MobileApi\Model\Data\Catalog\Product\ReviewFactory
     */
    protected $productReviewDataFactory;

    /**
     * @var \SM\Search\Api\Data\Product\SuggestionInterfaceFactory
     */
    protected $suggestionFactory;

    /**
     * @var \SM\Search\Api\Catalog\SuggestionResultInterface
     */
    protected $suggestionResult;

    /**
     * Search constructor.
     *
     * @param \SM\Search\Api\Catalog\SuggestionResultInterface                           $suggestionResult
     * @param \SM\Search\Api\Data\Product\SuggestionInterfaceFactory                     $suggestionFactory
     * @param \SM\Catalog\Helper\Data                                                    $helper
     * @param \SM\MobileApi\Model\Data\Catalog\Product\ReviewFactory                     $productReviewDataFactory
     * @param \SM\MobileApi\Model\Data\Product\ListItemFactory                           $productDataFactory
     * @param Searchs                                                                    $search
     * @param \Magento\Framework\Api\Search\SearchCriteriaInterface                      $searchCriteria
     * @param \Magento\Framework\Api\Search\FilterGroup                                  $filterGroup
     * @param \Magento\Framework\Api\FilterFactory                                       $filter
     * @param CollectionFactory                                                          $productFactory
     * @param \SM\MobileApi\Helper\Product                                               $productHelper
     * @param SearchInterface                                                            $searchInterface
     * @param \Magento\Framework\Api\SortOrder                                           $sortOrder
     * @param ReportViewedProductSummaryRepositoryInterface                              $reportViewedProductSummaryRepository
     * @param QueryFactory                                                               $queryFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface                            $productRepository
     * @param \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $configurableProductModel
     * @param \Magento\Framework\Api\SortOrderBuilder                                    $sortOrderBuilder
     * @param \Magento\Framework\App\RequestInterface                                    $request
     * @param \SM\Category\Model\Catalog\Search                                          $mCatalogSearch
     * @param \SM\MobileApi\Model\Data\Product\LiistFactory                              $listFactory
     * @param \Magento\Framework\Registry                                                $registry
     */
    public function __construct(
        \SM\Search\Api\Catalog\SuggestionResultInterface $suggestionResult,
        \SM\Search\Api\Data\Product\SuggestionInterfaceFactory $suggestionFactory,
        \SM\Catalog\Helper\Data $helper,
        \SM\MobileApi\Model\Data\Catalog\Product\ReviewFactory $productReviewDataFactory,
        \SM\MobileApi\Model\Data\Product\ListItemFactory $productDataFactory,
        Searchs $search,
        \Magento\Framework\Api\Search\SearchCriteriaInterface $searchCriteria,
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magento\Framework\Api\FilterFactory $filter,
        CollectionFactory $productFactory,
        \SM\MobileApi\Helper\Product $productHelper,
        SearchInterface $searchInterface,
        \Magento\Framework\Api\SortOrder $sortOrder,
        ReportViewedProductSummaryRepositoryInterface $reportViewedProductSummaryRepository,
        QueryFactory $queryFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $configurableProductModel,
        \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \SM\Category\Model\Catalog\Search $mCatalogSearch,
        \SM\MobileApi\Model\Data\Product\LiistFactory $listFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->search                               = $search;
        $this->searchCriterial                      = $searchCriteria;
        $this->filterGroup                          = $filterGroup;
        $this->filterFactory                        = $filter;
        $this->productCollectionFactory             = $productFactory;
        $this->productHelper                        = $productHelper;
        $this->responseInterface                    = $searchInterface;
        $this->sortOrder                            = $sortOrder;
        $this->reportViewedProductSummaryRepository = $reportViewedProductSummaryRepository;
        $this->queryFactory                         = $queryFactory;
        $this->productRepository                    = $productRepository;
        $this->configurableProductModel             = $configurableProductModel;
        $this->sortOrderBuilder                     = $sortOrderBuilder;
        $this->request                              = $request;
        $this->mCatalogSearch                       = $mCatalogSearch;
        $this->productListFactory                   = $listFactory;
        $this->registry                             = $registry;
        $this->productDataFactory = $productDataFactory;
        $this->helper = $helper;
        $this->productReviewDataFactory = $productReviewDataFactory;
        $this->suggestionFactory = $suggestionFactory;
        $this->suggestionResult = $suggestionResult;
    }

    /**
     * {@inheritdoc}
     */
    public function search($customerId, $keyword, $page = 1, $size = 12, $categoryId = null)
    {
        if (trim($keyword) === '') {
            return [];
        }
        $keyword = urldecode($keyword);

        $filter = [];
        //page start from 0
        $page = (int)$page - 1;
        // set limit data
        if ($size >= SearchInterface::MAXIMUM_PRODUCTS_RESPONSE) {
            $size = SearchInterface::MAXIMUM_PRODUCTS_RESPONSE;
        }

        $this->searchCriterial->setCurrentPage($page);
        $filter[] = $this->createFilterFactory(SearchHelperConfig::SEARCH_PARAM_SEARCH_TEXT_FIELD_NAME, $keyword, 'eq');
        if (!empty($categoryId)) {
            $filter[] = $this->createFilterFactory(SearchHelperConfig::CATEGORY_IDS_ATTRIBUTE_CODE, $categoryId, 'eq');
        }
        $this->filterGroup->setFilters($filter);

        $this->searchCriterial->setFilterGroups([$this->filterGroup]);
        $this->searchCriterial->setPageSize($size);
        $this->searchCriterial->setRequestName('quick_search_container');

        /** @var \Magento\Framework\Api\Search\SearchResult $result */
        $result     = $this->search->search($customerId, $this->searchCriterial);
        $result->setData(SuggestionSearchResultInterface::TYPE, '');
        $result->setData(SuggestionSearchResultInterface::TYPO_SUGGEST_KEYWORD, '');

        return $this->_initProducts($result);
    }

    /**
     * @param int    $customerId
     * @param string $keyword
     * @param int    $p
     * @param int    $limit
     *
     * @return \SM\MobileApi\Api\Data\Product\ListInterface
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function searchV2($customerId, $keyword, $p = 1, $limit = 12)
    {
        //Set prams for query factory
        $this->request->setParams([QueryFactory::QUERY_VAR_NAME => $keyword]);

        $this->mCatalogSearch->init($customerId);
        //Save number result to update data in latest search
        $query = $this->queryFactory->get();
        $query->saveNumResults($this->mCatalogSearch->getToolbarInfo()->getProductTotal());

        $result = $this->productListFactory->create();
        $result->setToolbarInfo($this->mCatalogSearch->getToolbarInfo());
        $result->setFilters($this->mCatalogSearch->getFilters());
        $result->setProducts($this->getLayerProducts($customerId));

        return $result;
    }

    /**
     * @param $key
     * @param $value
     * @param $conditionType
     * @return \Magento\Framework\Api\Filter
     */
    protected function createFilterFactory($key, $value, $conditionType)
    {
        $filter = $this->filterFactory->create();
        $filter->setValue($value);
        $filter->setField($key);
        $filter->setConditionType($conditionType);
        return $filter;
    }

    /**
     * @param int $customerId
     *
     * @return array|SearchInterface
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getRecommendationProducts(int $customerId)
    {
        $total = 0;
        $productsData = [];

        if ($customerId) {
            $enableReview = $this->productHelper->getReviewEnable();
            $coll = $this->reportViewedProductSummaryRepository->getRecommendationCollection($customerId);
            foreach ($coll as $item) {
                $productsData[] = $this->prepareListItemData($item)->setReviewEnable($enableReview);
            }

            $total = count($productsData);
        }

        $this->responseInterface->setProducts($productsData);
        $this->responseInterface->setTotal($total);

        return $this->responseInterface;
    }

    /**
     * @param string $keyword
     * @param int    $category_id
     *
     * @return \SM\Search\Api\Catalog\SuggestionResultInterface
     */
    public function getSearchSuggestion($keyword, $category_id = null)
    {
        $this->filterGroup->setFilters($this->getSuggestionFilter($keyword, $category_id));
        $this->searchCriterial->setFilterGroups([$this->filterGroup]);
        $this->searchCriterial->setRequestName('quick_search_container');

        /** @var \Magento\Framework\Api\Search\SearchResult $searchResult */
        $searchResult = $this->search->searchSuggestion($this->searchCriterial);
        $productIds   = [];
        foreach ($searchResult->getItems() as $item) {
            $productIds[] = $item->getId();
        }

        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $coll */
        $coll = $this->productCollectionFactory->create();
        $coll->addAttributeToSelect('name')
            ->addFieldToFilter('entity_id', ['in' => $productIds])
            ->addAttributeToFilter([
                ["attribute" => "is_tobacco", "null" => true],
                ["attribute" => "is_tobacco", "eq" => 0]
            ]);

        if ($this->request->getParam('limit')) {
            $coll->getSelect()->limit((int)$this->request->getParam('limit'));
        }

        $list = $this->prepareSuggestionData($coll->getItems());

        return $this->suggestionResult
            ->setProducts($list)
            ->setTotal(count($list));
    }

    /**
     * Search product using barcode
     * {@inheritDoc}
     */
    public function searchProductByBarcode(int $customerId, string $barcode)
    {
        $barcode = urldecode($barcode);

        //Get Products
        $product = $this->_getProductByBarcode($barcode);
        $productName = isset($product) ? $product->getName() : null;

        $convertProductObject = $this->productHelper->getProductListToResponseV2($product);
        $listProduct = isset($convertProductObject) ? [$convertProductObject] : [];

        //Create or update query search
        $query = $this->queryFactory->create()->loadByQueryText($productName);
        $query->setData(Config::CUSTOMER_ID_ATTRIBUTE_CODE, $customerId);

        if (empty($product)) {
            $query->setQueryText('');
            $this->responseInterface->setSearchType(SuggestionSearchResultInterface::TYPE_NO_RESULT);
        }

        if (!empty($product)) {
            $query->setQueryText($productName);
            $this->responseInterface->setSearchType(SuggestionSearchResultInterface::TYPE_MATCH_RESULTS);
        }

        if (!$query->isQueryTextShort()) {
            $query->saveIncrementalPopularity();
        }

        $query->saveNumResults(isset($product) ? 1 : 0);
        $this->responseInterface->setSuggestKeyword(null);
        $this->responseInterface->setProducts($listProduct);
        $this->responseInterface->setTotal(isset($product) ? 1 : 0);

        return $this->responseInterface;
    }

    /**
     * @param string $barcode
     * @return ProductInterface|\Magento\Framework\DataObject | null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _getProductByBarcode(string $barcode)
    {
        $productCollection = $this->productCollectionFactory->create();
        $products          = $productCollection->addAttributeToSelect('name')->addFieldToFilter(
            Config::PRODUCT_ATTRIBUTE_BARCODE,
            ['eq' => $barcode]
        )->addAttributeToFilter([
            ["attribute" => "is_tobacco", "null" => true],
            ["attribute" => "is_tobacco", "eq" => 0]
        ])->getFirstItem();
        if (!$products->getEntityId()) {
            return null;
        }

        //Check product is child of configurable product
        $productResult  = null;
        $firstProductId = $products->getEntityId();
        $parentIds      = $this->configurableProductModel->getParentIdsByChild($firstProductId);

        if (!empty($parentIds)) {
            try {
                $productResult = $this->productRepository->getById($parentIds[0]);
            } catch (NoSuchEntityException $exception) {
                $productResult = null;
            }
        }

        if (empty($parentIds)) {
            $productResult = $products;
        }

        return $productResult;
    }

    /**
     * @param $result
     * @return array|SearchInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _initProducts($result)
    {
        $items      = $result->getProducts();
        $totalItems = $result->getTotalCount();

        return $this->_convertProductByIds($items, $totalItems);
    }

    /**
     * @param $items
     * @param $totalItems
     * @param bool $getId
     * @return array|SearchInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _convertProductByIds($items, $totalItems, $getId = true)
    {
        $productIds = [];
        foreach ($items as $item) {
            if ($getId) {
                $productIds[] = $item->getId();
            }

            if (!$getId) {
                $productIds[] = $item->getProductId();
            }
        }

        $productCollection = $this->productCollectionFactory->create();
        $products          = $productCollection->addFieldToSelect('*')
            ->addFieldToFilter('entity_id', ['in' => $productIds])
            ->addAttributeToFilter([
                ["attribute" => "is_tobacco", "null" => true],
                ["attribute" => "is_tobacco", "eq" => 0]
            ])
            ->getItems();

        usort($products, function ($a, $b) use ($productIds) {
            /** @var ProductInterface $a */
            $pos_a = array_search($a->getId(), $productIds);
            /** @var ProductInterface $b */
            $pos_b = array_search($b->getId(), $productIds);
            return $pos_a - $pos_b;
        });

        $list = $this->productHelper->parseProductsResponse($products);

        $this->responseInterface->setProducts($list);
        $this->responseInterface->setTotal($productCollection->getSize());

        return $this->responseInterface;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return \SM\MobileApi\Api\Data\Product\ListItemInterface
     */
    protected function prepareListItemData($product)
    {
        /** @var \SM\MobileApi\Model\Data\Product\ListItem $data */
        $data = $this->productDataFactory->create();
        /** @var \SM\MobileApi\Model\Data\Catalog\Product\Review $reviewData */
        $reviewData = $this->productReviewDataFactory->create();

        $reviewData
            ->setReviewCounter($product->getData('reviews_count'))
            ->setPercent($product->getData('rating_summary'));

        if ($product->getTypeId() !== \Magento\Bundle\Model\Product\Type::TYPE_CODE) {
            $minProduct = $this->helper->getMinProduct($product);
            $price = $minProduct->getPrice();
            $final = $minProduct->getFinalPrice();
            $discountPercent = $this->helper->getDiscountSingle($minProduct);
        } else {
            [$final, $price] = $this->helper->getSumPriceMinBundle($product);
            $discountPercent = $this->helper->getDiscountBundleMin($final, $price);
        }

        $data
            ->setId($product->getId())
            ->setSku($product->getSku())
            ->setName($product->getName())
            ->setType($product->getTypeId())
            ->setTypeId($product->getTypeId())
            ->setPrice($price)
            ->setFinalPrice($final)
            ->setReview($reviewData)
            ->setItemId($product->getData('quote_item_id'))
            ->setItemQty($product->getData('quote_item_qty'))
            ->setIsAvailable($product->isAvailable())
            ->setStock($this->productHelper->getProductStockQty($product))
            ->setIsInStock($product->isInStock())
            ->setIsSaleable($product->isSaleable())
            ->setConfigChildCount($this->helper->getCountChildren($product->getId()))
            ->setDiscountPercent((int)$discountPercent);

        try {
            $data->setGtmData($this->productHelper->prepareGTM($product, $data));
        } catch (\Exception $e) {
        }

        try {
            $data->setImage($this->productHelper->getMainImage($product));
        } catch (\Exception $e) {
        }

        try {
            $data->setProductLabel($this->productHelper->getProductLabel($product));
        } catch (\Exception $e) {
        }

        return $data;
    }

    /**
     * @param string $keyword
     * @param int $categoryId
     *
     * @return \Magento\Framework\Api\Filter[]
     */
    protected function getSuggestionFilter($keyword, $categoryId)
    {
        $result = [];
        $result[] = $this->createFilterFactory(
            SearchHelperConfig::SEARCH_PARAM_SEARCH_TEXT_FIELD_NAME,
            $keyword,
            'eq'
        );

        if (!empty($categoryId)) {
            $result[] = $this->createFilterFactory(SearchHelperConfig::CATEGORY_IDS_ATTRIBUTE_CODE, $categoryId, 'eq');
            $this->registry->register(self::SHOW_CATEGORY_NAMES, false);
        } else {
            $this->registry->register(self::SHOW_CATEGORY_NAMES, true);
        }

        return $result;
    }

    /**
     * @param \Magento\Catalog\Model\Product[]|\Magento\Framework\DataObject[] $products
     *
     * @return \SM\Search\Api\Data\Product\SuggestionInterface[]
     */
    protected function prepareSuggestionData($products)
    {
        $result = [];

        foreach ($products as $product) {
            /** @var \SM\Search\Api\Data\Product\SuggestionInterface $data */
            $data = $this->suggestionFactory->create();

            $data
                ->setId($product->getId())
                ->setSku($product->getSku())
                ->setName($product->getName());

            $result[] = $data;
        }

        return $result;
    }

    /**
     * @param int $customerId
     *
     * @return array
     */
    protected function getLayerProducts($customerId)
    {
        $result = [];

        $enableReview = $this->productHelper->getReviewEnable();
        /** @var \Magento\Catalog\Model\Product $product */
        foreach ($this->prepareLayerProductCollection($customerId) as $product) {
            $result[] = $this->prepareListItemData($product)->setReviewEnable($enableReview);
        }

        return $result;
    }

    /**
     * @param int $customerId
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function prepareLayerProductCollection($customerId)
    {
        $coll = $this->mCatalogSearch->getProductCollectionWithReview();

        $coll
            ->addAttributeToSelect('*')
            ->addAttributeToFilter(
                [
                    ["attribute" => "is_tobacco", "null" => true],
                    ["attribute" => "is_tobacco", "eq" => 0],
                ]
            );

        if ($customerId) { // Add quote item data
            $inCartSelect = $coll->getConnection()->select();
            $inCartSelect
                ->from(['qi' => 'quote_item'], [])
                ->joinInner(['q' => 'quote'], 'q.entity_id = qi.quote_id', [])
                ->where('qi.product_id = e.entity_id')
                ->where('qi.parent_item_id IS NULL')
                ->where('q.is_active = ?', 1)
                ->where('q.customer_id = ?', $customerId)
                ->limit(1);

            $itemIdSelect = clone $inCartSelect;
            $itemIdSelect->columns(['qi.item_id']);
            $qtySelect = clone $inCartSelect;
            $qtySelect->columns(['qi.qty']);

            $coll
                ->getSelect()
                ->columns([
                    "({$itemIdSelect->__toString()}) AS quote_item_id",
                    "({$qtySelect->__toString()}) AS quote_item_qty",
                ]);
        }

        return $coll;
    }
}
