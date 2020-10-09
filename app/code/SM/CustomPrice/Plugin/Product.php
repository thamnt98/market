<?php


namespace SM\CustomPrice\Plugin;


use SM\CustomPrice\Helper\ProductCollection;
use SM\CustomPrice\Model\Session\Customer;

class Product
{

    /**
     * @var ProductCollection
     */
    protected $productHelper;

    public function __construct(
        ProductCollection $productCollection
    ) {
        $this->productHelper = $productCollection;
    }

    public function afterGetPrice(\Magento\Catalog\Model\Product $subject, $result)
    {
        if ($subject->getTypeId()== \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            return $result;
        }
        return $this->productHelper->getBasePrice($subject, $result);
    }

    public function afterGetFinalPrice(\Magento\Catalog\Model\Product $subject, $result)
    {
        if ($subject->getTypeId()==\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            return $result;
        }
        return $this->productHelper->getPromoPrice($subject, $result);
    }

    public function afterGetSpecialPrice(\Magento\Catalog\Model\Product $subject, $result)
    {
        if ($subject->getTypeId()==\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            return $result;
        }
        return $this->productHelper->getPromoPrice($subject, $result);
    }

    public function afterGetMaximalPrice(\Magento\Catalog\Model\Product $subject, $result)
    {
        if ($subject->getTypeId()==\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            return $result;
        }
        return $this->productHelper->getBasePrice($subject, $result);
    }
   public function afterGetMaxPrice(\Magento\Catalog\Model\Product $subject, $result)
    {
        if ($subject->getTypeId()==\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            return $result;
        }
        return $this->productHelper->getBasePrice($subject, $result);
    }

    public function afterGetMinimalPrice(\Magento\Catalog\Model\Product $subject, $result)
    {
        return $this->productHelper->getPromoPrice($subject, $result);
    }
    public function afterGetCalculatedFinalPrice(\Magento\Catalog\Model\Product $subject, $result)
    {
        if ($subject->getTypeId()==\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            return $result;
        }
        return $this->productHelper->getPromoPrice($subject, $result);
    }

}
