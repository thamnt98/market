<?php

namespace SM\MobileApi\Helper;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;

class Product extends \Magento\Framework\App\Helper\AbstractHelper
{
    const UNDEFINED_ID                 = -1;
    const CONFIG_TOPIC_SHOW_TAB_RETURN = 'sm_help/main_page/topic_show_tab';

    /**
     * Option type percent
     */
    const TYPE_PERCENT         = 'percent';
    const PRODUCT_CONFIGURABLE = 'configurable';
    const PRODUCT_GROUPED      = 'grouped';
    const PRODUCT_BUNDLE       = 'bundle';

    protected $_supportProductType = ['simple', 'configurable', 'bundle', 'grouped'];

    protected $catalogHelper;
    protected $optionFactory;
    protected $optionValueFactory;
    protected $_storeManager;
    protected $_taxConfig;
    protected $productListV2Factory;
    protected $japiRestRequest;
    protected $productCollectionFactory;
    protected $productDetailsV2Factory;
    protected $reviewOverviewFactory;
    protected $japiReviewHelper;
    protected $registry;
    protected $outputHelper;
    protected $filterProvider;
    protected $appState;
    protected $productRepository;
    protected $catalogConfig;
    protected $appEmulation;
    protected $magentoReview;
    protected $configurableHelper;
    protected $helperOutput;
    protected $specificationFactory;
    protected $helperCommon;
    protected $helperInstallation;
    protected $installationFactory;
    protected $helperGrouped;
    protected $helperBundle;
    protected $productPreparator;
    protected $catalogProduct;
    protected $productLabelHelper;
    protected $sourceItemRepository;
    protected $searchCriteriaBuilder;
    protected $bundleProductLinkManagement;
    protected $productFlashSale;
    protected $productGtm;
    protected $gtmFactory;
    protected $productHelperImage;
    protected $stock;
    protected $topicRepository;
    protected $deliveryReturnFactory;
    protected $ruleCollFact;
    protected $ruleValidateHelper;
    protected $tokenUserContext;
    protected $customer;
    protected $quote;
    protected $adjustPrice;

