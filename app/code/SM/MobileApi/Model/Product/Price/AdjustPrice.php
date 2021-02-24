<?php

namespace SM\MobileApi\Model\Product\Price;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class AdjustPrice
 * @package SM\MobileApi\Model\Product\Price
 */
class AdjustPrice
{

    /**
     * @var \SM\MobileApi\Helper\Price
     */
    protected $priceHelper;

    /**
     * @var \SM\MobileApi\Helper\Price\TierPrice
     */
    protected $mTierPriceHelper;

    /**
     * @var \SM\MobileApi\Model\Data\Catalog\Product\TierPriceFactory
     */
    protected $tierPriceFactory;

    /**
     * @var Type\Grouped
     */
    protected $groupedProductPrice;

    /**
     * @var Type\Bundle
     */
    protected $bundleProductPrice;

    /**
     * @var Type\Configurable
     */
    protected $configurableProductPrice;

    /**
     * Price constructor.
     * @param \SM\MobileApi\Helper\Price $mPriceHelper
     * @param \SM\MobileApi\Helper\Price\TierPrice $mTierPriceHelper
     * @param \SM\MobileApi\Model\Data\Catalog\Product\TierPriceFactory $tierPriceFactory
     * @param Type\Grouped $groupedProductPrice
     * @param Type\Configurable $configurableProductPrice
     * @param Type\Bundle $bundleProductPrice
     */
    public function __construct(
        \SM\MobileApi\Helper\Price $mPriceHelper,
        \SM\MobileApi\Helper\Price\TierPrice $mTierPriceHelper,
        \SM\MobileApi\Model\Data\Catalog\Product\TierPriceFactory $tierPriceFactory,
        \SM\MobileApi\Model\Product\Price\Type\Grouped $groupedProductPrice,
        \SM\MobileApi\Model\Product\Price\Type\Configurable $configurableProductPrice,
        \SM\MobileApi\Model\Product\Price\Type\Bundle $bundleProductPrice
    ) {
        $this->priceHelper              = $mPriceHelper;
        $this->mTierPriceHelper         = $mTierPriceHelper;
        $this->tierPriceFactory         = $tierPriceFactory;
        $this->groupedProductPrice      = $groupedProductPrice;
        $this->configurableProductPrice = $configurableProductPrice;
        $this->bundleProductPrice       = $bundleProductPrice;
    }

    /**
     * Set product prices
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \SM\MobileApi\Api\Data\Product\ProductDetailsInterface|\SM\MobileApi\Api\Data\Product\ListItemInterface $productInfo
     *
     * @return \SM\MobileApi\Api\Data\Product\ProductDetailsInterface
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function execute($productInfo, $product)
    {
        if (!$product) {
            return $productInfo;
        }

        // Load price data with tax
        $prices = $product->getPriceInfo()->getPrices();

        /** @var \Magento\Catalog\Pricing\Price\FinalPrice $finalPriceModel */
        $finalPriceModel = $prices->get(\Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE);

        // Check if grouped product do not has any child product
        if (method_exists($finalPriceModel, 'getMinProduct')) {
            $groupedMinProduct = $finalPriceModel->getMinProduct();
            if (!$groupedMinProduct) {
                return $productInfo;
            }
        }

        //Tier prices
        //$productInfo->setTierPrice($this->getProductTierPrice($product));

        //Set base price, final price, minimal price depends on product type
        $productType = $product->getTypeId();

        switch ($productType) {
            case \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE:
                return $this->configurableProductPrice->setPrices($productInfo, $product);
            case \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE:
                return $this->groupedProductPrice->setPrices($productInfo, $product);
            case \Magento\Bundle\Model\Product\Type::TYPE_CODE:
                return $this->bundleProductPrice->setPrices($productInfo, $product);
            default:
                //Final price
                $productInfo->setFinalPrice($this->formatPrice($finalPriceModel->getAmount()->getValue()));

                //Base price
                $regularPrice = $prices->get(\Magento\Catalog\Pricing\Price\RegularPrice::PRICE_CODE)->getAmount()->getValue();
                $productInfo->setPrice($this->formatPrice($regularPrice));

                //Min, max prices
                $productInfo->setMinPrice($this->formatPrice($product->getMinimalPrice()));
                $productInfo->setMaxPrice($this->formatPrice($product->getPrice()));

                return $productInfo;
        }
    }

    /**
     * Format price to store currency
     *
     * @param float $price
     *
     * @return float
     * @throws NoSuchEntityException
     */
    public function formatPrice($price)
    {
        return $this->priceHelper->formatPrice($price);
    }

    /**
     * Get Tier price data for product
     *
     * @param $product
     *
     * @return array
     */
    public function getProductTierPrice($product)
    {
        $data = [];

        $prices = $this->mTierPriceHelper->getTierPrices($product);
        foreach ($prices as $price) {
            $tierPrice = $this->tierPriceFactory->create();
            $tierPrice->setPrice($price['price']);
            $tierPrice->setQty($price['qty']);
            $tierPrice->setSavePercent($price['save_percent']);

            $data[] = $tierPrice;
        }

        return $data;
    }
}
