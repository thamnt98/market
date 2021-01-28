<?php

namespace SM\Category\Model\Catalog;

use SM\Search\Helper\Config;

/**
 * Class Search
 * @package SM\MobileApi\Model\Catalog
 */
class Search extends \Magento\CatalogSearch\Block\Result
{
    protected $_productFactory;
    protected $layerResolver;
    protected $blockFactory;
    protected $objectManager;
    protected $_resultBlock;
    protected $_toolbar;
    protected $_collection;
    protected $mProductHelper;
    protected $productToolbar;
    protected $productToolbarOrder;
    protected $productFilterFactory;
    protected $productFilterItemFactory;
    protected $productV2Factory;
    protected $filterList;
    protected $urlResolver;
    protected $filterSettingHelper;
    protected $mProductStock;

    /**
     * @var \Magento\Review\Model\ResourceModel\Review\Summary
     */
    protected $reviewSummary;

    public function __construct(
        \Magento\Review\Model\ResourceModel\Review\Summary $reviewSummary,
        \Magento\Framework\ObjectManagerInterface $objectManagerInterface,
        \Magento\Framework\View\Element\BlockFactory $blockFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\CatalogSearch\Helper\Data $catalogSearchData,
        \Magento\Search\Model\QueryFactory $queryFactory,
        \SM\MobileApi\Helper\Product $japiProductHelper,
        \SM\MobileApi\Model\Product\Url $urlResolver,
        \SM\MobileApi\Model\Catalog\Layer\FilterListFactory $filterListFactory,
        \SM\MobileApi\Model\Data\Catalog\ProductFactory $productFactory,
        \SM\MobileApi\Model\Data\Catalog\ProductToolbarFactory $productToolbar,
        \SM\MobileApi\Model\Data\Catalog\ProductToolbarOrderFactory $productToolbarOrder,
        \SM\MobileApi\Model\Data\Catalog\ProductFilterFactory $productFilterFactory,
        \SM\MobileApi\Model\Data\Catalog\ProductFilterItemFactory $productFilterItemFactory,
        \SM\MobileApi\Model\Data\Product\ListItemFactory $listItemFactory,
        \Amasty\Shopby\Helper\FilterSetting $settingHelper,
        \SM\MobileApi\Model\Product\Stock $mProductStock,
        array $data = []
    ) {
        $this->layerResolver            = $layerResolver;
        $this->blockFactory             = $blockFactory;
        $this->objectManager            = $objectManagerInterface;
        $this->mProductHelper           = $japiProductHelper;
        $this->_productFactory          = $productFactory;
        $this->productToolbar           = $productToolbar;
        $this->productToolbarOrder      = $productToolbarOrder;
        $this->productFilterFactory     = $productFilterFactory;
        $this->productFilterItemFactory = $productFilterItemFactory;
        $this->productV2Factory         = $listItemFactory;
        $this->filterList               = $filterListFactory->create([], true);
        $this->urlResolver              = $urlResolver;
        $this->filterSettingHelper      = $settingHelper;
        $this->mProductStock            = $mProductStock;
        parent::__construct($context, $layerResolver, $catalogSearchData, $queryFactory, $data);
        $this->reviewSummary = $reviewSummary;
    }

    /**
     * @param $customerId
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function init($customerId)
    {
        /**
         * Init search query
         */
        $this->_initSearch($customerId);

        /**
         * Apply filters
         */
        foreach ($this->_getFilters() as $filter) {
            $filter->apply($this->_request);
        }