    public function __construct(
        \SM\GTM\Block\Product\ListProduct $productGtm,
        \Magento\InventoryApi\Api\SourceItemRepositoryInterface $sourceItemRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Bundle\Api\ProductLinkManagementInterface $bundleProductLinkManagement,
        \Amasty\Label\Model\LabelViewer $productLabelHelper,
        \Magento\Catalog\Helper\Product $catalogProduct,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Helper\Data $catalogHelper,
        \Magento\Catalog\Helper\Output $outputHelper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Tax\Model\Config $taxConfig,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\Webapi\Rest\Request $mRestRequest,
        \SM\MobileApi\Model\Data\Catalog\Product\OptionFactory $optionFactory,
        \SM\MobileApi\Model\Data\Catalog\Product\Option\ValueFactory $optionValueFactory,
        \SM\MobileApi\Model\Data\Product\ListItemFactory $productListV2Factory,
        \SM\MobileApi\Model\Data\Product\ProductDetailsFactory $productDetailsV2Factory,
        \Magento\Store\Model\App\Emulation $appEmulation,
        \SM\MobileApi\Helper\Review $reviewHelper,
        \SM\MobileApi\Model\Data\Catalog\Product\ReviewFactory $reviewOverviewFactory,
        \Magento\Review\Model\Review $magentoReview,
        \SM\MobileApi\Helper\Configurable $configurableHelper,
        \Magento\Catalog\Helper\Output $helperOutput,
        \SM\MobileApi\Model\Data\Catalog\Product\SpecificationFactory $specificationFactory,
        \SM\MobileApi\Helper\Product\Common $helperCommon,
        \SM\Installation\Helper\Data $helperInstallation,
        \SM\MobileApi\Model\Data\ProductInstallation\InstallationFactory $installationFactory,
        \SM\MobileApi\Helper\GroupedProduct $helperGrouped,
        \SM\MobileApi\Helper\BundleProduct $helperBundle,
        \SM\Search\Model\Search\Suggestion\Product\Preparator $productPreparator,
        \SM\MobileApi\Model\Product\FlashSale $productFlashSale,
        \SM\MobileApi\Model\Data\GTM\GTMFactory $gtmFactory,
        \SM\MobileApi\Model\Product\Image $productHelperImage,
        \SM\MobileApi\Model\Product\Stock $productStock,
        \SM\Help\Api\TopicRepositoryInterface $topicRepository,
        \SM\MobileApi\Model\Data\Product\DeliveryReturnFactory $deliveryReturnFactory,
        \SM\Promotion\Helper\Validation $ruleValidateHelper,
        \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $ruleCollFact,
        \Magento\Webapi\Model\Authorization\TokenUserContext $tokenUserContext,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Quote\Model\QuoteManagement $quote,
        \SM\MobileApi\Model\Product\Price\AdjustPrice $adjustPrice
    ) {
        $this->productGtm = $productGtm;
        $this->optionFactory = $optionFactory;
        $this->optionValueFactory = $optionValueFactory;
        $this->catalogHelper = $catalogHelper;
        $this->_storeManager = $storeManager;
        $this->_taxConfig = $taxConfig;
        $this->productListV2Factory = $productListV2Factory;
        $this->productDetailsV2Factory = $productDetailsV2Factory;
        $this->japiRestRequest = $mRestRequest;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->registry = $registry;
        $this->outputHelper = $outputHelper;
        $this->filterProvider = $filterProvider;
        $this->appState = $appState;
        $this->productRepository = $productRepository;
        $this->catalogConfig = $catalogConfig;
        $this->appEmulation = $appEmulation;
        $this->japiReviewHelper = $reviewHelper;
        $this->reviewOverviewFactory = $reviewOverviewFactory;
        $this->magentoReview = $magentoReview;
        $this->configurableHelper = $configurableHelper;
        $this->helperOutput = $helperOutput;
        $this->specificationFactory = $specificationFactory;
        $this->helperCommon = $helperCommon;
        $this->helperInstallation = $helperInstallation;
        $this->installationFactory = $installationFactory;
        $this->catalogProduct = $catalogProduct;
        $this->productLabelHelper = $productLabelHelper;
        $this->helperGrouped = $helperGrouped;
        $this->helperBundle = $helperBundle;
        $this->productPreparator = $productPreparator;
        $this->productFlashSale = $productFlashSale;
        $this->sourceItemRepository = $sourceItemRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->bundleProductLinkManagement = $bundleProductLinkManagement;
        $this->gtmFactory = $gtmFactory;
        $this->productHelperImage = $productHelperImage;
        $this->stock = $productStock;
        $this->topicRepository = $topicRepository;
        $this->deliveryReturnFactory = $deliveryReturnFactory;
        $this->ruleCollFact = $ruleCollFact;
        $this->ruleValidateHelper = $ruleValidateHelper;
        $this->tokenUserContext = $tokenUserContext;
        $this->customer = $customer;
        $this->quote = $quote;
        $this->adjustPrice = $adjustPrice;
        parent::__construct($context);
    }

    /**
     * Get default product image on listing page
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getMainImage($product)
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $this->appEmulation->startEnvironmentEmulation($storeId, \Magento\Framework\App\Area::AREA_FRONTEND, true);
        $mainImage = $this->productHelperImage->getMainImage($product);
        $this->appEmulation->stopEnvironmentEmulation();

        return $mainImage;
    }

    /**
     * Set data for Toolbar Block by request params
     *
     * @param \SM\Category\Model\Catalog\Category $model
     * @param \Magento\Catalog\Block\Product\ProductList\Toolbar $toolbar
     */
    public function setDataToolbarByParams($model, &$toolbar)
    {
        $request = $this->japiRestRequest;

        // use sortable parameters
        $orders = $model->getAvailableOrders();
        if ($orders) {
            $toolbar->setAvailableOrders($orders);
        }

        $sort = $request->getParam('order') ?: $model->getSortBy();
        if ($sort) {
            $toolbar->setDefaultOrder($sort);
        }

        $dir = $request->getParam('dir');
        if (!$dir) {
            $dir = $this->scopeConfig->getValue('japi/jmango_rest_catalog_settings/default_direction');
        }
        if (!$dir) {
            $dir = $model->getDefaultDirection();
        }
        if ($dir) {
            $toolbar->setDefaultDirection($dir);
        }

        $modes = $model->getModes();
        if ($modes) {
            $toolbar->setModes($modes);
        }

        $limit = $request->getParam('limit');
        if ($limit) {
            $toolbar->setData('_current_limit', $limit);
        }
    }

