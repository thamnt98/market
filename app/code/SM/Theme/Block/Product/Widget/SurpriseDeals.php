<?php
/**
 * @category SM
 * @package SM_Theme
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Chinhvd <chinhvd@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

declare(strict_types=1);

namespace SM\Theme\Block\Product\Widget;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogEvent\Model\Category\EventList;
use Magento\CatalogEvent\Model\Event as SaleEvent;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Magento\CatalogWidget\Model\Rule;
use Magento\Framework\App\Http\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\View\LayoutFactory;
use Magento\Review\Model\RatingFactory as RatingFactory;
use Magento\Review\Model\ReviewFactory;
use Magento\Rule\Model\Condition\Sql\Builder;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Widget\Helper\Conditions;
use SM\Catalog\Block\Product\ProductList\Item\AddTo\Iteminfo;
use SM\Catalog\Helper\Data as CatalogHelper;
use Zend_Json_Encoder;

class SurpriseDeals extends \Magento\CatalogWidget\Block\Product\ProductsList
{
    /**
     * Default value for products count that will be shown
     */
    const DEFAULT_PRODUCTS_COUNT = 5;

    /**
     * Default value for products per page
     */
    const DEFAULT_PRODUCTS_PER_PAGE = 5;

    /**
     * @var StockItemRepository
     */
    protected $_stockItemRepository;

    /**
     * @var Iteminfo
     */
    public $itemInfo;
    /**
     * @var RatingFactory
     */
    private $rating;

    protected $categoryEventList;

    protected $categoryRepository;
    /**
     * @var CatalogHelper
     */
    private $catalogHelper;
    /**
     * @var ReviewFactory
     */
    private $reviewFactory;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * SurpriseDeals constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param CollectionFactory $productCollectionFactory
     * @param Visibility $catalogProductVisibility
     * @param Context $httpContext
     * @param Builder $sqlBuilder
     * @param Rule $rule
     * @param Conditions $conditionsHelper
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Json|null $json
     * @param LayoutFactory|null $layoutFactory
     * @param EncoderInterface|null $urlEncoder
     * @param ProductRepositoryInterface $productRepository
     * @param CatalogHelper $catalogHelper
     * @param RatingFactory $rating
     * @param StockItemRepository $stockItemRepository
     * @param Iteminfo $itemInfo
     * @param EventList $categoryEventList
     * @param ReviewFactory $reviewFactory
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        CollectionFactory $productCollectionFactory,
        Visibility $catalogProductVisibility,
        Context $httpContext,
        Builder $sqlBuilder,
        Rule $rule,
        Conditions $conditionsHelper,
        CategoryRepositoryInterface $categoryRepository,
        Json $json = null,
        LayoutFactory $layoutFactory = null,
        EncoderInterface $urlEncoder = null,
        ProductRepositoryInterface $productRepository,
        CatalogHelper $catalogHelper,
        RatingFactory $rating,
        StockItemRepository $stockItemRepository,
        Iteminfo $itemInfo,
        EventList $categoryEventList,
        ReviewFactory $reviewFactory,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->productRepository = $productRepository;
        $this->rating = $rating;
        $this->_stockItemRepository = $stockItemRepository;
        $this->itemInfo = $itemInfo;
        $this->categoryEventList = $categoryEventList;
        $this->categoryRepository = $categoryRepository;
        $this->catalogHelper = $catalogHelper;
        $this->reviewFactory = $reviewFactory;
        $this->storeManager = $storeManager;
        parent::__construct(
            $context,
            $productCollectionFactory,
            $catalogProductVisibility,
            $httpContext,
            $sqlBuilder,
            $rule,
            $conditionsHelper,
            $categoryRepository,
            $data,
            $json,
            $layoutFactory,
            $urlEncoder
        );
    }

    /**
     * @inheritdoc
     */
    protected function _beforeToHtml()
    {
        $this->setData('products_per_page', self::DEFAULT_PRODUCTS_PER_PAGE);
        $this->setData('products_count', self::DEFAULT_PRODUCTS_COUNT);
        return parent::_beforeToHtml();
    }

    /**
     * Get stock product
     * @param $productId
     * @return float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Exception
     */
    public function getStockItem($productId)
    {
        try {
            $stockItem = $this->_stockItemRepository->get($productId);
            $qty = $stockItem->getQty();
        } catch (NoSuchEntityException $e) {
            $qty = 0;
        }
        return $qty;
    }

    /**
     * Get percent sale
     * @param $price
     * @param $finalPrice
     * @return string
     */
    public function getPercent($price, $finalPrice)
    {
        if ($price > 0) {
            return 100 - round($finalPrice / $price * 100);
        }

        return 100;
    }

    public function getPriceGTM($product)
    {
        $price = ['sale_price' => 'Not in sale', 'discount_rate' => 'Not in sale'];
        $discount = $this->catalogHelper->getDiscountPercent($product);
        if ($discount) {
            $price['sale_price'] = $this->trimNumber($product->getFinalPrice());
            $price['discount_rate'] = $discount . '%';
        }
        return $price;
    }

    /**
     * @return mixed
     */
    public function getWeightUnit()
    {
        return $this->_scopeConfig->getValue(
            'general/locale/weight_unit',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $num
     * @return string
     */
    public function trimNumber($num)
    {
        if (!$num) {
            return "Not available";
        }
        if ($num == 0) {
            return 0;
        }
        $result = rtrim($num, '0');
        $result = ltrim($result, '0');
        $result = rtrim($result, '.');
        return $result;
    }

    /**
     * @param $product
     * @return string
     */

    public function getGTMMinProduct($product)
    {
        switch ($product->getTypeId()) {
            case \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE:
                return $this->catalogHelper->getMinConfigurable($product);
            case \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE:
                return $this->catalogHelper->getMinGrouped($product);
            case \Magento\Bundle\Model\Product\Type::TYPE_CODE:
                return $this->catalogHelper->getMinBundle($product);
            default:
                return $product;
        }
    }

    public function getGTMProductWeight($product)
    {
        return $product->getWeight() ? $this->trimNumber($product->getWeight()) . $this->getWeightUnit() : "Not available";
    }

    /**
     * @param $product
     * @return string
     */
    public function getGTMInitialProductPrice($product)
    {
        return $this->trimNumber($product->getPrice()) ?? 'Not available';
    }

    /**
     * @param $product
     * @return mixed
     */
    public function getGTMProductCategory($product)
    {
        return $product->getCategoryCollection()
            ->addAttributeToSelect('name')
            ->getFirstItem()
            ->getName();
    }

    /**
     * @param $product
     * @return string
     */
    public function getGTMProductVariant($product)
    {
        if ($product->getAttributeText('color') && $product->getAttributeText('product_size')) {
            return $product->getAttributeText('color') . ', ' . $product->getAttributeText('product_size');
        }
        if ($product->getAttributeText('color')) {
            return $product->getAttributeText('color');
        }
        if ($product->getAttributeText('product_size')) {
            return $product->getAttributeText('product_size');
        }
        return "Not available";
    }

    /**
     * @param $product
     * @return float|int|string
     */
    public function getGTMProductRating($product)
    {
        $RatingOb = $this->rating->create()->getEntitySummary($product->getId());
        return $RatingOb->getSum() ? ($RatingOb->getSum() / $RatingOb->getCount() ? (($RatingOb->getSum() / $RatingOb->getCount()) / 20) . " Stars" : "Not available") : "Not available";
    }

    public function getRattingSummary($product)
    {
        $reviewModel = $this->reviewFactory->create();
        $storeId = $this->storeManager->getStore()->getStoreId();
        $reviewModel->getEntitySummary($product, $storeId);

        $ratingSummary = $product->getRatingSummary()->getRatingSummary();
        if (is_null($ratingSummary)) {
            $ratingSummary = 1;
        }
        return $ratingSummary;
    }

    public function getRattingCount($product)
    {
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
     * @return mixed
     */
    public function getGTMProductType($product)
    {
        return $product->getTypeId();
    }

    /**
     * @param $product
     * @return string
     */
    public function getGTMBrand($product)
    {
        return $product->getAttributeText('shop_by_brand') ? $product->getAttributeText('shop_by_brand') : "Not available";
    }

    /**
     * @param $product
     * @return string
     */
    public function getGTMProductSize($product)
    {
        if (!$product->getData('product_length') ||
            !$product->getData('product_height') ||
            !$product->getData('product_width')
        ) {
            return 'Not available';
        }
        return $product->getData('product_length') . 'x' .
            $product->getData('product_width') . 'x' .
            $product->getData('product_height');
    }

    /**
     * @param $product
     * @return string
     */
    public function getGTMProductVolume($product)
    {
        return $product->getData('product_volume') ? $product->getData('product_volume') : 'Not available';
    }

    /**
     * @param $productBase
     * @return string
     */
    public function getGtm($productBase)
    {
        if (!$productBase) {
            return null;
        }
        $product = $this->getGTMMinProduct($productBase);
        if (!$product) {
            $product = $productBase;
        }
        $product = $this->productRepository->getById($product->getId());
        $priceGTM = $this->getPriceGTM($product);
        $initPrice = $this->getGTMInitialProductPrice($product);
        $price = $priceGTM['sale_price'] != 'Not in sale' ? $priceGTM['sale_price'] : $initPrice;
        if (is_numeric($initPrice) && is_numeric($price)) {
            $salePrice = $initPrice - $price;
        } else {
            $salePrice =  'Not in sale';
        }

        return Zend_Json_Encoder::encode([
            "product_size" => $this->getGTMProductSize($product),
            "product_volume" => $this->getGTMProductVolume($product),
            "product_weight" => $this->getGTMProductWeight($product),
            "salePrice" => $salePrice,
            "discountRate" => $priceGTM['discount_rate'],
            "rating" => $this->getGTMProductRating($productBase),
            "type" => $this->getGTMProductType($productBase),
            "initialPrice" => $initPrice,
            "productBundle" => $this->getGTMProductType($productBase) === "bundle" ? "Yes" : "No",
            "list" => $this->getData('title') ?? "Not available",
            "name" => $productBase->getName(),
            "id" => $productBase->getSku(),
            "price" => $price,
            "brand" => $this->getGTMBrand($product),
            "category" => $this->getGTMProductCategory($product),
            "variant" => $this->getGTMProductVariant($product),
            "eventTimeout" => 2000
        ], true);
    }

    /**
     * @return mixed
     */
    public function getGTMProductCollection()
    {
        $event = $this->getEvent();
        $eventCategoryId = $event->getData("category_id");
        $_productCollection = $this->getProductCollection();
        $_productCollection->addAttributeToSelect('*')
            ->addAttributeToFilter('is_flashsale', 1)
            ->addFieldToFilter('flashsale_qty', array('gt' => 0))
            ->addFieldToFilter('flashsale_qty_per_customer', array('gt' => 0))
            ->addCategoriesFilter(["in" => $eventCategoryId])->setPageSize($this::DEFAULT_PRODUCTS_PER_PAGE);

        return $_productCollection->addAttributeToSelect('weight')->addAttributeToSelect('color');
    }

    public function getFlashSaleProductCollection()
    {
        $eventCategory = $this->getEventCategory();
        if ($eventCategory != null) {
            $_productCollection = $eventCategory->getProductCollection();
            $_productCollection->addAttributeToSelect(['name', 'url_key', 'image', 'small_image', 'thumbnail','price','special_price']);
            $_productCollection->addFieldToFilter('is_flashsale', 1);
            $_productCollection->setPageSize($this::DEFAULT_PRODUCTS_PER_PAGE);
            return $_productCollection->addAttributeToSelect('weight')->addAttributeToSelect('color');
        } else {
            return null;
        }
    }

    public function getEvent()
    {
        $event = $this->categoryEventList->getEventCollection()
            ->addFieldToFilter('status', SaleEvent::STATUS_OPEN)->addVisibilityFilter()->getFirstItem();
        return $event;
    }

    public function getEventCategory()
    {
        $event = $this->getEvent();
        $eventCategoryId = $event->getData("category_id");
        if ($eventCategoryId != null) {
            $category = $this->categoryRepository->get($eventCategoryId);
            return $category;
        } else {
            return null;
        }
    }

    public function getEndTimeUTC($event)
    {
        return strtotime($event->getData('date_end'));
    }

    public function getDefaultProductPerPage()
    {
        return $this::DEFAULT_PRODUCTS_PER_PAGE;
    }

    /**
     * @param $product
     * @return float|null
     */
    public function getDiscountPercent($product)
    {
        return $this->itemInfo->getDiscountPercent($product);
    }

    /**
     * @param $product
     * @return bool
     */
    public function isConfigProduct($product)
    {
        return $this->itemInfo->isConfigProduct($product);
    }
}
