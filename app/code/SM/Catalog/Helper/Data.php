<?php

/**
 * @category  SM
 * @package   SM_Catalog
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author    Logan Ta <phuongtt2@smartosc.com>
 *
 * @copyright 2020 Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

declare(strict_types=1);

namespace SM\Catalog\Helper;

use SM\Bundle\Helper\BundleAttribute as BundleHelper;

/**
 * Class Data
 *
 * @package SM\Catalog\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const PRODUCT_DETAIL_PAGE = 'catalog_product_view';
    const VALUE_YES = 1;
    const VALUE_NO = 0;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollFact;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $httpRequest;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $_stockRegistry;

    /**
     * @var \Magento\Review\Model\ReviewFactory
     */
    protected $reviewFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var BundleHelper
     */
    protected $bundleHelper;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\App\Request\Http $httpRequest,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollFact,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        BundleHelper $bundleHelper,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        parent::__construct($context);
        $this->productCollFact = $productCollFact;
        $this->httpRequest = $httpRequest;
        $this->productRepository = $productRepository;
        $this->_stockRegistry = $stockRegistry;
        $this->reviewFactory = $reviewFactory;
        $this->storeManager = $storeManager;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->bundleHelper = $bundleHelper;
    }

    /**
     * @param $config
     *
     * @return mixed
     */
    public function getConfig($config)
    {
        return $this->scopeConfig->getValue(
            $config,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_urlBuilder->getBaseUrl();
    }

    /**
     * @return bool
     */
    public function isProductDetailPage()
    {
        if ($this->httpRequest->getFullActionName() == self::PRODUCT_DETAIL_PAGE) {
            return true;
        }

        return false;
    }


    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return int|null
     */
    public function getDiscountPercent($product)
    {
        switch ($product->getTypeId()) {
            case \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE:
                return $this->getDiscountConfigurable($product);
            case \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE:
                return $this->getDiscountGrouped($product);
            case \Magento\Bundle\Model\Product\Type::TYPE_CODE:
                return $this->getDiscountBundle($product);
            default:
                return $this->getDiscountSingle($product);
        }
    }

    /**
     * @param $product
     *
     * @return \Magento\Catalog\Model\Product|null
     */
    public function getMinProduct($product)
    {
        switch ($product->getTypeId()) {
            case \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE:
                return $this->getMinConfigurable($product);
            case \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE:
                return $this->getMinGrouped($product);
            case \Magento\Bundle\Model\Product\Type::TYPE_CODE:
                return $this->getMinBundle($product);
            default:
                return $product;
        }
    }

    /**
     * @param $product
     *
     * @return \Magento\Catalog\Model\Product|null
     */
    public function getSumMinPriceBundleOptionProduct($product)
    {
        return $this->getSumPriceMinBundle($product);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return int|null
     */
    public function getDiscountSingle($product)
    {
        if (is_null($product) ||
            is_null($product->getFinalPrice()) ||
            $product->getFinalPrice() >= $product->getPrice()
        ) {
            return null;
        }

        return round(
            ($product->getPrice() - $product->getFinalPrice()) * 100 / $product->getPrice()
        );
    }

    /**
     * @param $sumSpecialPriceMin
     * @param $sumPriceMin
     * @return float|null
     */
    public function getDiscountBundleMin($sumSpecialPriceMin, $sumPriceMin)
    {
        if (
            is_null($sumPriceMin) ||
            is_null($sumSpecialPriceMin) ||
            $sumPriceMin <= $sumSpecialPriceMin
        ) {
            return NULL;
        }

        return round(
            ($sumPriceMin - $sumSpecialPriceMin) * 100 / $sumPriceMin
        );
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return \Magento\Catalog\Model\Product|null
     */
    public function getMinConfigurable($product)
    {
        if ($product->getTypeId() !== \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            return null;
        }

        /** @var \Magento\Catalog\Model\Product[] $children */
        $children = $product->getTypeInstance()->getUsedProducts($product);

        return $this->getMinChildren($children);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return int|null
     */
    public function getDiscountConfigurable($product)
    {
        $minProduct = $this->getMinConfigurable($product);
        if ($minProduct) {
            return $this->getDiscountSingle($minProduct);
        }

        return null;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return int|null
     */
    public function getDiscountGrouped($product)
    {
        return $this->getDiscountSingle($this->getMinGrouped($product));
    }

    /**
     * @param $product
     * @return \Magento\Catalog\Model\Product|null
     */
    public function getMinGrouped($product)
    {
        /** @var \Magento\Catalog\Model\Product[] $associatedProducts */
        $associatedProducts = $product->getTypeInstance()->getAssociatedProducts($product);

        if (count($associatedProducts) == 0) {
            return null;
        }

        return $this->getMinChildren($associatedProducts);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return int|null
     */
    public function getDiscountBundle($product)
    {
        return $this->getDiscountBundleMin($this->bundleHelper->getMinAmount($product, true),
            $this->bundleHelper->getMinAmount($product, true, true));
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getMinBundle($product)
    {
        /** @var \Magento\Catalog\Model\Product[] $children */
        $childrenIds = $product->getTypeInstance()->getChildrenIds($product->getId());
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $coll */
        $coll = $this->productCollFact->create();
        $coll->addFieldToFilter('entity_id', $childrenIds);

        return $this->getMinChildren($coll->getItems());
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getSumPriceMinBundle($product)
    {
        /** @var \Magento\Catalog\Model\Product[] $children */
        $childrenIds = $product->getTypeInstance()->getChildrenIds($product->getId(), false);
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $coll */
        $coll = $this->productCollFact->create();
        $coll->addFieldToSelect('type_id');
        $coll->addFieldToSelect('price');
        $coll->addFieldToSelect('special_price');
        $coll->addFieldToFilter('entity_id', $childrenIds);

        return $this->getSumMinPriceChildrenBundle($coll->getItems(), true);
    }

    /**
     * @param \Magento\Catalog\Model\Product[] $children
     *
     * @return \Magento\Catalog\Model\Product|null
     */
    protected function getMinChildren($children)
    {
        if (empty($children)) {
            return null;
        } else {
            $minProduct = null;
            $minPrice = null;

            foreach ($children as $item) {
                if ($item->getTypeId() === \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                    $item = $this->getMinConfigurable($item);
                }

                try {
                    /** @var \Magento\Catalog\Model\Product $item */
                    $item = $this->productRepository->getById($item->getId());
                } catch (\Exception $e) {
                    continue;
                }

                if (is_null($minProduct) || $minPrice > $item->getFinalPrice()) {
                    $minPrice = $item->getFinalPrice();
                    $minProduct = $item;
                }
            }

            return $minProduct;
        }
    }

    /**
     * @param \Magento\Catalog\Model\Product[] $children
     *
     * @return \Magento\Catalog\Model\Product|null
     */
    protected function getSumMinPriceChildrenBundle($children, $require = false)
    {
        if (empty($children)) {
            return NULL;
        } else {
            $sumMinPrice = 0;
            $sumMinPriceRegular = 0;
            $minProduct = NULL;
            $minPrice = NULL;
            foreach ($children as $item) {
                if ($item->getTypeId() === \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                    $item = $this->getMinConfigurable($item);
                }

                if ($require) {
                    $sumMinPrice = $sumMinPrice + floatval($item->getFinalPrice());
                    $sumMinPriceRegular = $sumMinPriceRegular + floatval($item->getPrice());
                } else {
                    if ($sumMinPrice > floatval($item->getFinalPrice())) {
                        $sumMinPrice = floatval($item->getFinalPrice());
                        $sumMinPriceRegular = floatval($item->getPrice());
                    }
                }
            }

            return ['special' => $sumMinPrice, 'regular' => $sumMinPriceRegular];
        }
    }

    /**
     * @param $children
     * @return float|int|null
     */
    protected function getSumMinFinalPriceChildrenBundle($children)
    {
        if (empty($children)) {
            return NULL;
        } else {
            $sumMinPrice = 0;
            $minProduct = NULL;
            $minPrice = NULL;

            foreach ($children as $item) {
                if ($item->getTypeId() === \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                    $item = $this->getMinConfigurable($item);
                    $sumMinPrice = $sumMinPrice + floatval($item->getFinalPrice());
                } else {
                    $sumMinPrice = $sumMinPrice + floatval($item->getFinalPrice());
                }
            }

            return $sumMinPrice;
        }
    }

    /**
     * @param $productId
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isProductStockStatus($productId)
    {
        $product = $this->productRepository->getById($productId);
        if ($product->getTypeId() === \Magento\Bundle\Model\Product\Type::TYPE_CODE) {
            $typeInstance = $product->getTypeInstance();
            $requiredChildrenIds = $typeInstance->getChildrenIds($product->getId(), true);
            $childIdArr = [];
            //get all child of bundle product
            foreach ($requiredChildrenIds as $valCB) {
                if (is_array($valCB)) {
                    foreach ($valCB as $valItem) {
                        $childIdArr[] = $valItem;
                    }
                }
            }

            if (!empty($childIdArr)) {
                foreach ($childIdArr as $childId) {
                    $parent = $this->productRepository->getById($childId);
                    if ($parent->getTypeId() === \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                        //case configurable product
                        $childProductStockStatus = $this->_stockRegistry->getProductStockStatus($childId);
                        if ($childProductStockStatus === self::VALUE_NO) {
                            return self::VALUE_NO;
                        }
                        //case child simple of configurable
                        $childInStock = $parent->getTypeInstance()->getUsedProducts($parent);
                        $childNumber = $parent->getTypeInstance()->getUsedProductCollection($parent);
                        if ($childNumber->getSize() > count($childInStock)) {
                            return self::VALUE_NO;
                        }
                    } else {
                        //case simple product
                        $childProductStockStatus = $this->_stockRegistry->getProductStockStatus($childId);
                        if ($childProductStockStatus === self::VALUE_NO) {
                            return self::VALUE_NO;
                        }
                    }
                }
            }

            return self::VALUE_YES;
        }

        return $this->_stockRegistry->getProductStockStatus($productId);
    }

    /**
     * @param $description
     * @return bool|null|string
     */
    public function convertDescription($description)
    {
        $countDescription = strlen($description);
        $valueAccept = NULL;
        if ($countDescription > 1000) {
            $valueAccept = substr($description, 0, 1000);

            return $valueAccept;
        } else {
            return $description;
        }
    }

    /**
     * @param $productSku
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getRattingSummary($productSku)
    {
        $product = $this->productRepository->get($productSku);
        $reviewModel = $this->reviewFactory->create();
        $storeId = $this->storeManager->getStore()->getStoreId();
        $reviewModel->getEntitySummary($product, $storeId);

        $ratingSummary = $product->getRatingSummary()->getRatingSummary();
        if (is_null($ratingSummary)) {
            $ratingSummary = 1;
        }

        return $ratingSummary;
    }

    /**
     * @param $productSku
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getRattingCount($productSku)
    {
        $product = $this->productRepository->get($productSku);
        $reviewModel = $this->reviewFactory->create();
        $storeId = $this->storeManager->getStore()->getStoreId();
        $reviewModel->getEntitySummary($product, $storeId);

        $ratingSummary = $product->getRatingSummary()->getRatingSummary();
        $reviewCount = $product->getRatingSummary()->getReviewsCount();
        if (is_null($ratingSummary)) {
            $reviewCount = 0;
        }

        return $reviewCount;
    }

    /**
     * @param $product
     * @return bool
     */
    public function isValidation($product)
    {
        $typeValid = [
            \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE,
            \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE,
            \Magento\Bundle\Model\Product\Type::TYPE_CODE
        ];

        if (!$product ||
            !in_array($product->getTypeId(), $typeValid)
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param $product
     * @return int
     */
    public function countChildren($product)
    {
        if (!$this->isValidation($product)) {
            return 0;
        }

        $result = 0;
        $ids = $product->getTypeInstance()->getChildrenIds($product->getId());
        $ids = $this->mergeArray($ids);

        $searchCriteriaBuilder = $this->searchCriteriaBuilder->addFilter('entity_id', $ids, 'in')->create();
        $children = $this->productRepository->getList($searchCriteriaBuilder);

        /** @var \Magento\Catalog\Model\Product $child */
        foreach ($children->getItems() as $child) {
            if (!$child->isSaleable()) {
                continue;
            }

            ++$result;
        }

        return $result;
    }

    /**
     * @param $children
     * @return array
     */
    public function mergeArray($children)
    {
        $result = [];
        if (!is_array($children)) {
            $result[] = $children;
        } else {
            foreach ($children as $child) {
                if (is_array($child)) {
                    $result = array_merge($result, $this->mergeArray($child));
                } else {
                    $result[] = $child;
                }
            }
        }

        return array_unique($result);
    }

    /**
     * @param $type
     * @return string
     */
    public function checkTypeBlock($type)
    {
        if ($type !== 'related-rule' && $type !== 'related' && $this->isProductDetailPage()) {
            return 'notInvisible';
        }

        return $type;
    }
}