    /**
     * Get product custom options
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return \SM\MobileApi\Api\Data\Catalog\Product\OptionInterface[]
     */
    public function getCustomOptions($product)
    {
        if (!$product || !$product->getId()) {
            return null;
        }
        if (!$product->getOptions()) {
            return null;
        }

        $options = [];
        foreach ($product->getOptions() as $option) {
            /* @var $option \Magento\Catalog\Model\Product\Option */
            /* @var $optionData \SM\MobileApi\Api\Data\Catalog\Product\OptionInterface */
            $optionData = $this->optionFactory->create();
            $optionData->setOptionId($option->getOptionId());
            $optionData->setType($option->getType());
            $optionData->setTitle($option->getTitle());
            $optionData->setSortOrder($option->getSortOrder());
            $optionData->setIsRequire($option->getIsRequire());

            if ($option->getValues()) {
                $values = [];
                foreach ($option->getValues() as $value) {
                    /* @var $value \Magento\Catalog\Model\Product\Option\Value */
                    /* @var $valueData \SM\MobileApi\Api\Data\Catalog\Product\Option\ValueInterface */
                    $valueData = $this->optionValueFactory->create();
                    $valueData->setValueId($value->getId());
                    $valueData->setTitle($value->getTitle());
                    $valueData->setPrice($this->_getProductOptionValuePrice($product, $value));
                    $valueData->setPriceType($value->getPriceType());
                    $valueData->setSortOrder($value->getSortOrder());
                    $valueData->setSku($value->getSku());

                    $values[] = $valueData;
                }
                $optionData->setAdditionalFields($values);
            }

            $options[] = $optionData;
        }

        return $options;
    }

    /**
     * Process product price with Tax
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param float $price
     *
     * @return float
     * @throws NoSuchEntityException
     */
    public function getPriceWithTax($product, $price)
    {
        $includeTax = true;
        $store = $this->_storeManager->getStore();
        $_configDisplayTax = $this->_taxConfig->getPriceDisplayType($store);
        if ($_configDisplayTax == \Magento\Tax\Model\Config::DISPLAY_TYPE_EXCLUDING_TAX) {
            $includeTax = false;
        }

        return $this->catalogHelper->getTaxPrice($product, $price, $includeTax);
    }

    /**
     * Return product list V2 from product ids array
     *
     * @param array $productIds
     *
     * @return array
     */
    public function convertProductIdsToResponseV2($productIds)
    {
        if (!$productIds && !count($productIds)) {
            return null;
        }

        $productCollection = $this->productCollectionFactory->create();
        $productCollection
            ->addAttributeToSelect($this->catalogConfig->getProductAttributes())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addIdFilter($productIds);

        /**
         * Keep item order in collection follow array order
         */
        $productCollection->getSelect()->order(new \Zend_Db_Expr('FIELD(e.entity_id, ' . implode(
            ',',
            $productIds
        ) . ')'));

        return $this->convertProductCollectionToResponseV2($productCollection);
    }

