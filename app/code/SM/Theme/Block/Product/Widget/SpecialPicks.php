<?php
/**
 * Class SpecialPicks
 * @package SM\Theme\Block\Product\ListProduct
 * @author Son Nguyen <sonnn@smartosc.com>
 */

declare(strict_types=1);

namespace SM\Theme\Block\Product\Widget;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\View\LayoutFactory;
use Magento\Review\Model\RatingFactory as RatingFactory;
use SM\Catalog\Block\Product\ProductList\Item\AddTo\Iteminfo;
use SM\Catalog\Helper\Data as CatalogHelper;
use Zend_Json_Encoder;

class SpecialPicks extends \Magento\CatalogWidget\Block\Product\ProductsList
{
    /**
     * Default value for products count that will be shown
     */
    const DEFAULT_PRODUCTS_COUNT = 3;

    /**
     * Default value for products per page
     */
    const DEFAULT_PRODUCTS_PER_PAGE = 3;

    /**
     * @var Iteminfo
     */
    public $itemInfo;

    /**
     * @var RatingFactory
     */
    private $rating;
    /**
     * @var CatalogHelper
     */
    private $catalogHelper;

    /**
     * SpecialPicks constructor.
     * @param CatalogHelper $catalogHelper
     * @param Iteminfo $itemInfo
     * @param RatingFactory $rating
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder
     * @param \Magento\CatalogWidget\Model\Rule $rule
     * @param \Magento\Widget\Helper\Conditions $conditionsHelper
     * @param array $data
     * @param Json|NULL $json
     * @param LayoutFactory|NULL $layoutFactory
     * @param EncoderInterface|NULL $urlEncoder
     */
    public function __construct(
        CatalogHelper $catalogHelper,
        Iteminfo $itemInfo,
        RatingFactory $rating,
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder,
        \Magento\CatalogWidget\Model\Rule $rule,
        \Magento\Widget\Helper\Conditions $conditionsHelper,
        array $data = [],
        Json $json = null,
        LayoutFactory $layoutFactory = null,
        EncoderInterface $urlEncoder = null
    ) {
        $this->itemInfo = $itemInfo;
        $this->rating = $rating;
        parent::__construct(
            $context,
            $productCollectionFactory,
            $catalogProductVisibility,
            $httpContext,
            $sqlBuilder,
            $rule,
            $conditionsHelper,
            $data,
            $json,
            $layoutFactory,
            $urlEncoder
        );
        $this->catalogHelper = $catalogHelper;
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
     * @param $product
     * @return array
     */
    /**
     * @param $product
     * @return array
     */
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
        return $RatingOb->getSum() ? ($RatingOb->getSum()/$RatingOb->getCount() ? (($RatingOb->getSum()/$RatingOb->getCount())/20) . " Stars" : "Not available") : "Not available";
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
        $priceGTM = $this->getPriceGTM($product);
        $initPrice = $this->getGTMInitialProductPrice($product);
        $price = $priceGTM['sale_price'] != 'Not in sale' ? $priceGTM['sale_price'] : $initPrice;
        return Zend_Json_Encoder::encode([
            "product_size" => $this->getGTMProductSize($product),
            "product_volume" => $this->getGTMProductVolume($product),
            "product_weight" => $this->getGTMProductWeight($product),
            "salePrice" => $initPrice - $price,
            "discountRate" => $priceGTM['discount_rate'],
            "rating" => $this->getGTMProductRating($productBase),
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
        $_productCollection = $this->getProductCollection();
        return $_productCollection->addAttributeToSelect('weight')->addAttributeToSelect('color');
    }

    /**
     * @param $product
     * @return float|null
     */
    public function getDiscountPercent($product)
    {
        return $this->itemInfo->getDiscountPercent($product);
    }
}
