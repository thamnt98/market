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
    protected $productFactory;

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
     * Search constructor.
     * @param Searchs $search
     * @param \Magento\Framework\Api\Search\SearchCriteriaInterface $searchCriteria
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param \Magento\Framework\Api\FilterFactory $filter
     * @param CollectionFactory $productFactory
     * @param \SM\MobileApi\Helper\Product $productHelper
     * @param SearchInterface $searchInterface
     * @param \Magento\Framework\Api\SortOrder $sortOrder
     * @param ReportViewedProductSummaryRepositoryInterface $reportViewedProductSummaryRepository
     * @param QueryFactory $queryFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $configurableProductModel
     * @param \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \SM\Category\Model\Catalog\Search $mCatalogSearch
     * @param \SM\MobileApi\Model\Data\Product\LiistFactory $listFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
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
        $this->productFactory                       = $productFactory;
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
        $filter[] = $this->_createFilterFactory(SearchHelperConfig::SEARCH_PARAM_SEARCH_TEXT_FIELD_NAME, $keyword, 'eq');
        if (!empty($categoryId)) {
            $filter[] = $this->_createFilterFactory(SearchHelperConfig::CATEGORY_IDS_ATTRIBUTE_CODE, $categoryId, 'eq');
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
     * {@inheritdoc}
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
        $result->setProducts($this->mCatalogSearch->getProductsV2());

        return $result;
    }

    /**
     * @param $key
     * @param $value
     * @param $conditionType
     * @return \Magento\Framework\Api\Filter
     */
    protected function _createFilterFactory($key, $value, $conditionType)
    {
        $filter = $this->filterFactory->create();
        $filter->setValue($value);
        $filter->setField($key);
        $filter->setConditionType($conditionType);
        return $filter;
    }

    /**
     * @param int $customerId
     * @return array|SearchInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getRecommendationProducts(int $customerId)
    {
        /** @var \Magento\Framework\Api\Search\SearchResult $result */
        $result = $this->reportViewedProductSummaryRepository->getRecommendationProducts($customerId);

        $items      = $result->getProducts();
        $totalCount = $result->getTotalCount();
        return $this->_convertProductByIds($items, $totalCount, true);
    }

    /**
     * @param string $keyword
     * @param int $categoryId
     * @return array|SearchInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSearchSuggestion($keyword, $categoryId = null)
    {
        $filter = [];
        $filter[] = $this->_createFilterFactory(SearchHelperConfig::SEARCH_PARAM_SEARCH_TEXT_FIELD_NAME, $keyword, 'eq');

        if (!empty($categoryId)) {
            $filter[] = $this->_createFilterFactory(SearchHelperConfig::CATEGORY_IDS_ATTRIBUTE_CODE, $categoryId, 'eq');
            $this->registry->register(self::SHOW_CATEGORY_NAMES, false);
        } else {
            $this->registry->register(self::SHOW_CATEGORY_NAMES, true);
        }

        $this->filterGroup->setFilters($filter);

        $this->searchCriterial->setFilterGroups([$this->filterGroup]);
        $this->searchCriterial->setRequestName('quick_search_container');

        /** @var \Magento\Framework\Api\Search\SearchResult $result */
        $result            = $this->search->suggest($this->searchCriterial);
        $responseInterface = $this->_initProducts($result);
        $responseInterface->setSearchType($result->getType());
        $responseInterface->setSuggestKeyword($result->getTypoSuggestKeyword());

        return $responseInterface;
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
        $productCollection = $this->productFactory->create();
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

        $productCollection = $this->productFactory->create();
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
}