    /**
     * Return product list V2
     * @param $collection
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function convertProductCollectionToResponseV2($collection)
    {
        $data = [];

        //select all attributes
        $collection
            ->addAttributeToSelect('*');

        foreach ($collection as $product) {
            if ($product->getData("is_tobacco")) {
                continue;
            }
            /* @var $product \Magento\Catalog\Model\Product */
            $productInfo = $this->getProductListToResponseV2($product);
            $data[] = $productInfo;
        }

        return $data;
    }

    /**
     * Return product details V2
     *
     * @param int $productId
     * @param string $sku
     *
     * @return \SM\MobileApi\Api\Data\Product\ProductDetailsInterface|null
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function convertProductDetailsToResponseV2($productId = -1, $sku = null)
    {
        if (self:: UNDEFINED_ID == $productId && null == $sku) {
            return null;
        } else {
            if (!$productId || !is_numeric($productId)) {
                return null;
            }
        }

        $productCollection = $this->productCollectionFactory->create();
        $productCollection
            ->addAttributeToSelect($this->catalogConfig->getProductAttributes());

        $productCollection->addTaxPercents();

        if (self:: UNDEFINED_ID == $productId) {
            //get by sku
            $productCollection->addAttributeToFilter('sku', $sku);
        } else {
            //get by id
            $productCollection->addAttributeToFilter('entity_id', $productId);
        }

        /**
         * Load review, should be put at last
         */
        //        $this->addReviewData($productCollection);

        $products = [];
        foreach ($productCollection as $product) {
            /** @var \Magento\Catalog\Model\Product $product */
            //Using $product->load() for get more information
            $product->load($product->getId());

            if (!in_array($product->getTypeId(), $this->_supportProductType)) {
                throw new \Magento\Framework\Webapi\Exception(
                    __('Product not supported.'),
                    0,
                    \Magento\Framework\Webapi\Exception::HTTP_INTERNAL_ERROR
                );
            }

            $productInfo = $this->getProductDetailsToResponseV2($product);
            $products[] = $productInfo;
            break;
        }

        return count($products) ? $products[0] : null;
    }

    /**
     * Return product data in list for API response
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return  \SM\MobileApi\Api\Data\Product\ListItemInterface
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Json_Exception
     */
    public function getProductListToResponseV2($product)
    {
        if (!$product || !$product->getId()) {
            return null;
        }
        /** @var \SM\MobileApi\Api\Data\Product\ListItemInterface $productInfo */
        $productInfo = $this->productListV2Factory->create();
        $productRepository = $this->productRepository->get($product->getSku());
        $isShowCategoryNames = $this->registry->registry(\SM\MobileApi\Model\Product\Search::SHOW_CATEGORY_NAMES);
        $categoryNames = $isShowCategoryNames ? $this->productPreparator->prepareCategoryNames($product) : [];

        $productInfo->setId($product->getId());
        $productInfo->setName($product->getName());
        $productInfo->setSku($product->getSku());
        $productInfo->setType($product->getTypeId());
        $productInfo->setTypeId($product->getTypeId());
        $productInfo->setProductUrl($this->_getProductUrl($product));
        $productInfo->setCategoryNames($categoryNames);
        $productInfo->setStock($this->_getProductStockQty($product));
        $productInfo->setIsSaleable($productRepository->isSalable());
        $productInfo->setIsInStock((boolean)$productInfo->getStock());
        $productInfo->setIsAvailable($product->isAvailable());
        $this->adjustPrice->execute($productInfo, $product);
        $productInfo->setDescription($product->getDescription());
        $productInfo->setShortDescription($product->getShortDescription());
        $productInfo->setImage($this->getMainImage($product));
        $productInfo->setConfigChildCount($this->getConfigChildCount($product));

        $productInfo->setGtmData($this->getGTMData($product, $productInfo));

        /**
         * Product label PLP: Limit offer, Sale, etc...
         */
        /** @var \SM\MobileApi\Api\Data\ProductLabel\ProductLabelInterface $label */
        $labels = $this->helperCommon->getProductLabel($product);
        $productInfo->setProductLabel($labels ? $labels : null);

        /**
         * Review data
         */
        $review_enable = $this->_getReviewEnable($product);
        $productInfo->setReviewEnable($review_enable);
        $productInfo->setReview($this->_getReviewSummary($review_enable, $product));

        /**
         * Check need price calculation
         */
        $productInfo->setRequiredPriceCalculation(false);

        /**
         * Set flash sale data
         */
        $productInfo->setIsFlashsale($product->getData('is_flashsale'));
        $productInfo->setFlashSaleQty($product->getData('flashsale_qty'));
        $productInfo->setFlashSaleQtyPerCustomer($product->getData('flashsale_qty_per_customer'));
        $productInfo->setFlashSaleQtyAvailable($this->productFlashSale->getFlashSaleAvailableQty($product));

        $inCart = $this->checkInCart($product);

        if ($inCart != false) {
            $productInfo->setItemId($inCart['item_id']);
            $productInfo->setItemQty($inCart['item_qty']);
        }

        return $productInfo;
    }

    /**
     * @param $product
     * @param \SM\MobileApi\Api\Data\Product\ListItemInterface $productInfo
     * @return \SM\MobileApi\Model\Data\GTM\GTM
     * @throws NoSuchEntityException
     * @throws \Zend_Json_Exception
     */
    protected function getGTMData($product, $productInfo)
    {
        $product = $this->productRepository->getById($product->getId());
        $model = $this->gtmFactory->create();
        $data = $this->productGtm->getGtm($product);
        $data = \Zend_Json_Decoder::decode($data);
        $model->setProductName($data['name']);
        $model->setProductId($data['id']);
        $model->setProductPrice($data['price']);
        $model->setProductBrand($data['brand']);
        $model->setProductCategory($data['category']);
        $model->setProductSize($data['product_size']);
        $model->setProductVolume($data['product_volume']);
        $model->setProductWeight($data['product_weight']);
        $model->setProductVariant($data['variant']);
        $model->setDiscountPrice($data['salePrice']);
        $model->setProductList($data['list']);
        $model->setInitialPrice($data['initialPrice']);
        $model->setDiscountRate($data['discountRate']);
        $model->setProductRating($data['rating']);
        if ($product->getTypeId() == "bundle") {
            $model->setProductPrice($productInfo->getFinalPrice());
            $model->setDiscountPrice($productInfo->getPrice() - $productInfo->getFinalPrice());
            $model->setInitialPrice($productInfo->getPrice());
            $discount = $productInfo->getPrice() - $productInfo->getFinalPrice();

            if ($discount != 0) {
                $discount = round(($discount * 100) / $productInfo->getPrice()) . '%';
            }
            $model->setDiscountRate($discount);
        } else {
            $model->setDiscountPrice($productInfo->getPrice() - $productInfo->getFinalPrice());
        }

        if ($data['salePrice'] && $data['salePrice'] > 0) {
            $model->setProductOnSale(__('Yes'));
        } else {
            $model->setProductOnSale(__('Not on sale'));
        }

        if ($productInfo->getFinalPrice() < $productInfo->getPrice()) {
            $model->setProductOnSale(__('Yes'));
        } else {
            $model->setProductOnSale(__('Not on sale'));
        }

        return $model;
    }

    public function checkInCart($product)
    {
        if ($product->getTypeId() == "simple") {
            $data = [];
            $customerId = $this->tokenUserContext->getUserId();
            if ($customerId) {
                $quote = $this->quote->getCartForCustomer($customerId);
                foreach ($quote->getItemsCollection() as $item) {
                    if ($item->getParentItemId()) {
                        continue;
                    }

                    if ($item->getProduct()->getId() == $product->getId()) {
                        $data = [
                            "item_id"  => $item->getId(),
                            "item_qty" => $item->getQty()
                        ];
                        break;
                    }
                }
            }

            if (empty($data)) {
                return false;
            } else {
                return $data;
            }
        } else {
            return false;
        }
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return int|null
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function getConfigChildCount($product)
    {
        $result = 0;
        $skipSaleableCheck = $this->catalogProduct->getSkipSaleableCheck();
        switch ($product->getTypeId()) {
            case self::PRODUCT_CONFIGURABLE:
                $childProducts = $product->getTypeInstance()->getUsedProducts($product, null);
                break;
            case self::PRODUCT_GROUPED:
                $childProducts = $product->getTypeInstance()->getAssociatedProducts($product);
                break;
            case self::PRODUCT_BUNDLE:
                $childProducts = $this->bundleProductLinkManagement->getChildren($product->getSku());
                break;
        }
        if (isset($childProducts)) {
            if ($product->getTypeId() === self::PRODUCT_BUNDLE) {
                foreach ($childProducts as &$childProduct) {
                    $childProduct = $this->productRepository->get($childProduct->getSku());
                }
            }
            foreach ($childProducts as $item) {
                $result += ($item->isSaleable() || $skipSaleableCheck);
            }
            return $result;
        }

        return null;
    }

    /**
     * Return product data in details for API response
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return \SM\MobileApi\Api\Data\Product\ProductDetailsInterface|null
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getProductDetailsToResponseV2($product)
    {
        if (!$product || !$product->getId()) {
            return null;
        }

        $this->registry->register('current_product', $product, true);
        $this->registry->register('product', $product, true);

        $productInfo = $this->productDetailsV2Factory->create();

        $productInfo->setId($product->getId());
        $productInfo->setName($product->getName());
        $productInfo->setSku($product->getSku());
        $productInfo->setType($product->getTypeId());
        $productInfo->setTypeId($product->getTypeId());
        $productInfo->setStock($this->_getProductStockQty($product));
        $productInfo->setIsSaleable($product->getIsSalable());
        $productInfo->setIsInStock((boolean)$productInfo->getStock());
        $productInfo->setIsAvailable($product->isAvailable());
        $productInfo->setImage($this->getMainImage($product));
        $productInfo->setCssDescriptionMobi($this->getCssMobile($product));
        $productInfo->setMediaUrls($this->productHelperImage->getMediaUrlsData($product));
        $productInfo->setGallery($this->productHelperImage->getGalleryInfo($product, $productInfo->getImage()));
        $productInfo->setProductUrl($this->_getProductUrl($product));
        $productInfo->setDeliveryInto($this->helperCommon->getDeliveryMethodProduct($product));
        $productInfo->setStoresInfo($this->helperCommon->getStoreInfo($product));
        $productInfo->setSpecifications($this->getSpecification($product));
        //$productInfo->setDeliveryReturn($this->getDeliveryReturn($product));

        /**
         * Check need price calculation
         */
        $productInfo->setRequiredPriceCalculation($this->checkRequireReload($product));
        $productInfo = $this->adjustPrice->execute($productInfo, $product);
        $productInfo->setDescription($this->_getProductDescription($product));
        $productInfo->setShortDescription($this->_getProductShortDescription($product));

        /**
         * Load configurable data
         */
        $productInfo->setConfigurableAttributes($this->configurableHelper->getConfigurableAttributes($product));

        /**
         * Load bundle data
         */
        $productInfo->setBundleItems($this->helperBundle->getBundleProductItems($product));

        /**
         * Load grouped data
         */
        $productInfo->setGroupedItems($this->helperGrouped->getGroupedItems($product));

        /**
         * Load custom options
         */
        $productInfo->setOptions($this->getCustomOptions($product));

        /**
         * Review data
         */
        $review_enable = $this->_getReviewEnable($product);
        $productInfo->setReviewEnable($review_enable);
        $productInfo->setReview($this->_getReviewSummary($review_enable, $product));
        $productInfo->setProductLabel($this->helperCommon->getProductLabel($product));
        $productInfo->setInstallation($this->getInstallation($product));

        $productInfo->setCouponLabel($this->getRuleLabel($product));
        $productInfo->setCouponTooltip($this->getRuleToolTip($product));
        return $productInfo;
    }

    public function getProductRule($product)
    {
        $rule = null;
        $customerId = $this->tokenUserContext->getUserId();

        try {
            $customer = $this->customer->load($customerId);
            $customerGroup = $customer->getGroupId();
        } catch (\Exception $e) {
            $customerGroup = \Magento\Customer\Model\Group::NOT_LOGGED_IN_ID;
        }

        /** @var \Magento\SalesRule\Model\ResourceModel\Rule\Collection $coll */
        $coll = $this->ruleCollFact->create();
        $coll->addIsActiveFilter()
            ->addCustomerGroupFilter($customerGroup)
            ->addFieldToFilter('coupon_type', \Magento\SalesRule\Model\Rule::COUPON_TYPE_NO_COUPON)
            ->addOrder(
                \Magento\SalesRule\Model\Data\Rule::KEY_SORT_ORDER,
                \Magento\SalesRule\Model\ResourceModel\Rule\Collection::SORT_ORDER_ASC
            );

        /** @var \Magento\SalesRule\Model\Rule $item */
        foreach ($coll as $item) {
            if ($this->ruleValidateHelper->validateApiProduct($item, $product, $customerId)) {
                $rule = $item;
                break;
            }
        }

        return $rule;
    }

    public function getRuleLabel($product)
    {
        $promo = $this->getProductRule($product);
        if ($promo) {
            return $promo->getName();
        }

        return '';
    }

    public function getRuleToolTip($product)
    {
        $promo = $this->getProductRule($product);
        if ($promo) {
            return $promo->getData('term_condition');
        }

        return '';
    }

    /**
     * Get Css of Product
     *
     * @param $product
     * @return string
     */
    protected function getCssMobile($product)
    {
        $attCode = \SM\MobileApi\Api\Data\Product\ProductDetailsInterface::CSS_DESCRIPTION_MOBI;
        if ($product->getCustomAttribute($attCode) != null) {
            return $product->getCustomAttribute($attCode)->getValue();
        }
        return '';
    }

    /**
     * Get is review enabled
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return bool
     */
    protected function _getReviewEnable($product)
    {
        return $this->japiReviewHelper->isReviewEnable($product);
    }

    /**
     * @param $review_enable
     * @param $product
     * @return \SM\MobileApi\Model\Data\Catalog\Product\Review|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _getReviewSummary($review_enable, $product)
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $this->magentoReview->getEntitySummary($product, $storeId);
        if ($review_enable) {
            $reviewSummary = $product->getRatingSummary();
            if ($reviewSummary) {
                $reviewOverview = $this->reviewOverviewFactory->create();
                $reviewOverview->setReviewCounter($reviewSummary->getReviewsCount());
                $reviewOverview->setPercent($reviewSummary->getRatingSummary());
                return $reviewOverview;
            }

            return null;
        } else {
            return null;
        }
    }

    /**
     * Get product URL with store view code
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return string|null
     */
    protected function _getProductUrl($product)
    {
        if (!$product || !$product->getId()) {
            return null;
        }

        return $product->getUrlModel()->getUrl(
            $product,
            ['_query' => ['___store' => $this->_storeManager->getStore()->getCode()]]
        );
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return int
     */
    protected function _getProductStockQty($product)
    {
        return $this->stock->getStock($product);
    }

    /**
     * Append review data to collection
     *
     * @param Collection $collection
     *
     * @return Collection
     */
    public function addReviewData($collection)
    {
        return $this->japiReviewHelper->addReviewData($collection);
    }

    /**
     * Check product need to call api to reload price or not
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return bool
     */
    protected function checkRequireReload($product)
    {
        if ($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            return true;
        }

        return false;
    }

    /**
     * Get product short desciption
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return string
     */
    protected function _getProductShortDescription($product)
    {
        return $this->_getProductAttribute($product, 'short_description');
    }

    /**
     * Get product desciption
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return string
     */
    protected function _getProductDescription($product)
    {
        return $this->_getProductAttribute($product, 'description');
    }

    /**
     * Get product attribute value, useful for "description", "short_description" and other WYSIWYG attributes
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $attributeName
     *
     * @return string
     */
    protected function _getProductAttribute($product, $attributeName)
    {
        return $this->outputHelper->productAttribute(
            $product,
            $this->appState->emulateAreaCode('frontend', [
                $this,
                'getFilteredValue'
            ], [$product->getData($attributeName)]),
            $attributeName
        );
    }

    /**
     * Filter value
     *
     * @param string $value
     *
     * @return string
     */
    public function getFilteredValue($value)
    {
        return $this->filterProvider->getBlockFilter()->filter($value);
    }

    /**
     * Get product option price
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Model\Product\Option\Value $optionValue
     *
     * @return float
     */
    protected function _getProductOptionValuePrice($product, $optionValue)
    {
        if ($optionValue->getPriceType() == self::TYPE_PERCENT) {
            $prices = $product->getPriceInfo()->getPrices();

            /** @var \Magento\Catalog\Pricing\Price\FinalPrice $finalPriceModel */
            $finalPriceModel = $prices->get(\Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE);

            //Final price
            $finalPrice = $finalPriceModel->getAmount()->getValue();

            $price = $finalPrice * ($optionValue->getPrice() / 100);

            return $price;
        }

        return $optionValue->getPrice();
    }

    /**
     * @param $collection
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function parseProductsResponse($collection)
    {
        $data = [];
        foreach ($collection as $product) {
            $productInfo = $this->getProductListToResponseV2($product);
            $data[] = $productInfo;
        }
        return $data;
    }

    /**
     * Get specification of product
     *
     * @param $product
     * @return mixed
     * @throws LocalizedException
     */
    public function getSpecification($product)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productAttributes = $objectManager->get('\Magento\Catalog\Block\Product\View\Attributes');
        $specification = $productAttributes->getAdditionalData();
        $data = [];
        foreach ($specification as $key => $value) {
            $specticationFactory = $this->specificationFactory->create();
            $specticationFactory->setLabel($value['label']);
            if ($this->isWeightAttribute($value["code"])) {
                $specticationFactory->setValue(
                    $this->formatWeight($value['value'])
                );
            } else {
                $specticationFactory->setValue(
                    $this->helperOutput->productAttribute($product, $value['value'], $value['code'])
                );
            }

            $specticationFactory->setCode($value["code"]);
            $data[] = $specticationFactory;
        }
        return $data;
    }

    /**
     * @param $attributeCode
     * @return bool
     */
    public function isWeightAttribute($attributeCode)
    {
        return $attributeCode == "weight";
    }

    /**
     * @param $value
     * @return string
     */
    public function formatWeight($value)
    {
        return ((float) $value) . " " . $this->getWeightUnit();
    }


    /**
     * Get installation of product
     *
     * @param $product
     * @return \SM\MobileApi\Model\Data\ProductInstallation\Installation
     */
    public function getInstallation($product)
    {
        $installation = $this->installationFactory->create();
        if ($this->helperInstallation->isEnabled() && $product->getIsService()) {
            $installation->setStatus(1);
        } else {
            $installation->setStatus(0);
        }
        $installation->setTooltip($this->helperInstallation->getTooltip());
        return $installation;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     * @throws LocalizedException
     */
    private function getDeliveryReturn(\Magento\Catalog\Model\Product $product)
    {
        $topicIds = $this->scopeConfig->getValue(
            self::CONFIG_TOPIC_SHOW_TAB_RETURN,
            ScopeInterface::SCOPE_STORE
        );
        $deliveryReturnData = [];

        if ($topicIds) {
            $topicIds = explode(',', $topicIds);

            foreach ($topicIds as $id) {
                $topic = $this->topicRepository->getById($id);
                $deliveryReturn = $this->deliveryReturnFactory->create();
                $deliveryReturn->setTopicName($topic->getName());
                $deliveryReturn->setChildQuestions($this->topicRepository->getChildQuestions($topic->getId()));
                $deliveryReturnData[] = $deliveryReturn;
            }
        }

        return $deliveryReturnData;
    }

    public function getWeightUnit()
    {
        return $this->scopeConfig->getValue(
            'general/locale/weight_unit',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
