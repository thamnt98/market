<?php

namespace SM\MobileApi\Model\Product\Price\Type;

/**
 * Class Configurable
 * @package SM\MobileApi\Model\Product\Price\Type
 */
class Configurable
{
    /**
     * @var \SM\Catalog\Helper\Data
     */
    protected $helperPrice;

    /**
     * @var \SM\MobileApi\Helper\Price
     */
    protected $mPriceHelper;

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
        $this->mPriceHelper = $mPriceHelper;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param \SM\MobileApi\Api\Data\Product\ProductDetailsInterface|\SM\MobileApi\Api\Data\Product\ListItemInterface $productInfo
     * @return \SM\MobileApi\Api\Data\Product\ProductDetailsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setPrices($productInfo, $product)
    {
        $priceModel = $this->helperPrice->getMinConfigurable($product);

        //Get final price & base price
        $finalPrice = $priceModel->getFinalPrice();
        $basePrice  = $priceModel->getPrice();

        $productInfo->setFinalPrice($this->mPriceHelper->formatPrice($finalPrice));
        $productInfo->setPrice($this->mPriceHelper->formatPrice($basePrice));

        return $productInfo;
    }
}
