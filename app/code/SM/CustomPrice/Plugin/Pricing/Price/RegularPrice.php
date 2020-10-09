<?php


namespace SM\CustomPrice\Plugin\Pricing\Price;


use SM\CustomPrice\Helper\ProductCollection;

class RegularPrice
{
    /**
     * @var ProductCollection
     */
    protected $productHelper;

    public function __construct(ProductCollection $productCollection)
    {
        $this->productHelper = $productCollection;
    }

    public function afterGetValue(\Magento\Catalog\Pricing\Price\RegularPrice $subject,$result)
    {
        $product = $subject->getProduct();
        return $this->productHelper->getBasePrice($product, $result);
    }
}