        $this->_getSearchLayer()->apply();
        $this->setListOrders();
        $this->setListModes();
    }

    /**
     * Get filters
     * @return array|\Magento\Catalog\Model\Layer\Filter\AbstractFilter[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFilters()
    {
        $filters = [];

        /** @var \Magento\Catalog\Model\Layer\Filter\Category $filter */
        foreach ($this->_getFilters() as $filter) {
            $filterInfo    = $this->productFilterFactory->create();
            $filterSetting = $this->filterSettingHelper->getSettingByLayerFilter($filter);
            $items         = [];

            foreach ($filter->getItems() as $item) {
                if (is_array($item)) {
                    foreach ($item as $value) {
                        $filterItemInfo = $this->productFilterItemFactory->create();
                        $filterItemInfo->setValue($value->getValue());
                        $filterItemInfo->setLabel($value->getLabel());
                        $filterItemInfo->setCount($value->getCount());
                        $items[] = $filterItemInfo;
                    }
                }

                if (!is_array($item)) {
                    $filterItemInfo = $this->productFilterItemFactory->create();
                    $filterItemInfo->setValue($item->getValue());
                    $filterItemInfo->setLabel($item->getLabel());
                    $filterItemInfo->setCount($item->getCount());
                    $items[] = $filterItemInfo;
                }
            }

            if ($filter->hasAttributeModel()) {
                $filterInfo->setName(__($filter->getAttributeModel()->getStoreLabel()));
                $filterInfo->setCode($filter->getAttributeModel()->getAttributeCode());
            } else {
                $filterInfo->setName(__($filter->getName()));
                $filterInfo->setCode($filter->getRequestVar());
            }

            if ($filter instanceof \Amasty\Shopby\Api\Data\FromToFilterInterface) {
                $setting = $filter->getFromToConfig();
                $filterInfo->setData(\SM\MobileApi\Model\Data\Catalog\ProductFilter::FROM, $setting['from'] ?? 0);
                $filterInfo->setData(\SM\MobileApi\Model\Data\Catalog\ProductFilter::TO, $setting['to'] ?? 0);
                $filterInfo->setData(\SM\MobileApi\Model\Data\Catalog\ProductFilter::MAX, $setting['max'] ?? 0);
                $filterInfo->setData(\SM\MobileApi\Model\Data\Catalog\ProductFilter::MIN, $setting['min'] ?? 0);
            }

            $filterInfo->setIsMultiselect($filterSetting->isMultiselect());
            $filterInfo->setItems($items);
            $filters[] = $filterInfo;
        }

        return $filters;
    }

    /**
     * Get Assigned Products V2
     */
    public function getProductsV2()
    {
        return $this->mProductHelper->convertProductCollectionToResponseV2($this->_getProductCollection());
    }

    /**
     * Get Toolbar infomation
     * @return \SM\MobileApi\Model\Data\Catalog\ProductToolbar
     */
    public function getToolbarInfo()
    {
        $this->_setToolbarData();
        /** @var \Magento\Catalog\Block\Product\ProductList\Toolbar $blockToolbar */
        $blockToolbar = $this->_toolbar;

        $result = $this->productToolbar->create();
        $result->setCurrentPageNum($blockToolbar->getCurrentPage());
        $result->setLastPageNum($blockToolbar->getLastPageNum());
        $result->setCurrentLimit($blockToolbar->getLimit());
        $result->setCurrentOrder($blockToolbar->getCurrentOrder());
        $result->setCurrentDirection($blockToolbar->getCurrentDirection());
        $result->setProductTotal($blockToolbar->getTotalNum());

        $orders = [];
        foreach ($blockToolbar->getAvailableOrders() as $key => $order) {
            $toolbarOrder = $this->productToolbarOrder->create();
            $toolbarOrder->setField($key);
            $toolbarOrder->setLabel(__($order));
            $orders[] = $toolbarOrder;
        }
        $result->setAvailableOrders($orders);

        return $result;
    }

    /**
     * init Search
     *
     * @param $customerId
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _initSearch($customerId)
    {
        /* @var $query \Magento\Search\Model\Query */
        $query = $this->_getQuery();

        $query->setStoreId($this->_storeManager->getStore()->getId());
        $query->setData(Config::CUSTOMER_ID_ATTRIBUTE_CODE, $customerId);

        if (! $query->getQueryText() || $query->getQueryText() == '') {
            throw new \Magento\Framework\Exception\LocalizedException(__('Query cannot be empty.'));
        }

        if ($this->catalogSearchData->isMinQueryLength()) {
            $query->setId(0)->setIsActive(1)->setIsProcessed(1);
        } else {
            $query->saveIncrementalPopularity();
        }
        $this->catalogSearchData->checkNotes();

        if (! $this->_resultBlock) {
            $_resultBlock  = $this->blockFactory->createBlock('Magento\CatalogSearch\Block\SearchResult\ListProduct');
            $_toolbarBlock = $this->blockFactory->createBlock('Magento\Catalog\Block\Product\ProductList\Toolbar');
            $_resultBlock->setToolbarBlock($_toolbarBlock);
            $this->_resultBlock = $_resultBlock;
        }
    }

    /**
     * Set search layer
     *
     * @return \Magento\Catalog\Model\Layer\Search
     */
    protected function _getSearchLayer()
    {
        return $this->objectManager->get('Magento\Catalog\Model\Layer\Search');
    }

    /**
     * Get filters data
     *
     * @return array|\Magento\Catalog\Model\Layer\Filter\AbstractFilter[]
     */
    protected function _getFilters()
    {
        return $this->filterList->getFilters($this->_getSearchLayer());
    }

    /**
     * Set search available list orders
     *
     * @return $this
     */
    public function setListOrders()
    {
        $category = $this->catalogLayer->getCurrentCategory();
        /* @var $category \Magento\Catalog\Model\Category */
        $availableOrders = $category->getAvailableSortByOptions();
        unset($availableOrders['position']);
        $availableOrders['relevance'] = __('Relevance');

        $this->_resultBlock->setAvailableOrders(
            $availableOrders
        )->setDefaultDirection(
            'desc'
        )->setDefaultSortBy(
            'relevance'
        );

        return $this;
    }

    /**
     * Set available view mode
     *
     * @return $this
     */
    public function setListModes()
    {
        $this->_resultBlock->setModes([ 'grid' => __('Grid'), 'list' => __('List') ]);

        return $this;
    }

    /**
     * Get Product Collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function _getProductCollection()
    {
        if (!$this->_collection) {
            $this->_collection = $this->_getSearchLayer()->getProductCollection();
            //Remove product is tobacco
            $this->_collection->addAttributeToFilter([
                ["attribute" => "is_tobacco", "null" => true],
                ["attribute" => "is_tobacco", "eq" => 0]]);
        }

        return $this->_collection;
    }

    /**
     * Set Product collection
     *
     * @param $collection
     *
     * @return $this
     */
    protected function _setProductCollection($collection)
    {
        $this->_collection = $collection;

        return $this;
    }

    /**
     * Set Toolbar Data
     */
    protected function _setToolbarData()
    {
        /** @var \Magento\Catalog\Block\Product\ProductList\Toolbar $toolbar */
        $toolbar = $this->_resultBlock->getToolbarBlock();

        $collection = $this->_getProductCollection();

        $orders = $this->_resultBlock->getAvailableOrders();
        if ($orders) {
            $toolbar->setAvailableOrders($orders);
        }

        $request = $this->_request;

        $sort = $request->getParam('order') ?: $this->_resultBlock->getDefaultSortBy();
        if ($sort) {
            $toolbar->setDefaultOrder($sort);
        }
        $dir = $request->getParam('dir') ?: $this->_resultBlock->getDefaultDirection();
        if ($dir) {
            $toolbar->setDefaultDirection($dir);
        }
        $modes = $this->_resultBlock->getModes();
        if ($modes) {
            $toolbar->setModes($modes);
        }
        $limit = $request->getParam('limit');
        if ($limit) {
            $toolbar->setData('_current_limit', $limit);
        }

        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);

        $page = $request->getParam('p');
        $collection->setCurPage($page);
        $collection->load();

        $this->_setProductCollection($collection);

        $this->_toolbar = $toolbar;
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getProductCollectionWithReview()
    {
        $coll = clone $this->_getProductCollection();
        $coll->clear();
        $coll->addAttributeToSelect('*');

        try {
            $this->reviewSummary->appendSummaryFieldsToCollection(
                $coll,
                $this->_storeManager->getStore()->getId() . '',
                \Magento\Review\Model\Review::ENTITY_PRODUCT_CODE
            );
        } catch (\Exception $e) {
        }

        return $coll;
    }
}
