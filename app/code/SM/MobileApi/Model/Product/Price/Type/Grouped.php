<?php

namespace SM\MobileApi\Model\Product\Price\Type;

/**
 * Class Grouped
 * @package SM\MobileApi\Model\Product\Price\Type
 */
class Grouped
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
     * Price constructor.
     * @param \SM\Catalog\Helper\Data $helperPrice
     * @param \SM\MobileApi\Helper\Price $mPriceHelper
     */
    public function __construct(
        \SM\Catalog\Helper\Data $helperPrice,
        \SM\MobileApi\Helper\Price $mPriceHelper
    ) {
        $this->helperPrice = $helperPrice;
        $this->priceHelper = $mPriceHelper;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param \SM\MobileApi\Api\Data\Product\ProductDetailsInterface|\SM\MobileApi\Api\Data\Product\ListItemInterface $productInfo
     * @return \SM\MobileApi\Api\Data\Product\ProductDetailsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function setPrices($productInfo, $product)
    {
        $groupPrice = $this->helperPrice->getMinGrouped($product);

        //get base price and discount price
        $regularPrice = $groupPrice->getPriceInfo()->getPrice(\Magento\Catalog\Pricing\Price\RegularPrice::PRICE_CODE)
            ->getAmount()->getValue();
        $finalPrice = $groupPrice->getPriceInfo()->getPrice(\Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE)
            ->getAmount()->getValue();

        $productInfo->setFinalPrice($this->priceHelper->formatPrice($finalPrice));
        $productInfo->setPrice($this->priceHelper->formatPrice($regularPrice));

        return $productInfo;
    }
}
