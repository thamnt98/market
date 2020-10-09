<?php


namespace SM\CustomPrice\Plugin\Pricing\Price;


use SM\CustomPrice\Helper\ProductCollection;

class FinalPrice
{
    /**
     * @var ProductCollection
     */
    protected $productHelper;

    public function __construct(ProductCollection $productCollection)
    {
        $this->productHelper = $productCollection;
    }

    public function afterGetValue(\Magento\Catalog\Pricing\Price\FinalPrice $subject, $result)
    {
        $product = $subject->getProduct();
        return $this->productHelper->getPromoPrice($product, $result);
    }

    public function afterGetBasePrice(\Magento\Catalog\Pricing\Price\FinalPrice $subject, $result)
    {
        $product = $subject->getProduct();
        return $this->productHelper->getBasePrice($product, $result);
    }
}
