<?php

namespace SM\Category\Model\Catalog;

/**
 * Class Category
 * @package SM\Category\Model\Catalog
 */
class Category extends \Magento\Catalog\Block\Product\ListProduct
{
    protected $_toolbar;
    protected $productToolbar;
    protected $productToolbarOrder;
    protected $productFactory;
    protected $productV2Factory;
    protected $productMediaFactory;
    protected $productModel;
    protected $mProductHelper;
    protected $_filterList;
    protected $productFilterFactory;
    protected $productFilterItemFactory;
    protected $_categoryRepository;
    protected $_objectManager;
    protected $_catalogSession;
    protected $_productRepository;
    protected $_productDetailsFactory;
    protected $customerSession;
    protected $commonHelper;
    protected $categoryFactory;
    protected $storeManager;
    protected $currencyFactory;
    protected $settingHelper;
    protected $productCollectionFactory;
    protected $productCollectionStockCondition;
    protected $productStock;

    /**
     * Category constructor.
     * @param \Amasty\Shopby\Helper\FilterSetting $settingHelper
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Magento\Framework\ObjectManagerInterface $objectManagerInterface
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Catalog\Model\Product $productModel
     * @param \SM\MobileApi\Model\Catalog\Layer\FilterListFactory $filterListFactory
     * @param \Magento\Catalog\Model\Session $catalogSession
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface
     * @param \SM\MobileApi\Helper\Product $japiProductHelper
     * @param \SM\MobileApi\Model\Data\Catalog\ProductToolbarFactory $productToolbar
     * @param \SM\MobileApi\Model\Data\Catalog\ProductToolbarOrderFactory $productToolbarOrder
     * @param \SM\MobileApi\Model\Data\Catalog\ProductFactory $productFactory
     * @param \SM\MobileApi\Model\Data\Product\ListItemFactory $listItemFactory
     * @param \SM\MobileApi\Model\Data\Catalog\Product\ProductMediaFactory $productMediaFactory
     * @param \SM\MobileApi\Model\Data\Catalog\ProductFilterFactory $productFilterFactory
     * @param \SM\MobileApi\Model\Data\Catalog\ProductFilterItemFactory $productFilterItemFactory
     * @param \SM\MobileApi\Model\Data\Product\ProductDetailsFactory $productDetailsFactory
     * @param \SM\MobileApi\Helper\Common $commonHelper
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\CatalogInventory\Model\ProductCollectionStockCondition $productCollectionStockCondition
     * @param \SM\MobileApi\Model\Product\Stock $productStock
     * @param array $data
     */
    public function __construct(
        \Amasty\Shopby\Helper\FilterSetting $settingHelper,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Framework\ObjectManagerInterface $objectManagerInterface,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Model\Product $productModel,
        \SM\MobileApi\Model\Catalog\Layer\FilterListFactory $filterListFactory,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
        \SM\MobileApi\Helper\Product $japiProductHelper,
        \SM\MobileApi\Model\Data\Catalog\ProductToolbarFactory $productToolbar,
        \SM\MobileApi\Model\Data\Catalog\ProductToolbarOrderFactory $productToolbarOrder,
        \SM\MobileApi\Model\Data\Catalog\ProductFactory $productFactory,
        \SM\MobileApi\Model\Data\Product\ListItemFactory $listItemFactory,
        \SM\MobileApi\Model\Data\Catalog\Product\ProductMediaFactory $productMediaFactory,
        \SM\MobileApi\Model\Data\Catalog\ProductFilterFactory $productFilterFactory,
        \SM\MobileApi\Model\Data\Catalog\ProductFilterItemFactory $productFilterItemFactory,
        \SM\MobileApi\Model\Data\Product\ProductDetailsFactory $productDetailsFactory,
        \SM\MobileApi\Helper\Common $commonHelper,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\CatalogInventory\Model\ProductCollectionStockCondition $productCollectionStockCondition,
        \SM\MobileApi\Model\Product\Stock $productStock,
        array $data = []
    ) {
        $this->currencyFactory = $currencyFactory;
        $this->storeManager = $storeManager;
        $this->categoryFactory = $categoryFactory;
        $this->productToolbar = $productToolbar;
        $this->productToolbarOrder = $productToolbarOrder;
        $this->productFactory = $productFactory;
        $this->productV2Factory = $listItemFactory;
        $this->productMediaFactory = $productMediaFactory;
        $this->productModel = $productModel;
        $this->mProductHelper = $japiProductHelper;
        $this->_filterList = $filterListFactory->create([], false);
        $this->productFilterFactory = $productFilterFactory;
        $this->productFilterItemFactory = $productFilterItemFactory;
        $this->_categoryRepository = $categoryRepository;
        $this->_objectManager = $objectManagerInterface;
        $this->_catalogSession = $catalogSession;
        $this->_productRepository = $productRepositoryInterface;
        $this->_productDetailsFactory = $productDetailsFactory;
        $this->customerSession = $customerSession;
        $this->commonHelper = $commonHelper;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productCollectionStockCondition = $productCollectionStockCondition;
        $this->productStock = $productStock;

        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);
        $this->settingHelper = $settingHelper;
    }

    public function init($categoryId)
    {
        /**
         * Init category
         */
        $this->_initCategory($categoryId);
    }

    public function applyFilter()
    {
        /**
         * Apply filters
         */
        foreach ($this->_getFilters() as $filter) {
            $filter->apply($this->_request);
        }

        /**
         * Apply layer
         */
        $this->_catalogLayer->apply();
    }

    /**
     * Get Toolbar information
     * @return \SM\MobileApi\Api\Data\Catalog\ProductToolbarInterface
     */
    public function getToolbarInfo()
    {
        $this->_setToolbarData();

        /** @var \Magento\Catalog\Block\Product\ProductList\Toolbar $blockToolbar */
        $blockToolbar = $this->_toolbar;

        /** @var \SM\MobileApi\Api\Data\Catalog\ProductToolbarInterface $result */
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
     * Get Assigned Products V2
     */
    public function getProductsV2()
    {
        return $this->mProductHelper->convertProductCollectionToResponseV2($this->_getProductCollection());
    }

    /**
     * Get product details V2
     *
     * @param $productId
     *
     * @return null|\SM\MobileApi\Api\Data\Product\ProductDetailsInterface
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function getProductV2($productId)
    {
        return $this->mProductHelper->convertProductDetailsToResponseV2($productId);
    }

    /**
     * Get product details V2
     *
     * @param $sku
     *
     * @return null|\SM\MobileApi\Api\Data\Product\ProductDetailsInterface
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function getProductV2BySKU($sku)
    {
        return $this->mProductHelper->convertProductDetailsToResponseV2(-1, $sku);
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFilters()
    {
        $filters = [];

        /** @var \Magento\Catalog\Model\Layer\Filter\Category $filter */
        foreach ($this->_getFilters() as $filter) {
            $filterInfo = $this->productFilterFactory->create();
            $items      = [];
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

            $filterInfo->setIsMultiselect($this->getFilterSetting($filter)->isMultiselect());

            $filterInfo->setItems($items);

            $filters[] = $filterInfo;
        }

        return $filters;
    }

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\FilterInterface $filter
     * @return \Amasty\ShopbyBase\Api\Data\FilterSettingInterface
     */
    protected function getFilterSetting($filter)
    {
        return $this->settingHelper->getSettingByLayerFilter($filter);
    }

    /**
     * Get filter data
     *
     * @return array|\Magento\Catalog\Model\Layer\Filter\AbstractFilter[]
     */
    protected function _getFilters()
    {
        return $this->_filterList->getFilters($this->_catalogLayer);
    }

    /**
     * Set data for Toolbar
     */
    protected function _setToolbarData()
    {
        $toolbar = $this->getToolbarBlock();

        // called prepare sortable parameters
        $collection = $this->_getProductCollection();

        $this->mProductHelper->setDataToolbarByParams($this, $toolbar);

        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);
        if (!$collection->isLoaded()) {
            $collection->load();
        }

        $this->_eventManager->dispatch(
            'catalog_block_product_list_collection',
            ['collection' => $this->_getProductCollection()]
        );

        $this->_toolbar = $toolbar;
    }

    /**
     * Initialize requested category object
     *
     * @param $categoryId
     *
     * @return \Magento\Catalog\Model\Category
     * @throws \Magento\Framework\Webapi\Exception
     */
    protected function _initCategory($categoryId)
    {
        if (!$categoryId) {
            throw new \Magento\Framework\Webapi\Exception(
                __('Category not found.'),
                0,
                \Magento\Framework\Webapi\Exception::HTTP_BAD_REQUEST
            );
        }

        try {
            /* @var $category \Magento\Catalog\Model\Category */
            $category = $this->_categoryRepository->get($categoryId, $this->_storeManager->getStore()->getId());
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            throw new \Magento\Framework\Webapi\Exception(
                __('Category not found.'),
                0,
                \Magento\Framework\Webapi\Exception::HTTP_INTERNAL_ERROR
            );
        }

        $this->_catalogSession->setLastVisitedCategoryId($category->getId());
        $this->_catalogSession->setLastViewedCategoryId($category->getId());
        $this->_coreRegistry->register('current_category', $category);

        try {
            $this->_eventManager->dispatch(
                'catalog_controller_category_init_after',
                ['category' => $category, 'controller_action' => $this]
            );
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            throw new \Magento\Framework\Webapi\Exception(
                __($e->getLogMessage()),
                0,
                \Magento\Framework\Webapi\Exception::HTTP_INTERNAL_ERROR
            );
        }

        return $category;
    }

    /**
     * Retrieve loaded category collection
     *
     * @return AbstractCollection
     */
    protected function _getProductCollection()
    {
        $magentoVersion = $this->commonHelper->getMagentoVersion();
        if (version_compare($magentoVersion, '2.2.0') > 0) {
            return $this->getProductCollection22x();
        } else {
            return $this->getProductCollectionUnder22x();
        }
    }

    /**
     * Retrieve loaded category collection
     *
     * @return AbstractCollection
     */
    private function getProductCollectionUnder22x()
    {
        if ($this->_productCollection === null) {
            $layer = $this->getLayer();
            /* @var $layer \Magento\Catalog\Model\Layer */
            if ($this->getShowRootCategory()) {
                $this->setCategoryId($this->_storeManager->getStore()->getRootCategoryId());
            }

            // if this is a product view page
            if ($this->_coreRegistry->registry('product')) {
                // get collection of categories this product is associated with
                $categories = $this->_coreRegistry->registry('product')
                    ->getCategoryCollection()->setPage(1, 1)
                    ->load();
                // if the product is associated with any category
                if ($categories->count()) {
                    // show products from this category
                    $this->setCategoryId(current($categories->getIterator()));
                }
            }

            $origCategory = null;
            if ($this->getCategoryId()) {
                try {
                    $category = $this->categoryRepository->get($this->getCategoryId());
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    $category = null;
                }

                if ($category) {
                    $origCategory = $layer->getCurrentCategory();
                    $layer->setCurrentCategory($category);
                }
            }
            $this->_productCollection = $layer->getProductCollection();

            $this->prepareSortableFieldsByCategory($layer->getCurrentCategory());

            if ($origCategory) {
                $layer->setCurrentCategory($origCategory);
            }
        }

        return $this->_productCollection;
    }

    /**
     * Retrieve loaded category collection
     *
     * @return AbstractCollection
     */
    private function getProductCollection22x()
    {
        if ($this->_productCollection === null) {
            $this->_productCollection = $this->initializeProductCollection();
        }

        return $this->_productCollection;
    }

    /**
     * Configures product collection from a layer and returns its instance.
     *
     * Also in the scope of a product collection configuration, this method initiates configuration of Toolbar.
     * The reason to do this is because we have a bunch of legacy code
     * where Toolbar configures several options of a collection and therefore this block depends on the Toolbar.
     *
     * This dependency leads to a situation where Toolbar sometimes called to configure a product collection,
     * and sometimes not.
     *
     * To unify this behavior and prevent potential bugs this dependency is explicitly called
     * when product collection initialized.
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function initializeProductCollection()
    {
        $layer = $this->getLayer();
        /* @var $layer \Magento\Catalog\Model\Layer */
        if ($this->getShowRootCategory()) {
            $this->setCategoryId($this->_storeManager->getStore()->getRootCategoryId());
        }

        // if this is a product view page
        if ($this->_coreRegistry->registry('product')) {
            // get collection of categories this product is associated with
            $categories = $this->_coreRegistry->registry('product')
                ->getCategoryCollection()->setPage(1, 1)
                ->load();
            // if the product is associated with any category
            if ($categories->count()) {
                // show products from this category
                $this->setCategoryId(current($categories->getIterator())->getId());
            }
        }

        $origCategory = null;
        if ($this->getCategoryId()) {
            try {
                $category = $this->categoryRepository->get($this->getCategoryId());
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $category = null;
            }

            if ($category) {
                $origCategory = $layer->getCurrentCategory();
                $layer->setCurrentCategory($category);
            }
        }
        $collection = $layer->getProductCollection();
        //Remove product is tobacco
        $collection->addAttributeToFilter([
            ["attribute" => "is_tobacco", "null" => true],
            ["attribute" => "is_tobacco", "eq" => 0]]);
        $this->productCollectionStockCondition->apply($collection);

        $this->prepareSortableFieldsByCategory($layer->getCurrentCategory());

        if ($origCategory) {
            $layer->setCurrentCategory($origCategory);
        }

        $toolbar = $this->getToolbarBlock();
        $this->configureToolbar($toolbar, $collection);

        $this->_eventManager->dispatch(
            'catalog_block_product_list_collection',
            ['collection' => $collection]
        );

        return $collection;
    }

    /**
     * Configures the Toolbar block with options from this block and configured product collection.
     *
     * The purpose of this method is the one-way sharing of different sorting related data
     * between this block, which is responsible for product list rendering,
     * and the Toolbar block, whose responsibility is a rendering of these options.
     *
     * @param \Magento\Catalog\Block\Product\ProductList\Toolbar $toolbar
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     *
     * @return void
     */
    private function configureToolbar($toolbar, $collection)
    {
        // use sortable parameters
        $orders = $this->getAvailableOrders();
        if ($orders) {
            $toolbar->setAvailableOrders($orders);
        }
        $sort = $this->getSortBy();
        if ($sort) {
            $toolbar->setDefaultOrder($sort);
        }
        $dir = $this->getDefaultDirection();
        if ($dir) {
            $toolbar->setDefaultDirection($dir);
        }
        $modes = $this->getModes();
        if ($modes) {
            $toolbar->setModes($modes);
        }
        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);

        //don't set child here
        //$this->setChild('toolbar', $toolbar);
    }

    /**
     * @param $categoryId
     * @return int|void
     */
    public function getTotalProductByCategoryId($categoryId)
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addCategoriesFilter(['in' => $categoryId]);
        $collection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
        $collection->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);

        return count($collection->getItems());
    }
}
