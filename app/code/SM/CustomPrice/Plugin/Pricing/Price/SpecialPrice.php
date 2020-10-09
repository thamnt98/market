<?php


namespace SM\CustomPrice\Plugin\Pricing\Price;


use SM\CustomPrice\Helper\ProductCollection;

class SpecialPrice
{
    /**
     * @var ProductCollection
     */
    protected $productHelper;

    public function __construct(ProductCollection $productCollection)
    {
        $this->productHelper = $productCollection;
    }

    public function afterGetSpecialPrice(\Magento\Catalog\Pricing\Price\SpecialPrice $subject,$result)
    {
        $product = $subject->getProduct();
        return $this->productHelper->getPromoPrice($product, $result);
    }
}
