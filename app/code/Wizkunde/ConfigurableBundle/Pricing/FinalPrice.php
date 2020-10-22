<?php

namespace Wizkunde\ConfigurableBundle\Pricing;

use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\Adjustment\CalculatorInterface;
use Magento\Bundle\Pricing\Price\FinalPrice as BundleFinalPrice;

class FinalPrice extends BundleFinalPrice
{
    protected $amountFactory;

    /**
     * @param Product $saleableItem
     * @param float $quantity
     * @param CalculatorInterface $calculator
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Pricing\Amount\AmountFactory $amountFactory
     */
    public function __construct(
        Product $saleableItem,
        $quantity,
        CalculatorInterface $calculator,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Pricing\Amount\AmountFactory $amountFactory
    ) {
        parent::__construct($saleableItem, $quantity, $calculator, $priceCurrency);

        $this->amountFactory = $amountFactory;
    }

    /**
     * Get price value, ensure its not lower than the minimal price
     *
     * @return float
     */
    public function getValue()
    {
        return max(
            parent::getValue(),
            $this->product->getData('min_price')
        );
    }

    /**
     * get bundle product price without any option
     *
     * @return \Magento\Framework\Pricing\Amount\AmountInterface
     */
    public function getPriceWithoutOption()
    {
        $optionData = parent::getPriceWithoutOption();

        if($optionData->getValue() < $this->getValue()) {
            return $this->amountFactory->create($this->getValue());
        }

        return $optionData;
    }
}
