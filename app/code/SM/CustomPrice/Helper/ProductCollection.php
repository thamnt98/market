<?php

namespace SM\CustomPrice\Helper;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;

/**
 * Class ProductCollection
 * @package SM\CustomPrice\Helper
 */
class ProductCollection
{
    /**
     * @param Collection $productCollection
     */
    protected $promoPriceCode;
    protected $basePriceCode;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    public function __construct(
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_customerSession = $customerSession;
        if ($this->_customerSession->isLoggedIn()||$this->_customerSession->isLoggedInByAPI()) {
            $this->basePriceCode  = $this->_customerSession->getOmniNormalPriceAttributeCode();
            $this->promoPriceCode = $this->_customerSession->getOmniFinalPriceAttributeCode();
        }
    }

    public function addCustomPriceToProductCollection(
        Collection $productCollection
    ) {
        if (!empty($this->basePriceCode)) {
            $productCollection->addAttributeToSelect($this->basePriceCode);
        }
        if (!empty($this->promoPriceCode)) {
            $productCollection->addAttributeToSelect($this->promoPriceCode);
        }

    }

    /**
     * @param $product
     * @param $originPrice
     * @return mixed
     */
    public function getBasePrice($product, $originPrice)
    {
        $price = $this->getCustomPrice($product, $this->basePriceCode);
        if (!empty((int)$price)) {
            return $price;
        }
        return $originPrice;
    }

    /**
     * @param $product
     * @param $originPrice
     * @return mixed
     */
    public function getPromoPrice($product, $originPrice)
    {
        $price=$this->getCustomPrice($product, $this->promoPriceCode);
        if (!empty((int)$price)) {
            return $price;
        }
        return $this->getBasePrice($product, $originPrice);
    }

    /**
     * @param $product
     * @param $code
     * @return mixed
     */
    public function getCustomPrice($product, $code)
    {
        if (!empty($code)) {
            $price = $product->getData($code);
            return $price;
        }
        return 0;
    }
}
