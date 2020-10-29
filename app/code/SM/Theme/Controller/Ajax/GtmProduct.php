<?php
namespace SM\Theme\Controller\Ajax;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Review\Model\RatingFactory as RatingFactory;
use SM\Catalog\Helper\Data as CatalogHelper;

class GtmProduct extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var RatingFactory
     */
    protected $rating;
    /**
     * @var CatalogHelper
     */
    private $catalogHelper;
    /**
     * @var ScopeConfigInterface
     */
    private $_scopeConfig;

    /**
     * GtmProduct constructor.
     * @param CatalogHelper $catalogHelper
     * @param RatingFactory $rating
     * @param ScopeConfigInterface $_scopeConfig
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        CatalogHelper $catalogHelper,
        RatingFactory $rating,
        ScopeConfigInterface $_scopeConfig,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->rating = $rating;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->collectionFactory = $collectionFactory;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
        $this->catalogHelper = $catalogHelper;
        $this->_scopeConfig = $_scopeConfig;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $productIds = [];
        foreach ($this->getRequest()->getParams() as $productData) {
            $productIds[$productData['product_id']] = $productData['added_at'];
        }

        arsort($productIds);
        $count = 1;
        foreach ($productIds as $id => &$value) {
            $value = $count;
            $count++;
        }
        $productCollection = $this->collectionFactory->create()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('entity_id', ['in' => array_keys($productIds)]);
        $dataResult = [];
        $data = [
            'list' => 'Special Picks For You'
        ];
        foreach ($productCollection as $product) {
            $productMin = $this->getGTMMinProduct($product);
            if (!$productMin) {
                $productMin = $product;
            }
            $priceGTM = $this->getPriceGTM($productMin);
            $initPrice = $this->getGTMInitialProductPrice($productMin);
            $price =  $priceGTM['sale_price'] != 'Not in sale' ? $priceGTM['sale_price'] : $initPrice;

            $data['product_size'] = $this->getGTMProductSize($product);
            $data['product_volume'] = $this->getGTMProductVolume($product);
            $data['product_weight'] = $this->getGTMProductWeight($productMin);
            $data['salePrice'] = $initPrice - $price;
            $data['discountRate'] = $priceGTM['discount_rate'];
            $data['rating'] = $this->getGTMProductRating($product);
            $data['initialPrice'] = $initPrice;
            $data['name'] = addslashes($product->getName());
            $data['id'] = addslashes($product->getSku());
            $data['price'] = $price;
            $data['brand'] = $this->getGTMBrand($product);
            $data['category'] = $this->getGTMProductCategory($productMin);
            $data['variant'] = $this->getGTMProductVariant($productMin);
            $data['position'] = $productIds[$product->getId()];
            $data['productUrl'] = $product->getProductUrl();
            $data['eventTimeout'] = 2000;
            $dataResult[$product->getId()] = $data;
        }

        $result = $this->resultJsonFactory->create();
        return $result->setData($dataResult);
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

}
