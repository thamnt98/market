<?php

namespace SM\GTM\Block\Product;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\ListProduct as ListProductBase;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Url\Helper\Data;
use Magento\Review\Model\RatingFactory as RatingFactory;
use SM\Catalog\Helper\Data as CatalogHelper;
use Zend_Json_Encoder;

class ListProduct extends ListProductBase
{
    /**
     * @var RatingFactory
     */
    private $rating;
    /**
     * @var CatalogHelper
     */
    private $catalogHelper;

    /**
     * ListProduct constructor.
     * @param CatalogHelper $catalogHelper
     * @param RatingFactory $rating
     * @param Context $context
     * @param PostHelper $postDataHelper
     * @param Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Data $urlHelper
     * @param array $data
     */
    public function __construct(
        CatalogHelper $catalogHelper,
        RatingFactory $rating,
        Context $context,
        PostHelper $postDataHelper,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        Data $urlHelper,
        array $data = []
    ) {
        $this->rating = $rating;
        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);
        $this->catalogHelper = $catalogHelper;
    }

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
     * @return string
     */
    protected function getBrandAttributeCode()
    {
        return $this->_scopeConfig->getValue(
            'amshopby_brand/general/attribute_code',
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
        return $RatingOb->getSum()
            ? ($RatingOb->getSum() / $RatingOb->getCount()
            ? (($RatingOb->getSum() / $RatingOb->getCount()) / 20) . " Stars" : "Not available") : "Not available";
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
        return $product->getAttributeText($this->getBrandAttributeCode())
            ? $product->getAttributeText($this->getBrandAttributeCode()) : "Not available";
    }

    /**
     * @param $product
     * @return string
     */
    public function getGTMProductSize($product)
    {
        if (
            !$product->getData('product_length') ||
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
}
