<?php

namespace SM\GTM\Block\Product;

use Magento\Catalog\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Review\Model\RatingFactory as RatingFactory;
use SM\Catalog\Helper\Data as CatalogHelper;
use Zend_Json_Encoder;

class View implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    const PRODUCT_EVENT_CLICK_NAME_NO_REDIRECT = 'product_click_no_redirect';
    const PRODUCT_EVENT_ADD_TO_CART = 'addToCart';
    const PRODUCT_EVENT_SEE_DETAIL_INSPIRE_ME = 'seeDetails_product_inspireMe';
    const SINGLE = 'single';
    const GROUP = 'group';
    /**
     * @var CatalogHelper
     */
    private $catalogHelper;
    /**
     * @var RatingFactory
     */
    private $rating;
    /**
     * @var ScopeConfigInterface
     */
    private $_scopeConfig;
    /**
     * @var Data
     */
    private $catalogData;

    /**
     * View constructor.
     * @param CatalogHelper $catalogHelper
     * @param RatingFactory $rating
     * @param ScopeConfigInterface $_scopeConfig
     * @param Data $catalogData
     */
    public function __construct(
        CatalogHelper $catalogHelper,
        RatingFactory $rating,
        ScopeConfigInterface $_scopeConfig,
        Data $catalogData
    ) {
        $this->catalogHelper = $catalogHelper;
        $this->rating = $rating;
        $this->_scopeConfig = $_scopeConfig;
        $this->catalogData = $catalogData;
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
        return $product->getAttributeText('color') ? $product->getAttributeText('color') : "Not available";
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

    /**
     * @param $product
     * @return mixed
     */
    public function getGTMProductType($product)
    {
        $type = $product->getTypeId();
        switch ($type) {
            case \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE:
                return self::SINGLE;
            case \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE:
                return self::GROUP;
            default:
                return $type;
        }
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
     * @return array
     */
    public function getGTMList()
    {
        $breadcrumbArray = $this->catalogData->getBreadcrumbPath();
        if ($breadcrumbArray) {
            if (is_array($breadcrumbArray)) {
                $valueBreadCrumb = array_values($breadcrumbArray);
                if (array_key_exists(count($breadcrumbArray)-2, $valueBreadCrumb)) {
                    if (array_key_exists('label', $valueBreadCrumb[count($breadcrumbArray)-2])) {
                        return $valueBreadCrumb[count($breadcrumbArray)-2]['label'];
                    }
                }
            }
        }
        return null;
    }

    /**
     * @param $productBase
     * @param null $qty
     * @param null $delivery_option
     * @return string
     */
    public function getGtmData($productBase, $qty = "Not available", $delivery_option = "Not available")
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
            "delivery_option" => $delivery_option,
            "product_size" => $this->getGTMProductSize($product),
            "product_volume" => $this->getGTMProductVolume($product),
            "product_weight" => $this->getGTMProductWeight($product),
            "salePrice" => $initPrice - $price,
            "discountRate" => $priceGTM['discount_rate'],
            "rating" => $this->getGTMProductRating($productBase),
            "type" => $this->getGTMProductType($productBase),
            "initialPrice" => $initPrice,
            "productBundle" => $this->getGTMProductType($productBase) === "bundle" ? "Yes" : "No",
            "list" => $this->getGTMList(),
            "name" => $productBase->getName(),
            "id" => $productBase->getSku(),
            "price" => $price,
            "brand" => $this->getGTMBrand($product),
            "category" => $this->getGTMProductCategory($product),
            "variant" => $this->getGTMProductVariant($product),
            "quantity" => $qty,
            'eventTimeout' => 2000
        ], true);
    }
}
