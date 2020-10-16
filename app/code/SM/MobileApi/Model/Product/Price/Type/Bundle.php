<?php

namespace SM\MobileApi\Model\Product\Price\Type;

/**
 * Class Bundle
 * @package SM\MobileApi\Model\Product\Price\Type
 */
class Bundle
{
    /**
     * @var \SM\Catalog\Helper\Data
     */
    protected $helperPrice;

    /**
     * @var \SM\MobileApi\Helper\Price
     */
    protected $priceHelper;

    /**
     * @var \SM\Bundle\Helper\BundleAttribute
     */
    protected $bundleAttribute;

    /**
     * Price constructor.
     * @param \SM\Catalog\Helper\Data $helperPrice
     * @param \SM\MobileApi\Helper\Price $mPriceHelper
     * @param \SM\Bundle\Helper\BundleAttribute $bundleAttribute
     */
    public function __construct(
        \SM\Catalog\Helper\Data $helperPrice,
        \SM\MobileApi\Helper\Price $mPriceHelper,
        \SM\Bundle\Helper\BundleAttribute $bundleAttribute
    ) {
        $this->helperPrice = $helperPrice;
        $this->priceHelper = $mPriceHelper;
        $this->bundleAttribute  = $bundleAttribute;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param \SM\MobileApi\Api\Data\Product\ProductDetailsInterface|\SM\MobileApi\Api\Data\Product\ListItemInterface $productInfo
     *
     * @return \SM\MobileApi\Api\Data\Product\ProductDetailsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function setPrices($productInfo, $product)
    {
        $regularPrice         = 0;
        $finalPrice           = 0;
        $completePackagePrice = $this->bundleAttribute->getMinPrice($product, true);
        $bundlePrice          = $product->getPriceInfo()->getPrice(\Magento\Catalog\Pricing\Price\RegularPrice::PRICE_CODE);
        $showRange            = $bundlePrice->getMinimalPrice() != $bundlePrice->getMaximalPrice();

        if (isset($completePackagePrice)) {
            $regularPrice = $this->bundleAttribute->getMinAmount($product, true, true, true);
            $finalPrice   = $completePackagePrice->getValue();
        } else {
            if (!$showRange) {
                //Check the custom options, if any
                /** @var \Magento\Catalog\Pricing\Price\CustomOptionPrice $customOptionPrice */
                $customOptionPrice = $product->getPriceInfo()->getPrice(\Magento\Catalog\Pricing\Price\CustomOptionPrice::PRICE_CODE);
                $showRange         = $customOptionPrice->getCustomOptionRange(true) != $customOptionPrice->getCustomOptionRange(false);
            }
            if ($showRange) {
                $regularPrice = $product->getPriceInfo()->getPrice(
                    \Magento\Catalog\Pricing\Price\RegularPrice::PRICE_CODE
                )->getAmount()->getValue();
                $finalPrice   = $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();
            }
        }

        $productInfo->setFinalPrice($this->priceHelper->formatPrice($finalPrice));
        $productInfo->setPrice($this->priceHelper->formatPrice($regularPrice));

        return $productInfo;
    }
}
